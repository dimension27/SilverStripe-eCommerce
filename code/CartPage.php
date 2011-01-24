<?php

/**
 *
 * @package ecommerce
 * @authors: Silverstripe, Jeremy, Nicolaas
 **/

class CartPage extends Page{

	public static $db = array();

	public static $has_one = array(
		'CheckoutPage' => 'CheckoutPage',
		'ContinuePage' => 'SiteTree'
	);

	public static $icon = 'ecommerce/images/icons/cart';

	function getCMSFields(){
		$fields = parent::getCMSFields();
		if($checkouts = DataObject::get('CheckoutPage')) {
			$fields->addFieldToTab('Root.Content.Links',new DropdownField('CheckoutPageID','Checkout Page',$checkouts->toDropdownMap()));
		}
		$fields->addFieldToTab('Root.Content.Links',new TreeDropdownField('ContinuePageID','Continue Page',"SiteTree"));
		return $fields;
	}

	function MenuTitle() {
		$count = 0;
		$cart = ShoppingCart::current_order();
		if($cart) {
			if($cart = $this->Cart()) {
				if($items = $cart->Items()) {
					$count = $items->count();
				}
			}
		}
		$v = $this->MenuTitle;
		if($count) {
			$v .= " (".$count.")";
		}
		return $v;
	}


	/**
	 * Returns the link or the Link to the account page on this site
	 * @return String (URLSegment)
	 */
	public static function find_link() {
		if(!$page = DataObject::get_one('CartPage')) {
			return CheckoutPage::link();
		}
		return $page->Link();
	}

	/**
	 * Return a link to view the order on this page.
	 * @return String (URLSegment)
	 * @param int|string $orderID ID of the order
	 */
	public static function get_order_link($orderID) {
		return self::find_link(). 'showorder/' . $orderID . '/';
	}

}

class CartPage_Controller extends Page_Controller{

	protected $currentOrder = null;

	protected $orderID = 0;

	protected $memberID = 0;

	public function init() {
		parent::init();
		ShoppingCart::add_requirements();
		Requirements::themedCSS('CheckoutPage');
		$orderID = intval($this->getRequest()->param('ID'));
		//WE HAVE THIS FOR SUBMITTING FORMS!
		if(isset($_POST['OrderID'])) {
			$this->orderID = intval($_POST['OrderID']);
		}
	}

	function CurrentOrder() {
		if(!$this->currentOrder) {
			if($this->orderID) {
				$this->currentOrder = Order::get_by_id($this->orderID);
			}
			else {
				$this->currentOrder = ShoppingCart::current_order();
			}
		}
		return $this->currentOrder;
	}

	function showorder($request) {
		Requirements::themedCSS('Order');
		Requirements::themedCSS('Order_print', 'print');
		$this->orderID = intval($request->param("ID"));
		if(!$this->CurrentOrder()) {
			$this->message = _t('CartPage.ORDERNOTFOUND', 'Order can not be found.');
		}
		return array();
	}

}



