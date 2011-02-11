<?php

/**
 * @description: Defines the Order Status Options
 *   There must always be an OrderStep Created
 * @package ecommerce
 * @authors: Silverstripe, Jeremy, Nicolaas
 **/

class OrderStep extends DataObject {
	//database
	public static $db = array(
		"Name" => "Varchar(50)",
		"Code" => "Varchar(50)",
		"Description" => "Text",
		"CustomerMessage" => "HTMLText",
		//customer privileges
		"CanEdit" => "Boolean",
		"CanCancel" => "Boolean",
		//What to show the customer...
		"ShowAsUncompletedOrder" => "Boolean",
		"ShowAsInProcessOrder" => "Boolean",
		"ShowAsCompletedOrder" => "Boolean",
		//sorting index
		"Sort" => "Int"
		//by-pass
	);
	public static $indexes = array(
		"Code" => true,
		"Sort" => true
	);
	public static $has_many = array(
		"Orders" => "Order"
	);
	public static $field_labels = array(
		"Sort" => "Sorting Index",
		"CanEdit" => "Customer can edit",
		"CanCancel" => "Customer can cancel"
	);
	public static $summary_fields = array(
		"Name" => "Name",
		"CanEdit" => "CanEdit",
		"CanCancel" => "CanCancel",
		"ShowAsUncompletedOrder" => "ShowAsUncompletedOrder",
		"ShowAsInProcessOrder" => "ShowAsInProcessOrder",
		"ShowAsCompletedOrder" => "ShowAsCompletedOrder"
	);

	public static $singular_name = "Order Status Option";
		static function get_singular_name() {return self::$singular_name;}
		static function set_singular_name($v) {self::$singular_name = $v;}
		function i18n_singular_name() { return _t("OrderStep.ORDERSTEPOPTION", "Order Status Option");}

	public static $plural_name = "Order Status Options";
		static function get_plural_name() {return self::$plural_name;}
		static function set_plural_name($v) {self::$plural_name = $v;}
		function i18n_plural_name() { return _t("OrderStep.ORDERSTEPOPTION", "Order Status Options");}

	// SUPER IMPORTANT TO KEEP ORDER!
	public static $default_sort = "\"Sort\" ASC";

	public static function get_status_id_from_code($code) {
		if($otherStatus = DataObject::get_one("OrderStep", "\"Code\" = '".$code."'")) {
			return $otherStatus->ID;
		}
		return 0;
	}

	// MOST IMPORTANT DEFINITION!
	protected static $order_steps_to_include = array(
		"OrderStep_Created",
		"OrderStep_Submitted",
		"OrderStep_SentInvoice",
		"OrderStep_Paid",
		"OrderStep_SentReceipt",
		"OrderStep_Confirmed",
		"OrderStep_Sent"
	);
		static function set_order_steps_to_include($v) {self::$order_steps_to_include = $v;}
		static function get_order_steps_to_include() {return self::$order_steps_to_include;}
		static function get_codes_for_order_steps_to_include() {
			$newArray = array();
			$array = self::get_order_steps_to_include();
			if($array && count($array)) {
				foreach($array as $className) {
					$code = singleton($className)->getMyCode();
					$newArray[$className] = strtoupper($code);
				}
			}
			return $newArray;
		}
		function getMyCode() {
			$array = Object::uninherited_static($this->ClassName, 'defaults');
			if(!isset($array["Code"])) {user_error($this->class." does not have a default code specified");}
			return $array["Code"];
		}

	//IMPORTANT:: MUST HAVE Code defined!!!
	public static $defaults = array(
		"CanEdit" => 0,
		"CanCancel" => 0,
		"ShowAsUncompletedOrder" => 0,
		"ShowAsInProcessOrder" => 0,
		"ShowAsCompletedOrder" => 0,
		"Code" => "ORDERSTEP"
	);

	function populateDefaults() {
		parent::populateDefaults();
		foreach(self::$defaults as $field => $value) {
			$this->$field = $value;
		}
	}

	function getCMSFields() {
		//TO DO: add warning messages and break up fields
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Main", new HeaderField("WARNING1", _t("OrderStep.CAREFUL", "CAREFUL! please edit with care"), 1), "Name");
		$fields->addFieldToTab("Root.Main", new DropdownField("ClassName", _t("OrderStep.TYPE", "Type"), self::get_codes_for_order_steps_to_include()), "Name");
		$fields->addFieldToTab("Root.Main", new HeaderField("WARNING2", _t("OrderStep.CUSTOMERCANCHANGE", "What can be changed?"), 3), "CanEdit");
		$fields->addFieldToTab("Root.Main", new HeaderField("WARNING5", _t("OrderStep.ORDERGROUPS", "Order groups for customer?"), 3), "ShowAsUncompletedOrder");
		$fields->replaceField("Code", $fields->dataFieldByName("Code")->performReadonlyTransformation());
		if($this->isDefaultStatusOption()) {
			$fields->replaceField("Code", $fields->dataFieldByName("Code")->performReadonlyTransformation());
		}
		return $fields;
	}

	function validate() {
		$result = DataObject::get_one(
			"OrderStep",
			" (\"Name\" = '".$this->Name."' OR \"Code\" = '".strtoupper($this->Code)."') AND \"OrderStep\".\"ID\" <> ".intval($this->ID));
		if($result) {
			return new ValidationResult((bool) ! $result, _t("OrderStep.ORDERSTEPALREADYEXISTS", "An order status with this name already exists. Please change the name and try again."));
		}
		$result = (bool)($this->ClassName == "OrderStep");
		if($result) {
			return new ValidationResult((bool) ! $result, _t("OrderStep.ORDERSTEPCLASSNOTSELECTED", "You need to select the right order status class."));
		}
		return parent::validate();
	}


/**************************************************
* moving between statusses...
**************************************************/
	/**
  	*initStep:
  	* makes sure the step is ready to run.... (e.g. check if the order is ready to be emailed as receipt).
	* should be able to run this function many times to check if the step is ready
  	*@param Order object
  	*@return Boolean - true if run correctly
  	**/
	public function initStep($order) {
		user_error("Please implement this in a subclass of OrderStep", E_USER_WARNING);
		return true;
	}
	/**
  	*doStep:
	* should only be able to run this function one (init stops you from running it twice - in theory....)
  	*runs the actual step
  	*@param Order object
  	*@return Boolean - true if run correctly
  	**/
	public function doStep($order) {
		user_error("Please implement this in a subclass of OrderStep", E_USER_WARNING);
		return true;
	}
	/**
  	*nextStep:
  	*runs the actual step
  	*@param Order object
  	*@return next step OrderStep object
  	**/
	public function nextStep($order) {
		$nextOrderStepObject = DataObject::get_one("OrderStep", "\"Sort\" > ".$this->Sort);
		if($nextOrderStepObject) {
			return $nextOrderStepObject;
		}
		return null;
	}



/**************************************************
* Boolean checks
**************************************************/

	public function canDelete($member = null) {
		if($order = DataObject::get_one("Order", "\"StatusID\" = ".$this->ID)) {
			return false;
		}
		if($this->isDefaultStatusOption()) {
			return false;
		}
		return true;
	}

	public function hasPassed($code, $orIsEqualTo = false) {
		$otherStatus = DataObject::get_one("OrderStep", "\"Code\" = '".$code."'");
		if($otherStatus) {
			if($otherStatus->Sort < $this->Sort) {
				return true;
			}
			if($orIsEqualTo && $otherStatus->Code == $this->Code) {
				return true;
			}
		}
		else {
			user_error("could not find $code in OrderStep", E_USER_NOTICE);
		}
		return false;
	}

	public function hasPassedOrIsEqualTo($code) {
		return $this->hasPassed($code, true);
	}

	public function hasNotPassed($code) {
		return (bool)!$this->hasPassed($code, true);
	}

	public function isBefore($code) {
		return (bool)!$this->hasPassed($code, false);
	}

	protected function isDefaultStatusOption() {
		return in_array($this->Code, self::get_codes_for_order_steps_to_include());
	}

	//EMAIL

	protected function hasBeenSent($order) {
		if(DataObject::get_one("OrderEmailRecord", "\"OrderEmailRecord\".\"OrderID\" = ".$order->ID." AND \"OrderEmailRecord\".\"OrderStepID\" = ".intval($this->ID)." AND  \"OrderEmailRecord\".\"Result\" = 1")) {
			return true;
		}
		return false;
	}

/**************************************************
* Silverstripe Standard Functions
**************************************************/


	function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->Code = strtoupper($this->Code);
	}

	function onAfterDelete() {
		parent::onAfterDelete();
		$this->requireDefaultRecords();
	}


	//USED TO BE: Unpaid,Query,Paid,Processing,Sent,Complete,AdminCancelled,MemberCancelled,Cart
	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$orderStepsToInclude = self::get_order_steps_to_include();
		$codesToInclude = self::get_codes_for_order_steps_to_include();
		if($orderStepsToInclude && count($orderStepsToInclude) && count($codesToInclude)) {
			foreach($codesToInclude as $className => $code) {
				if(!DataObject::get_one($className)) {
					if(!DataObject::get_one("OrderStep", "\"Code\" = '".strtoupper($code)."'")) {
						$obj = new $className();
						$obj->Code = strtoupper($obj->Code);
						$obj->write();
						DB::alteration_message("Created \"$code\" as $className.", "created");
					}
				}
			}
		}
	}
}

class OrderStep_Created extends OrderStep {

	public static $defaults = array(
		"Name" => "Create",
		"Code" => "CREATED",
		"Sort" => 10,
		"CanEdit" => 1,
		"CanCancel" => 1,
		"ShowAsUncompletedOrder" => 1
	);

	public function initStep($order) {
		return true;
	}

	public function doStep($order) {
		return true;
	}

	public function nextStep($order) {
		$nextOrderStepObject = parent::nextStep($order);
		if($order->Items()) {
			return $nextOrderStepObject;
		}
		return null;
	}


	function populateDefaults() {
		parent::populateDefaults();
		foreach(self::$defaults as $field => $value) {
			$this->$field = $value;
		}
	}
}

class OrderStep_Submitted extends OrderStep {

	static $defaults = array(
		"Name" => "Submit",
		"Code" => "SUBMITTED",
		"Sort" => 20,
		"ShowAsInProcessOrder" => 1
	);

	public function initStep($order) {
		if(!$order->Items()) {
			return false;
		}
		return true;
	}

	public function doStep($order) {
		if(!$order->MemberID && Member::currentUser()) {
			if(Member::currentUser()->IsShopAdmin) {
				$order->MemberID = Member::currentUserID();
				$order->write();
			}
		}
		if(!$order->MemberID) {
			return false;
		}
		return true;
	}

	public function nextStep($order) {
		$nextOrderStepObject = parent::nextStep($order);
		if($order->MemberID) {
			return $nextOrderStepObject;
		}
		return null;
	}

	function populateDefaults() {
		parent::populateDefaults();
		foreach(self::$defaults as $field => $value) {
			$this->$field = $value;
		}
	}

}



class OrderStep_SentInvoice extends OrderStep {

	static $db = array(
		"SendInvoiceToCustomer" => "Boolean"
	);

	public static $defaults = array(
		"Name" => "Send invoice",
		"Code" => "INVOICED",
		"Sort" => 25,
		"ShowAsInProcessOrder" => 1,
		"SendInvoiceToCustomer" => 1
	);

	public function initStep($order) {
		return true;
	}

	public function doStep($order) {
		if($this->SendInvoiceToCustomer){
			if(!$this->hasBeenSent($order)) {
				return $order->sendInvoice();
			}
		}
		return true;
	}

	public function nextStep($order) {
		$nextOrderStepObject = parent::nextStep($order);
		if(!$this->SendInvoiceToCustomer || $this->hasBeenSent($order)) {
			return $nextOrderStepObject;
		}
		return null;
	}


	function populateDefaults() {
		parent::populateDefaults();
		foreach(self::$defaults as $field => $value) {
			$this->$field = $value;
		}
	}


}

class OrderStep_Paid extends OrderStep {

	public static $defaults = array(
		"Name" => "Pay",
		"Code" => "PAID",
		"Sort" => 30,
		"ShowAsInProcessOrder" => 1
	);

	public function initStep($order) {
		return true;
	}

	public function doStep($order) {
		return true;
	}

	public function nextStep($order) {
		$nextOrderStepObject = parent::nextStep($order);
		if($order->IsPaid()) {
			return $nextOrderStepObject;
		}
		return null;
	}


	function populateDefaults() {
		parent::populateDefaults();
		foreach(self::$defaults as $field => $value) {
			$this->$field = $value;
		}
	}
}


class OrderStep_SentReceipt extends OrderStep {

	static $db = array(
		"SendReceiptToCustomer" => "Boolean"
	);

	public static $defaults = array(
		"Name" => "Send receipt",
		"Code" => "RECEIPTED",
		"Sort" => 35,
		"ShowAsInProcessOrder" => 1,
		"SendReceiptToCustomer" => 1
	);

	public function initStep($order) {
		return true;
	}


	public function doStep($order) {
		if($this->SendReceiptToCustomer){
			if(!$this->hasBeenSent($order)) {
				return $order->sendReceipt();
			}
		}
		return true;
	}

	public function nextStep($order) {
		$nextOrderStepObject = parent::nextStep($order);
		if(!$this->SendReceiptToCustomer || $this->hasBeenSent($order)) {
			return $nextOrderStepObject;
		}
		return null;
	}


	function populateDefaults() {
		parent::populateDefaults();
		foreach(self::$defaults as $field => $value) {
			$this->$field = $value;
		}
	}

}


class OrderStep_Confirmed extends OrderStep {

	public static $defaults = array(
		"Name" => "Confirm",
		"Code" => "CONFIRMED",
		"Sort" => 40,
		"ShowAsInProcessOrder" => 1
	);

	public function initStep($order) {
		if(!$order->ReceiptSent){
			$order->sendReceipt();
		}
	}

	public function doStep($order) {
		return true;
	}

	public function nextStep($order) {
		$nextOrderStepObject = parent::nextStep($order);
		if($order->HasPositivePaymentCheck()) {
			return $nextOrderStepObject;
		}
		return null;
	}


	function populateDefaults() {
		parent::populateDefaults();
		foreach(self::$defaults as $field => $value) {
			$this->$field = $value;
		}
	}
}

class OrderStep_Sent extends OrderStep {

	public static $defaults = array(
		"Name" => "Send order",
		"Code" => "SENT",
		"Sort" => 50,
		"ShowAsCompletedOrder" => 1
	);

	public function initStep($order) {
		return true;
	}

	public function doStep($order) {
		return true;
	}

	public function nextStep($order) {
		$nextOrderStepObject = parent::nextStep($order);
		if($order->HasDispatchRecord()) {
			return $nextOrderStepObject;
		}
		return null;
	}
	function populateDefaults() {
		parent::populateDefaults();
		foreach(self::$defaults as $field => $value) {
			$this->$field = $value;
		}
	}
}


