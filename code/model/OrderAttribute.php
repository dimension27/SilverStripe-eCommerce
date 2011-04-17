<?php
/**
 * @description: base class for OrderItem (item in cart) and OrderModifier (extra - e.g. Tax)
 * @see OrderModifier
 * @see OrderItem
 *
 * @package ecommerce
 * @authors: Silverstripe, Jeremy, Nicolaas
 **/

class OrderAttribute extends DataObject {

	public static $db = array(
		'Sort' => 'Int',
		'GroupSort' => 'Int'
	);

	public static $has_one = array(
		'Order' => 'Order',
		'OrderAttribute_Group' => 'OrderAttribute_Group'
	);

	public static $casting = array(
		'TableTitle' => 'HTMLText',
		'TableSubTitle' => 'HTMLText',
		'CartTitle' => 'HTMLText'
	);

	public static $create_table_options = array(
		'MySQLDatabase' => 'ENGINE=InnoDB'
	);

	/**
	* @note: we can add the \"OrderAttribute_Group\".\"Sort\" part because this table is always included (see extendedSQL).
	**/
	public static $default_sort = "\"OrderAttribute\".\"GroupSort\" ASC, \"OrderAttribute\".\"Sort\" ASC, \"OrderAttribute\".\"Created\" ASC";

	public static $indexes = array(
		"Sort" => true,
	);

	protected static $has_been_written = false;
		public static function set_has_been_written($b = true) {Session::set("OrderAttributeHasBeenWritten", $b); self::$has_been_written = true;}
		public static function get_has_been_written() {return Session::get("OrderAttributeHasBeenWritten") || self::$has_been_written ? true : false;}
		public static function unset_has_been_written() {Session::set("OrderAttributeHasBeenWritten", false);self::$has_been_written = false;}

	protected $_canEdit = null;

	function init() {
		return true;
	}

	/**
	 *@return Boolean
	 **/
	function TableMessageID() {
		return self::$table_message_id."_".$this->ID;
	}

	/**
	 *@return Boolean
	 **/
	function canCreate($member = null) {
		return true;
	}

	/**
	 *@return Boolean
	 **/
	function canEdit($member = null) {
		if($this->_canEdit === null) {
			$this->_canEdit = false;
			if($this->OrderID) {
				if($this->Order()->exists()) {
					if($this->Order()->canEdit($member)) {
						$this->_canEdit = true;
					}
				}
			}
		}
		return $this->_canEdit;
	}

	/**
	 *@return Boolean
	 **/
	function canDelete($member = null) {
		return false;
	}

	/**
	 *@return Boolean (true on success / false on failure)
	 **/
	public function addBuyableToOrderItem($object) {
		//more may be added here in the future
		return true;
	}
	/**
	 * @see DataObject::extendedSQL
	 * TO DO: make it work... because we call DataObject::get(....) it may not be called....
	public function extendedSQL($filter = "", $sort = "", $limit = "", $join = "", $having = ""){
		$join .= " LEFT JOIN \"OrderAttribute_Group\" ON \"OrderAttribute_Group\".\"ID\" = \"OrderAttribute\".\"OrderAttribute_GroupID\"";
		return parent::extendedSQL($filter, $sort, $limit, $join, $having);
	}
	*/
	######################
	## TEMPLATE METHODS ##
	######################

	/**
	 * Return a string of class names, in order
	 * of heirarchy from OrderAttribute for the
	 * current attribute.
	 *
	 * e.g.: "product_orderitem orderitem
	 * orderattribute".
	 *
	 * Used by the templates and for ajax updating functionality.
	 *
	 * @return string
	 */
	function Classes() {
		$class = get_class($this);
		$classes = array();
		$classes[] = strtolower($class);
		while(get_parent_class($class) != 'DataObject' && $class = get_parent_class($class)) {
			$classes[] = strtolower($class);
		}
		return implode(' ', $classes);
	}

	/**
	 *@return String for use in the Templates
	 **/
	function MainID() {
		return get_class($this) . '_' .'DB_' . $this->ID;
	}

	/**
	 *@return String for use in the Templates
	 **/
	function TableID() {
		return 'Table_' . $this->MainID();
	}

	/**
	 *@return String for use in the Templates
	 **/
	function CartID() {
		return 'Cart_' . $this->MainID();
	}

	/**
	 *@return String for use in the Templates
	 **/
	function TableTitleID() {
		return $this->TableID() . '_Title';
	}

	/**
	 *@return String for use in the Templates
	 **/
	function CartTitleID() {
		return $this->CartID() . '_Title';
	}

	/**
	 *@return String for use in the Templates
	 **/
	function TableTotalID() {
		return $this->TableID() . '_Total';
	}

	/**
	 *@return String for use in the Templates
	 **/
	function CartTotalID() {
		return $this->CartID() . '_Total';
	}
	/**
	 *Should this item be shown on check out page table?
	 *@return Boolean
	 **/
	function ShowInTable() {
		return true;
	}

	/**
	 *Should this item be shown on in the cart (which is on other pages than the checkout page)
	 *@return Boolean
	 **/
	function ShowInCart() {
		return $this->ShowInTable();
	}

	/**
	 * Return a name of what this attribute is
	 * called e.g. "Product 21" or "Discount"
	 *
	 * @return string
	 */
	function TableTitle() {
		return 'Attribute';
	}

	/**
	 * Return a name of what this attribute is
	 * called e.g. "Product 21" or "Discount"
	 *
	 * @return string
	 */
	function CartTitle() {
		return $this->TableTitle();
	}

	function onBeforeWrite() {
		parent::onBeforeWrite();
		if($this->OrderAttribute_GroupID) {
			if($group = $this->OrderAttribute_GroupID()) {
				$this->GroupSort = $group->Sort;
			}
		}
	}
	function onAfterWrite() {
		parent::onAfterWrite();
		self::set_has_been_written();
	}

	function onAfterDelete() {
		parent::onAfterDelete();
		self::set_has_been_written();
	}

}


class OrderAttribute_Group extends DataObject {

	public static $db = array(
		"Title" => "Varchar(100)",
		'Sort' => 'Int'
	);

	public static $default_sort = "\"Sort\" ASC";

	public static $indexes = array(
		"Sort" => true,
	);


}
