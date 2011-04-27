<?php
/**
 * Test {@link ShoppingCart}
 */
class ShoppingCartTestTwo extends FunctionalTest {

	static $fixture_file = 'ecommerce/tests/ecommerce.yml';
	
	static $disable_theme = true;
	
	static $use_draft_site = true;
	
	static $orig = array();
	
	function setUp() {
		parent::setUp();
		
		$this->orig['Product_site_currency'] = Product::site_currency();
		Product::set_site_currency('USD');
		
		$this->orig['Product_supported_currencies'] = Product::get_supported_currencies();
		Product::set_supported_currencies(array('EUR','USD','NZD'));
		
		$this->orig['Order_modifiers'] = Order::get_modifiers();
		Order::set_modifiers(array());
	}
	
	function tearDown() {
		parent::tearDown();
		
		Product::set_site_currency($this->orig['Product_site_currency']);
		Product::set_supported_currencies($this->orig['Product_supported_currencies']);
		Order::set_modifiers($this->orig['Order_modifiers']);
	}
	
	function testAddItemsToCart() {
		/* Retrieve the product to compare from fixture */
		$product2b = $this->objFromFixture('Product', 'p2b');
		
		/* Add 2 items of product-2b to the cart */
		$request = $this->get($product2b->addLink());	// New item
		$this->assertEquals($request->getStatusCode(), 200);
		$request = $this->get($product2b->addLink()); // Incrementing existing item by 1
		$this->assertEquals($request->getStatusCode(), 200);

		/* See what's in the cart */
		$items = ShoppingCart::get_items();
		
		/* There is 1 item in the cart */
		$this->assertEquals(count($items), 1, 'There is 1 item in the cart');
		
		/* We have the product that we asserted in our fixture file, with a quantity of 2 in the cart */
		$this->assertEquals($items[0]->getIdAttribute(), $product2b->ID, 'We have the correct Product ID in the cart.');
		$this->assertEquals($items[0]->Quantity, 2, 'We have 2 of this product in the cart.');
	}
	
	function testAddArbitraryQuantityToItem() {
		$product2b = $this->objFromFixture('Product', 'p2b');
		
		/* Add an item to the cart */
		$this->get($product2b->addLink());
		
		/* See what's in the cart */
		$items = ShoppingCart::get_items();
		
		/* There is 1 item in the cart, with a quantity of 1 */
		$this->assertEquals(count($items), 1, 'There is 1 item in the cart');
		$this->assertEquals($items[0]->Quantity, 1, 'There is a quantity of 1 for the item in the cart');
		
		/* Let's add 7 more of the same product to the cart */
		ShoppingCart::add_item($items[0]->getIDAttribute(), 7);
		
		/* See what's in the cart once more */
		$items = ShoppingCart::get_items();
		
		/* There is still 1 item in the cart, with a quantity of 8 */
		$this->assertEquals(count($items), 1, 'There is 1 item in the cart');
		$this->assertEquals($items[0]->Quantity, 8, 'There is a quantity of 1 for the item in the cart');
		
		/* Clear the shopping cart */
		ShoppingCart::clear();
	}
	
	function testRemoveItemFromCart() {
		$product2a = $this->objFromFixture('Product', 'p2a');
		$product2b = $this->objFromFixture('Product', 'p2b');
		
		/* Add 2 different products to the cart */
		$this->get($product2a->addLink());
		$this->get($product2b->addLink());
		$this->get($product2b->addLink());
		
		/* See what's in the cart in session */
		$items = ShoppingCart::get_items();
		
		/* There are 2 items in the cart. 1 of the first item, 2 of the second item */
		$this->assertEquals(count($items), 2, 'There are 2 items in the cart');
		$this->assertEquals($items[0]->getIDAttribute(), $product2a->ID, 'The first item is the same ID as the first product.');
		$this->assertEquals($items[0]->Quantity, 1, 'There is 1 of the first item.');
		$this->assertEquals($items[1]->getIDAttribute(), $product2b->ID, 'The second item is the same ID as the second product.');
		$this->assertEquals($items[1]->Quantity, 2, 'There are 2 of the second item.');
		
		/* Let's remove 1 piece of the second item from the cart */
		ShoppingCart::remove_item($product2b->ID, 1);
		
		/* We now have 1 piece left of the second item in the cart */
		$items = ShoppingCart::get_items();
		$this->assertEquals($items[1]->Quantity, 1, 'We now have 1 piece of the second item in the cart, we removed 1.');
		
		/* Now, let's remove the final piece of the item, removing that item from the cart completely */
		ShoppingCart::remove_item($product2b->ID, 1);
		
		/* Take a peek in the cart once again to see what changed */
		$items = ShoppingCart::get_items();
		
		/* We have none of the second item in the cart, because we removed both 2 pieces of it that existed */
		$this->assertTrue(empty($items[1]), 'There is no index of 1 because the item doesn\'t exist in the cart any longer.');
		
		/* Clear the shopping cart */
		ShoppingCart::clear();
	}
	
	function testClearEntireCart() {
		/* Invoke the existing test for adding items to the cart */
		$this->testAddItemsToCart();
		
		/* Get the items from the cart in session */
		$items = ShoppingCart::get_items();
		
		/* We have 1 item in the cart */
		$this->assertEquals(count($items), 1, 'There is 1 item in the cart');
		
		/* Clear the shopping cart */
		ShoppingCart::clear();
		
		/* Take a peek at what items are in the cart */
		$items = ShoppingCart::get_items();
		
		/* We have nothing in the cart */
		$this->assertEquals(count($items), 0, 'There are no items in the cart');
		
		/* Clear the shopping cart */
		ShoppingCart::clear();
	}
	
}
?>