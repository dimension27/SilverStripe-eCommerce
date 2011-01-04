<?php

/**
 * ShoppingCart is a session handler that stores
 * information about what products are in a user's
 * cart on the site.
 *
 * @package ecommerce
 * @Description
 ** Non URL based adding	add_buyable->find_or_make_order_item->add_(new)_item
 ** URL based adding	additem->getNew/ExistingOrderItem->add_(new)_item
 **/

class ShoppingCart extends Controller {

	//public, because it is referred to in the _config file...
	public static $url_segment = 'shoppingcart';
		static function set_url_segment($v) {self::$url_segment = $v;}
		static function get_url_segment() {return self::$url_segment;}

	protected static $order = null; // for temp caching
		static function set_order(Order $v) {self::$order = $v;}
		static function get_order() {user_error("Use self::current_order() to get order.", E_USER_ERROR);}

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
	protected static $default_param_filters = array();
		static function set_default_param_filters($array){self::$default_param_filters = $array;}
		static function add_default_param_filters($array){self::$default_param_filters = array_merge(self::$default_param_filters,$array);}
		static function get_default_param_filters(){return self::$default_param_filters;}


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

	static function add_item_link($buyableID, $className = "OrderItem", $parameters = array()) {
		return self::$url_segment.'/additem/'.$buyableID."/".self::order_item_class_name($className).self::params_to_get_string($parameters);
	}

	static function remove_item_link($buyableID, $className = "OrderItem", $parameters = array()) {
		return self::$url_segment.'/removeitem/'.$buyableID."/".self::order_item_class_name($className).self::params_to_get_string($parameters);
	}

	static function remove_all_item_link($buyableID, $className = "OrderItem", $parameters = array()) {
		return self::$url_segment.'/removeallitem/'.$buyableID."/".self::order_item_class_name($className).self::params_to_get_string($parameters);
	}

	static function set_quantity_item_link($buyableID, $className = "OrderItem", $parameters = array()) {
		return self::$url_segment.'/setquantityitem/'.$buyableID."/".self::order_item_class_name($className).self::params_to_get_string($parameters);
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
			$length = strlen(Buyable::get_order_item_class_name_post_fix()) * -1;
			if(substr($className, $length) != Buyable::get_order_item_class_name_post_fix()) {
				user_error("ShoppingCart::order_item_class_name, $className should end in '".Buyable::get_order_item_class_name_post_fix()."'", E_USER_ERROR);
			}
		}
		elseif(ClassInfo::is_subclass_of($className, "DataObject")) {
			$className .= Buyable::get_order_item_class_name_post_fix();
			return self::order_item_class_name($className);
		}
		return $className;
	}

	protected static function buyable_class_name($orderItemClassName) {
		return str_replace(Buyable::get_order_item_class_name_post_fix(), "", self::order_item_class_name($orderItemClassName));
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
			$cartID = Session::get(self::$cartid_session_name);
			//TODO: make clear cart on logout optional
			if ($cartID) {
				$cartIDParts = Convert::raw2sql(explode(".", $cartID));
				if(is_array($cartIDParts) && count($cartIDParts) == 2) {
					$orders = DataObject::get(
						'Order',
						"\"Order_Status\".\"CanEdit\" = 1 AND \"Order\".\"ID\" = '".intval($cartIDParts[0])."' AND \"Order\".\"SessionID\" = '".$cartIDParts[1]."'",
						"\"LastEdited\" DESC",
						"INNER JOIN \"Order_Status\" ON \"Order_Status\" .\"ID\" = \"Order\".\"StatusID\"",
						"1"
					);
					if($orders) {
						self::$order = $orders->First();
					}
					else {
						//TO DO: create notice that order can not be found
					}
				}
			}
			if(!self::$order){
				self::$order = new Order();
				self::$order->SessionID = session_id();
				//$order->MemberID = Member::currentUserID(); // Set the Member relation to this order
				if($newStatus = DataObject::get_one("Order_Status", "\"CanEdit\" = 1")) {
					self::$order->StatusID = $newStatus->ID;
					self::$order->write();
					$hasWritten = true;
					Session::set(self::$cartid_session_name,self::$order->ID.".".session_id());
				}
				else {
					user_error("No Order_Status has been created with CanEdit = 1... Run Dev/Build", E_USER_WARNING);
				}
			}
		}
		//TODO: re-introduce this because it allows seeing which members don't complete orders
		//$order->MemberID = Member::currentUserID(); // Set the Member relation to this order
		if(!$hasWritten) {
			self::$order->write(); // Write the order
		}
		//seems the best way to add some basic css.
		Requirements::themedCSS("EcommerceBasics");
		return self::$order;
	}


	// Static items management

	/**
	 * Either update or create OrderItem in ShoppingCart.
	 */
	protected static function add_new_item(OrderItem $newOrderItem, $quantity = 1) {
		//what happens if it has already been added???
		$newOrderItem->Quantity = $quantity;
		$newOrderItem->write();
		self::current_order()->Attributes()->add($newOrderItem);
	}

	/**
	 * Add QTY to an existing OrderItem to session
	 */
	protected static function add_item($existingOrderItem, $quantity = 1) {
		//what happens if the item doe not actually exists?
		if($existingOrderItem->ID) {
			$existingOrderItem->Quantity += $quantity;
			$existingOrderItem->write();
		}
		else {
			user_error("Item has not been saved yet", E_USER_WARNING);
		}
	}

	/**
	 * Update quantity of an OrderItem in the session
	 */
	static function set_quantity_item($existingOrderItem, $quantity) {
		if ($existingOrderItem) {
			$existingOrderItem->Quantity = $quantity;
			$existingOrderItem->write();
		}
	}

	/**
	 * Reduce quantity of an orderItem, or completely remove
	 */
	static function remove_item($existingOrderItem, $quantityToReduceBy = 1) {
		if ($existingOrderItem) {
			if ($quantityToReduceBy >= $existingOrderItem->Quantity) {
				$existingOrderItem->delete();
				$existingOrderItem->destroy();
			}
			else {
				$existingOrderItem->Quantity -= $quantityToReduceBy;
				$existingOrderItem->write();
			}
		}
	}

	static function remove_all_item($existingOrderItem) {
		if($existingOrderItem){
			$existingOrderItem->delete();
			$existingOrderItem->destroy();
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
	static function get_order_item_by_buyableid($buyableID, $orderItemClassName = "OrderItem", $parameters = null ) {
		if(!ClassInfo::is_subclass_of($orderItemClassName, "OrderItem")) {
			user_error("$className needs to be a subclass of OrderItem", E_USER_WARNING);
		}
		$filter = self::turn_params_into_sql($parameters = null);
		$order = self::current_order();
		$filterString = ($filter && trim($filter) != "") ? " AND $filter" : "";
		// NOTE: MUST HAVE THE EXACT CLASSNAME !!!!! THEREFORE INCLUDED IN WHERE PHRASE
		return DataObject::get_one($orderItemClassName, "\"ClassName\" = '".$orderItemClassName."' AND \"OrderID\" = ".$order->ID." AND \"BuyableID\" = ".$buyableID." ". $filterString);
	}


	static function add_buyable($buyable,$quantity = 1, $parameters = null){
		$orderItem = null;
		if(!$buyable) {
			user_error("No buyable was provided to add", E_USER_NOTICE);
			return null;
		}
		$orderItem = self::find_or_make_order_item($buyable, $parameters = null);
		if($orderItem) {
			if($orderItem->ID){
				self::add_item($orderItem, $quantity);
			}
			else{
				self::add_new_item($orderItem, $quantity);
			}
		}
		return $orderItem;
	}

	static function find_or_make_order_item($buyable, $parameters = null){
		if($orderItem = self::get_order_item_by_buyableid($buyable->ID,$buyable->classNameForOrderItem())){
			//do nothing
		}
		else {
			$orderItem = self::create_order_item($buyable, 1, $parameters = null);
		}
		return $orderItem;
	}

	/**
	 * Creates a new order item based on url parameters
	 */
	protected static function create_order_item($buyable,$quantity = 1, $parameters = null){
		$orderItem = null;
		if($buyable && $buyable->canPurchase()) {
			$classNameForOrderItem = $buyable->classNameForOrderItem();
			$orderItem = new $classNameForOrderItem();
			$orderItem->addBuyableToOrderItem($buyable, $quantity);
		}
		if($orderItem) {
			//set extra parameters
			if($orderItem instanceof OrderItem && is_array($parameters)){
				$defaultParamFilters = self::get_default_param_filters();
				foreach($defaultParamFilters as $param => $defaultvalue){
					$v = (isset($parameters[$param])) ? Convert::raw2sql($parameters[$param]) : $defaultvalue;
					//how does this get saved in database? should we check if field exists?
					$orderItem->$param = $v;
				}
			}
		}
		else {
			user_error("product is not for sale or buyable ($buyable->Title()) does not exists.", E_USER_NOTICE);
		}
		return $orderItem;
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
		return self::current_order();
	}

	//--------------------------------------------------------------------------
	//Actions
	//--------------------------------------------------------------------------

	/**
	 * Either increments the count or creates a new item.
	 */
	function additem($request) {
		if ($request->param('ID')) {
			if($orderItem = $this->getExistingOrderItemFromURL()) {
				ShoppingCart::add_item($orderItem, 1);
				return self::return_data("success","Extra item added"); //TODO: i18n
			}
			else {
				if($orderItem = $this->getNewOrderItemFromURL()) {
					ShoppingCart::add_new_item($orderItem, 1);
					return self::return_data("success","Item added"); //TODO: i18n
				}
			}
		}
		return self::return_data("failure","Item could not be added"); //TODO: i18n
	}

	function removeitem($request) {
		if ($orderItem = $this->getExistingOrderItemFromURL()) {
			ShoppingCart::remove_item($orderItem);
			return self::return_data("success","Item removed");//TODO: i18n
		}
		return self::return_data("failure","Item could not be found in cart");//TODO: i18n
	}

	function removeallitem() {
		if ($orderItem = $this->getExistingOrderItemFromURL()) {
			ShoppingCart::remove_all_item($orderItem);
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
			$orderItem = $this->getExistingOrderItemFromURL();
			if(!$orderItem){
				$newOrderItem = $this->getNewOrderItemFromURL();
				self::add_new_item($newOrderItem, $quantity);
			}
			else{
				ShoppingCart::set_quantity_item($orderItem, $quantity);
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
			return $cart->TotalItems();
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
	protected function getNewOrderItemFromURL(){
		$request = $this->getRequest();
		$orderitem = null;
		$buyableID = intval($request->param('ID'));
		//create order item
		if(is_numeric($buyableID)) {
			$buyableClassName = self::buyable_class_name($request->param('OtherID'));
			if($buyableClassName) {
				$buyable = null;
				/*
				if(Object::has_extension($buyableClassName,'Versioned') && singleton($buyableClassName)->hasVersionField('Live')){ //only 'Live' versions should be used for versioned products
					die("A");
					$buyable = Versioned::get_one_by_stage($buyableClassName,'Live', '"'.$buyableClassName.'_Live"."ID" = '.$buyableID);
				}
			*/
				$buyable = DataObject::get_by_id($buyableClassName, $buyableID);
				if ($buyable ) {
					if($buyable->canPurchase()) {
						$orderItemClassName = self::order_item_class_name($buyable->ClassName);
						$orderitem = new $orderItemClassName();
						$orderitem->addBuyableToOrderItem($buyable,1);
					}
					else {
						user_error($buyable->Title." is not for sale!", E_USER_ERROR);
					}
				}
				else {
					user_error("Buyable was not provided", E_USER_ERROR);
				}
			}
			else {
				user_error("no itemClassName ($buyableClassName) provided for item to be added", E_USER_ERROR);
			}
		}
		else {
			user_error("no id provided for item to be added - should be a URL parameter", E_USER_ERROR);
		}
		//set extra parameters
		if($orderitem instanceof OrderItem){
			$defaultParamFilters = self::get_default_param_filters();
			foreach($defaultParamFilters as $param => $defaultvalue){
				$v = ($request->getVar($param)) ? Convert::raw2sql($request->getVar($param)) : $defaultvalue;
				$orderitem->$param = $v;
			}
		}
		return $orderitem;
	}


	/**
	 * Get item according to a filter.
	 */
	protected function getExistingOrderItemFromURL() {
		$filter = $this->urlFilter();
		$order = self::current_order();
		if($filter) {
			$filterString = " AND ($filter)";
		}
		return  DataObject::get_one('OrderItem', "\"OrderID\" = $order->ID $filterString");
	}


	/**
	 * Gets a SQL filter based on array of parameters.
	 *
	 * 	 Returns default filter if none provided,
	 *	 otherwise it updates default filter with passed parameters
	 */
	protected static function turn_params_into_sql($params = array()){
		$defaultParamFilters = self::get_default_param_filters();
		if(!count($defaultParamFilters)) {
			return ""; //no use for this if there are not parameters defined
		}
		$outputArray = array();
		foreach($defaultParamFilters as $field => $value){
			if(isset($params[$field])){
				//TODO: convert to $dbfield->prepValueForDB() when Boolean problem figured out
				$defaultParamFilters[$field] = Convert::raw2sql($params[$field]);
			}
			$outputarray[$field] = "\"".$field."\" = ".$defaultParamFilters[$field];
		}
		if(count($outputArray)) {
			return implode(" AND ",$outputArray);
		}
	}

	/**
	 * Gets a filter based on urlParameters
	 */
	protected function urlFilter(){
		$result = '';
		$request = $this->getRequest();
		$orderItemClassName = self::order_item_class_name($request->param('OtherID'));
		$buyableClassName = self::buyable_class_name($orderItemClassName);
		$selection = array(
			"\"BuyableID\" = ".$request->param('ID')
		);
		if(ClassInfo::is_subclass_of($request->param('OtherID'), "OrderAttribute")){
			$selection[] = "\"ClassName\" = '".$orderItemClassName."'";
		}

		$filter = self::turn_params_into_sql($request->getVars());
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
