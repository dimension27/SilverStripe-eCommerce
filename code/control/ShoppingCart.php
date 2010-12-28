<?php

/**
 * ShoppingCart is a session handler that stores
 * information about what products are in a user's
 * cart on the site.
 *
 * @package ecommerce
 */
class ShoppingCart extends Controller {

	//public, because it is referred to in the _config file...
	public static $url_segment = 'shoppingcart';
		static function set_url_segment($v) {self::$url_segment = $v;}
		static function get_url_segment() {return self::$url_segment;}

	protected static $order = null; // for temp caching
		static function set_order(Order $v) {self::$url_segment = $v;}
		static function get_order() {return self::$url_segment;}

	protected static $cartid_session_name = 'shoppingcartid';
		public static function set_cartid_session_name($v) {self::$cartid_session_name = $v;}
		public static function get_cartid_session_name() {return self::$cartid_session_name;}

	protected static $response_class = "CartResponse";
		public static function set_response_class($v) {self::$url_segment = $v;}
		public static function get_response_class() {return self::$url_segment;}

	static $allowed_actions = array (
		'additem',
		'removeitem',
		'removeallitem',
		'removemodifier',
		'setcountry',
		'setquantityitem',
		'clear',
		'numberofitemsincart',
		'showcart',
		'debug' => 'ADMIN'
	);

	function init() {
		parent::init();
		self::current_order()->initModifiers();
	}

	/**
	 *	used for allowing certian url parameters to be applied to orderitems
	 *	eg: ?Color=red will set OrderItem color to 'red'
	 *	name - defaultvalue (needed for default orderitems)
	 *
	 *	array(
	 *		'Color' => 'Red' //default to red
	 *	)
	 *
	*/
	protected static $paramfilters = array();
		function set_param_filters($array){self::$paramfilters = array_merge(self::$paramfilters,$array);}


	//Country functions

	static function country_setting_index() {
		return "ShoppingCartCountry";
	}

	static function set_country($country) {
		$countrySettingIndex = self::country_setting_index();
		Session::set($countrySettingIndex, $country);
	}

	static function get_country() {
		$countrySettingIndex = self::country_setting_index();
		return Session::get($countrySettingIndex);
	}

	static function remove_country() {
		$countrySettingIndex = self::country_setting_index();
		Session::clear($countrySettingIndex);
	}


	//Controller links

	static function add_item_link($productID, $className = "OrderItem", $parameters = array()) {
		return self::$url_segment.'/additem/'.$productID."/".self::order_item_class_name($className).self::params_to_get_string($parameters);
	}

	static function remove_item_link($productID, $className = "OrderItem", $parameters = array()) {
		return self::$url_segment.'/removeitem/'.$productID."/".self::order_item_class_name($className).self::params_to_get_string($parameters);
	}

	static function remove_all_item_link($productID, $className = "OrderItem", $parameters = array()) {
		return self::$url_segment.'/removeallitem/'.$productID."/".self::order_item_class_name($className).self::params_to_get_string($parameters);
	}

	static function set_quantity_item_link($productID, $className = "OrderItem", $parameters = array()) {
		return self::$url_segment.'/setquantityitem/'.$productID."/".self::order_item_class_name($className).self::params_to_get_string($parameters);
	}

	static function add_modifier_link($modifierID, $className = "OrderModifier") {
		return self::$url_segment.'/addmodifier/'.$modifierID."/".self::order_modifier_class_name($className);
	}

	static function remove_modifier_link($modifierID, $className = "OrderModifier") {
		return self::$url_segment.'/removemodifier/'.$modifierID."/".self::order_modifier_class_name($className);
	}


	static function get_country_link() {
		return self::$url_segment.'/setcountry/';
	}

	/** helper function for appending variation id */
	protected static function variation_link($variationid) {
		user_error("This function is now outdated and we should use classname link instead!", E_USER_ERROR);
	}

	protected static function order_item_class_name($className) {
		if(!ClassInfo::exists($className)) {
			user_error("ShoppingCart::order_item_class_name ... $className does not exist", E_USER_ERROR);
		}
		if(in_array($className, array("OrderItem", "OrderAttribute"))) {
			user_error("ShoppingCart::order_item_class_name ... $className should be a subclassed", E_USER_NOTICE);
			return $className;
		}
		if(ClassInfo::is_subclass_of($className, "OrderItem")) {
			//do nothing
			if(substr($className, -10) != Buyable::get_order_item_class_name_post_fix()) {
				user_error("ShoppingCart::order_item_class_name, $className should end in _OrderItem", E_USER_ERROR);
			}
		}
		elseif(ClassInfo::is_subclass_of($className, "DataObject")) {
			$className .= Buyable::get_order_item_class_name_post_fix();
			return self::order_item_class_name($className);
		}
		return $className;
	}

	protected static function item_class_name($className) {
		return str_replace(Buyable::get_order_item_class_name_post_fix(), "", self::order_item_class_name($className));
	}

	//modifiers
	protected static function order_modifier_class_name($className) {
		if(!ClassInfo::exists($className)) {
			user_error("ShoppingCart::order_modifier_class_name ... $className does not exist", E_USER_ERROR);
		}
		if(in_array($className, array("OrderAttribute", "OrderModifier"))) {
			user_error("ShoppingCart::order_modifier_class_name ... $className should be a subclassed", E_USER_NOTICE);
			return $className;
		}
		if(ClassInfo::is_subclass_of($className, "OrderModifier")) {
			//do nothing
		}
		else {
			user_error("ShoppingCart::order_modifier_class_name ... $className should be a subclass of OrderModifier", E_USER_ERROR);
		}
		return $className;
	}

	/**
	 * Creates the appropriate string parameters for links from array
	 *
	 * Produces string such as: MyParam%3D11%26OtherParam%3D1
	 *     ...which decodes to: MyParam=11&OtherParam=1
	 *
	 * you will need to decode the url with javascript before using it.
	 *
	 */
	protected static function params_to_get_string($array){
		if($array & count($array > 0)){
			array_walk($array , create_function('&$v,$k', '$v = $k."=".$v ;'));
			return "/?".implode("&",$array);
		}
		return "/";
	}

	public static function current_order() {
		$order = self::$order;
		$hasWritten = false;
		if (!self::$order) {
			//find order by id saved to session (allows logging out and retaining cart contents)
			$cartid = Session::get(self::$cartid_session_name);
			//TODO: make clear cart on logout optional
			if ($cartid && self::$order = DataObject::get_one('Order', "\"Status\" = 'Cart' AND \"ID\" = $cartid")) {
				//do nothing
			}
			else {
				self::$order = new Order();
				self::$order->SessionID = session_id();
				//$order->MemberID = Member::currentUserID(); // Set the Member relation to this order
				self::$order->write();
				$hasWritten = true;
				Session::set(self::$cartid_session_name,self::$order->ID);
			}
		}
		//TODO: re-introduce this because it allows seeing which members don't complete orders
		//$order->MemberID = Member::currentUserID(); // Set the Member relation to this order
		if(!$hasWritten) {
			self::$order->write(); // Write the order
		}
		return self::$order;
	}


	// Static items management

	/**
	 * Either update or create OrderItem in ShoppingCart.
	 */
	static function add_new_item(OrderItem $item) {
		$item->write();
		self::current_order()->Attributes()->add($item);
	}

	/**
	 * Add a new OrderItem to session
	 */
	static function add_item($existingitem, $quantity = 1) {
		if ($existingitem) {
			$existingitem->Quantity += $quantity;
			$existingitem->write();
		}
	}

	/**
	 * Update quantity of an OrderItem in the session
	 */
	static function set_quantity_item($existingitem, $quantity) {
		if ($existingitem) {
			$existingitem->Quantity = $quantity;
			$existingitem->write();
		}
	}

	/**
	 * Reduce quantity of an orderItem, or completely remove
	 */
	static function remove_item($existingitem, $quantityToReduceBy = 1) {
		if ($existingitem) {
			if ($quantityToReduceBy >= $existingitem->Quantity) {
				$existingitem->delete();
				$existingitem->destroy();
			}
			else {
				$existingitem->Quantity -= $quantityToReduceBy;
				$existingitem->write();
			}
		}
	}

	static function remove_all_item($existingitem) {
		if($existingitem){
			$existingitem->delete();
			$existingitem->destroy();
		}
	}

	static function remove_all_items() {
		//TODO: make this ONLY remove items & not modifiers also?
		self::current_order()->Attributes()->removeAll();
	}

	/**
	 * Check if there are any items in the cart
	 */
	static function has_items() {
		return self::current_order()->Items() != null;
	}

	/**
	 * Return the items currently in the shopping cart.
	 * @return array
	 */
	static function get_items($filter = null) {
		return self::current_order()->Items($filter);
	}

	/**
	 * Get OrderItem according to product id, and coorresponding parameter filter.
	 */
	static function get_item_by_id($buyableID, $className = "OrderItem", $filter = "" ) {
		if(!ClassInfo::is_subclass_of($className, "OrderItem")) {
			user_error("$className needs to be a subclass of OrderItem", E_USER_WARNING);
		}
		$filter = self::get_param_filter($filter);
		$order = self::current_order();
		$filterString = ($filter && trim($filter) != "") ? " AND $filter" : "";
		return DataObject::get_one($className, "\"OrderID\" = ".$order->ID." AND \"BuyableID\" = ".$buyableID." ". $filterString);
	}

	/**
	 * Get item according to a filter.
	 */
	static function get_item($filter) {
		$order = self::current_order();
		if($filter) {
			$filterString = " AND ($filter)";
		}
		return  DataObject::get_one('OrderItem', "\"OrderID\" = $order->ID $filterString");
	}

	static function add_buyable($buyable,$quantity = 1){
		if(!$buyable) return null;
		
		$item = self::find_or_make_order_item($buyable);
		if($item->ID){
			$item->Quantity += $quantity;
			$item->write();
		}else{
			$item->Quantity = $quantity;
			$item->write();
			self::add_new_item($item);

		}
				
		return $item;
	}
	
	static function find_or_make_order_item($buyable){		
		if($item = self::get_item_by_id($buyable->ID,$buyable->classNameForOrderItem())){
			return $item;
		}
		return self::create_order_item($buyable);
	}

	/**
	 * Creates a new order item based on url parameters
	 */
	static function create_order_item($buyable,$quantity = 1, $parameters = null){
		
		$orderitem = null;
		$itemclass = $buyable->classNameForOrderItem();
		if($buyable && $buyable->canPurchase()) {
			$orderitem = new $itemclass();
			$orderitem->addBuyable($buyable);
		}

		//set extra parameters
		if($orderitem instanceof OrderItem && is_array($parameters)){
			foreach(self::$paramfilters as $param => $defaultvalue){
				$v = (isset($parameters[$param])) ? Convert::raw2sql($parameters[$param]) : $defaultvalue;
				$orderitem->$param = $v;
			}
		}
		return $orderitem;
	}


	// Modifiers management

	static function add_new_modifier(OrderModifier $modifier) {
		$modifier->write();
		self::current_order()->Attributes()->add($modifier);
	}

	static function can_remove_modifier($modifierIndex) {
		$serializedModifierIndex = self::modifier_index($modifierIndex);
		if ($serializedModifier = Session::get($serializedModifierIndex)) {
			$unserializedModifier = unserialize($serializedModifier);
			return $unserializedModifier->CanRemove();
		}
		return false;
	}

	static function remove_modifier($modifierIndex) {
		$serializedModifierIndex = self::modifier_index($modifierIndex);
		Session::clear($serializedModifierIndex);
	}

	static function remove_all_modifiers() {
		self::current_order()->Attributes()->removeAll(); //TODO: make this ONLY remove modifiers
	}

	static function has_modifiers() {
		return self::get_modifiers() != null;
	}

	/**
	 * Get all the {@link OrderModifier} instances
	 * that are currently in use. To set them, use
	 * {@link Order::set_modifiers()}.
	 *
	 * @return array
	 */
	static function get_modifiers() {
		return self::current_order()->Modifiers();
	}

	static function uses_different_shipping_address(){
		return self::current_order()->UseShippingAddress;
	}

	static function set_uses_different_shipping_address($use = true){
		$order = self::current_order();
		$order->UseShippingAddress = $use;
		$order->write();
	}

	/**
	 * Sets appropriate status, and message and redirects or returns appropriately.
	 */
	 //TODO: it seems silly that this should be a static method just because self::clear is static
	static function return_data($status = "success",$message = null){

		if(Director::is_ajax()){
			$obj = new self::$response_class();
			return $obj->ReturnCartData($status, $message);
		}
		//TODO: set session / status in session (like Form sessionMesssage)
		Director::redirectBack();
	}

	//--------------------------------------------------------------------------
	//Data
	//----
	function Cart() {
		return self::get_order();
	}

	//--------------------------------------------------------------------------
	//Actions
	//--------------------------------------------------------------------------

	/**
	 * Either increments the count or creates a new item.
	 */
	function additem($request) {
		if ($itemId = $request->param('ID')) {
			if($item = ShoppingCart::get_item($this->urlFilter())) {
				ShoppingCart::add_item($item);
				return self::return_data("success","Extra item added"); //TODO: i18n
			}else {
				if($orderitem = $this->getNewOrderItem()) {
					ShoppingCart::add_new_item($orderitem);
					return self::return_data("success","Item added"); //TODO: i18n
				}
			}
		}
		return self::return_data("failure","Item could not be added"); //TODO: i18n
	}

	function removeitem($request) {
		if ($item = ShoppingCart::get_item($this->urlFilter())) {
			ShoppingCart::remove_item($item);
			return self::return_data("success","Item removed");//TODO: i18n
		}
		return self::return_data("failure","Item could not be found in cart");//TODO: i18n
	}

	function removeallitem() {
		if ($item = ShoppingCart::get_item($this->urlFilter())) {
			ShoppingCart::remove_all_item($item);
			return self::return_data("success","Item fully removed");//TODO: i18n
		}
		return self::return_data("failure","Item could not be found in cart");//TODO: i18n
	}

	function setcountry() {
		$request = $this->getRequest();
		$countryCode = $request->param('ID');
		if($countryCode && strlen($countryCode) < 4) {
			ShoppingCart::set_country($countryCode);
			return self::return_data("success","Country updated");//TODO: i18n
		}
		return self::return_data("failure","Country could not be updated");//TODO: i18n
	}

	/**
	 * Clears the cart
	 * It disconnects the current cart from the user session.
	 */
	static function clear($request = null) {
		self::current_order()->SessionID = null;
		self::current_order()->write();
		self::remove_all_items();
		self::$order = null;

		//redirect back or send ajax only if called via http request.
		//This check allows this function to be called from elsewhere in the system.
		if($request instanceof SS_HTTPRequest){
			return self::return_data("success","Cart cleared");//TODO: i18n
		}
	}

	/**
	 * Ajax method to set an item quantity
	 */
	function setquantityitem($request) {
		$quantity = $request->getVar('quantity');
		if (is_numeric($quantity) && $quantity == floatval($quantity)) {
			$item = ShoppingCart::get_item($this->urlFilter());
			if(!$item){
				$item = $this->getNewOrderItem();
				$item->Quantity = $quantity;
				self::add_new_item($item);
			}
			else{
				ShoppingCart::set_quantity_item($item, $quantity);
			}
			return self::return_data("success","Quantity set successfully");//TODO: i18n
		}
		return self::return_data("failure","Quantity provided is not numeric");//TODO: i18n
	}

	/**
	 * Removes specified modifier, if allowed
	 */
	function removemodifier() {
		$modifierId = $this->urlParams['ID'];
		if (ShoppingCart::can_remove_modifier($modifierId)){
			ShoppingCart::remove_modifier($modifierId);
			return self::return_data("success","Removed");//TODO: i18n
		}
		return self::return_data("failure","Could not be removed");//TODO: i18n
	}

	/**
	 * return number of items in cart
	 */

	function numberofitemsincart() {
		$cart = self::current_order();
		if($cart) {
			if($cart = $this->Cart()) {
				if($items = $cart->Items()) {
					return $items->count();
				}
			}
		}
		return 0;
	}

	/**
	 * return cart for ajax call
	 */
	function showcart() {
		$this->renderWith("AjaxSimpleCart");
	}
	
	//Helper functions

	/**
	 * Creates new order item based on url parameters
	 */
	protected function getNewOrderItem(){
		$request = $this->getRequest();
		$orderitem = null;
		$buyableId = intval($request->param('ID'));
		//create order item
		if(is_numeric($buyableId)) {
			$itemClassName = self::item_class_name($request->param('OtherID'));
			if($itemClassName) {
				$buyable = null;
				if(Object::has_extension($itemClassName,'Versioned') && singleton($itemClassName)->hasVersionField('Live')){ //only 'Live' versions should be used for versioned products
					$buyable = Versioned::get_one_by_stage($itemClassName,'Live', '"'.$itemClassName.'_Live"."ID" = '.$buyableId);
				}else{
					$buyable = DataObject::get_one($itemClassName, '"'.$itemClassName.'"."ID" = '.$buyableId);
				}
				if ($buyable && $buyable->canPurchase()) {
					$orderItemClassName = self::order_item_class_name($buyable->ClassName);
					$orderitem = new $orderItemClassName();
					$orderitem->addBuyable($buyable,1);
				}
			}
			else {
				user_error("no itemClassName ($itemClassName) provided for item to be added", E_USER_ERROR);
			}
		}
		else {
			user_error("no id provided for item to be added", E_USER_ERROR);
		}
		//set extra parameters
		if($orderitem instanceof OrderItem){
			foreach(self::$paramfilters as $param => $defaultvalue){
				$v = ($request->getVar($param)) ? Convert::raw2sql($request->getVar($param)) : $defaultvalue;
				$orderitem->$param = $v;
			}
		}
		return $orderitem;
	}


	/**
	 * Gets a SQL filter based on array of parameters.
	 *
	 * 	 Returns default filter if none provided,
	 *	 otherwise it updates default filter with passed parameters
	 */
	static function get_param_filter($params = array()){
		if(!self::$paramfilters) {
			return ""; //no use for this if there are not parameters defined
		}
		$temparray = self::$paramfilters;
		$outputarray = array();
		foreach(self::$paramfilters as $field => $value){
			if(isset($params[$field])){
				//TODO: convert to $dbfield->prepValueForDB() when Boolean problem figured out
				$temparray[$field] = Convert::raw2sql($params[$field]);
			}
			$outputarray[] = "\"".$field."\" = ".$temparray[$field];
		}
		return implode(" AND ",$outputarray);
	}

	/**
	 * Gets a filter based on urlParameters
	 */
	protected function urlFilter(){
		$result = '';
		$request = $this->getRequest();
		$orderItemClassName = self::order_item_class_name($request->param('OtherID'));
		$itemClassName = self::item_class_name($request->param('OtherID'));
		$selection = array(
			"\"BuyableID\" = ".$request->param('ID')
		);
		if(ClassInfo::is_subclass_of($request->param('OtherID'), "OrderAttribute")){
			$selection[] = "\"ClassName\" = '".$orderItemClassName."'";
		}

		$filter = self::get_param_filter($request->getVars());
		if( $filter ){
			$result = implode(" AND ",array_merge($selection,array($filter)));
		}
		else {
			$result = implode(" AND ",$selection);
		}
		return $result;
	}

	/**
	 * Displays order info and cart contents.
	 */
	function debug() {
		Debug::show(ShoppingCart::current_order());
	}

}
