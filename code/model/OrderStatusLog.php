<?php
/**
 * @description:  Data class that records events for an order like "Payment Checked", "Cheque Cleaered", "Goods dispatched", etc...
 *
 *
 * @authors: Silverstripe, Jeremy, Nicolaas
 *
 * @package: ecommerce
 * @sub-package: cms
 *
 **/


class OrderStatusLog extends DataObject {

	public static $db = array(
		'Title' => 'Varchar(100)',
		'Note' => 'Text',
		'EmailCustomer' => 'Boolean',
		'EmailSent' => 'Boolean',
		'InternalUseOnly' => 'Boolean'
	);

	public static $has_one = array(
		'Author' => 'Member',
		'Order' => 'Order'
	);

	public static $casting = array(
		"CustomerNote" => "Text",
		"Type" => "Varchar",
		'EmailCustomerNice' => 'Varchar',
		'EmailSentNice' => 'Varchar',
		'InternalUseOnlyNice' => 'Varchar'
	);
	public static $summary_fields = array(
		"Created" => "Date",
		"Type" => "Type",
		"Title" => "Title",
		'EmailSentNice' => 'Email sent to customer',
		'InternalUseOnlyNice' => 'Internal use only'
	);

	public static $defaults = array(
		"InternalUseOnly" => true
	);

	function EmailCustomerNice() {if($this->EmailCustomer) { return _t("OrderStatusLog.YES", "Yes");} return _t("OrderStatusLog.No", "No");}
	function EmailSentNice() {if($this->EmailSent) { return _t("OrderStatusLog.YES", "Yes");} return _t("OrderStatusLog.No", "No");}
	function InternalUseOnlyNice() {if($this->InternalUseOnly) { return _t("OrderStatusLog.YES", "Yes");} return _t("OrderStatusLog.No", "No");}

	protected static $available_log_classes_array = array();
		static function set_available_log_classes_array(array $a) {self::$available_log_classes_array = $a;}
		static function get_available_log_classes_array() {return self::$available_log_classes_array;}

	/**
	*
	*@return Boolean
	**/
	public function canView($member = null) {
		if(!$member) {
			$member = Member::currentUser();
		}
		if($member) {
			if($member->IsShopAdmin()) {
				return true;
			}
		}
		if(!$this->InternalUseOnly) {
			if($this->Order()) {
				if($this->Order()->MemberID == $member->ID) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	*
	*@return Boolean
	**/
	public function canDelete($member = null) {
		return false;
	}

	/**
	*
	*@return Boolean
	**/
	public function canCreate($member = null) {
		return true;
	}

	/**
	*
	*@return Boolean
	**/
	public function canEdit($member = null) {
		if($o = $this->Order()) {
			return $o->canEdit($member);
		}
		return false;
	}

	public static $searchable_fields = array(
		'OrderID' => array(
			'field' => 'NumericField',
			'title' => 'Order Number'
		),
		"Title" => "PartialMatchFilter",
		"Note" => "PartialMatchFilter"
	);


	public static $singular_name = "Order Log Entry";
		function i18n_singular_name() { return _t("OrderStatusLog.ORDERLOGENTRY", "Order Log Entry");}

	public static $plural_name = "Order Log Entries";
		function i18n_plural_name() { return _t("OrderStatusLog.ORDERLOGENTRIES", "Order Log Entries");}

	public static $default_sort = "\"Created\" DESC";

	function populateDefaults() {
		parent::populateDefaults();
		$this->AuthorID = Member::currentUserID();
	}

	/**
	*
	*@return FieldSet
	**/
	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->replaceField("EmailSent", $fields->dataFieldByName("EmailSent")->performReadonlyTransformation());
		$fields->replaceField("AuthorID", $fields->dataFieldByName("AuthorID")->performReadonlyTransformation());
		if($this->OrderID) {
			$fields->replaceField("OrderID", $fields->dataFieldByName("OrderID")->performReadonlyTransformation());
		}
		if($this->InternalUseOnly) {
			$fields->removeByName("EmailCustomer");
		}
		$classes = ClassInfo::subclassesFor("OrderStatusLog");
		$dropdownArray = array();
		$availableLogs = self::get_available_log_classes_array();
		if(!is_array($availableLogs)) {
			$availableLogs = array();
		}
		if($classes) {
			foreach($classes as $className) {
				if(!count($availableLogs) || in_array($className, $availableLogs )) {
					$obj = singleton($className);
					if($obj) {
						$dropdownArray[$className] = $obj->i18n_singular_name();
					}
				}
			}
		}
		if(count($dropdownArray)) {
			$fields->addFieldToTab("Root.Main", new DropdownField("ClassName", "Type", $dropdownArray), "Title");
		}
		return $fields;
	}

	/**
	*
	*@return String
	**/
	function Type() {
		return $this->i18n_singular_name();
	}

	/**
	*
	*@return Fieldset
	**/
	function scaffoldSearchFields(){
		$fields = parent::scaffoldSearchFields();
		$fields->replaceField("OrderID", new NumericField("OrderID", "Order Number"));
		return $fields;
	}

	function onBeforeWrite() {
		parent::onBeforeWrite();
		if(!$this->OrderID && 1 == 2) {
			user_error("There is no order id for Order Status Log", E_USER_WARNING);
		}
		if(!$this->AuthorID && $m = Member::currentUser()) {
			$this->AuthorID = $m->ID;
		}
		if(!$this->Title) {
			$this->Title = "Order Update";
		}
		if($this->InternalUseOnly) {
			$this->EmailCustomer = 0;
		}
	}

	function onAfterWrite(){
		parent::onAfterWrite();
		if($this->EmailCustomer && !$this->EmailSent && !$this->InternalUseOnly) {
			$outcome = $this->order()->sendStatusChange($this->Title, $this->CustomerNote());
			if($outcome) {
				//can not do a proper write here for risk of ending up in a loop
				DB::query("UPDATE \"OrderStatusLog\" SET \"EmailSent\" = 1 WHERE  \"OrderStatusLog\".\"ID\" = ".$this->ID.";");
			}
			else {
				DB::query("UPDATE \"OrderStatusLog\" SET \"EmailCustomer\" = 0 WHERE  \"OrderStatusLog\".\"ID\" = ".$this->ID.";");
			}
		}
	}

	/**
	*
	*@return String
	**/
	function CustomerNote() {
		return $this->Note;
	}

}

class OrderStatusLog_Dispatch extends OrderStatusLog {

	public static $defaults = array(
		"InternalUseOnly" => false
	);

	public static $singular_name = "Order Log Dispatch Entry";
		function i18n_singular_name() { return _t("OrderStatusLog.ORDERLOGDISPATCHENTRY", "Order Log Dispatch Entry");}

	public static $plural_name = "Order Log Dispatch Entries";
		function i18n_plural_name() { return _t("OrderStatusLog.ORDERLOGDISPATCHENTRIES", "Order Log Dispatch Entries");}

	public function canDelete($member = null) {
		if(!$member) {
			$member = Member::currentMember();
		}
		if($member) {
			return $member->IsShopAdmin();
		}
	}


}
class OrderStatusLog_DispatchElectronicOrder extends OrderStatusLog_Dispatch {

	public static $db = array(
		'Link' => 'Text',
	);

	public static $singular_name = "Order Log Electronic Dispatch Entry";
		function i18n_singular_name() { return _t("OrderStatusLog.ORDERLOGELECTRONICDISPATCHENTRY", "Order Log Electronic Dispatch Entry");}

	public static $plural_name = "Order Log Electronic Dispatch Entries";
		function i18n_plural_name() { return _t("OrderStatusLog.ORDERLOGELECTRONICDISPATCHENTRIES", "Order Log Electronic Dispatch Entries");}

}

class OrderStatusLog_DispatchPhysicalOrder extends OrderStatusLog_Dispatch {

	public static $db = array(
		'DispatchedBy' => 'Varchar(100)',
		'DispatchedOn' => 'Date',
		'DispatchTicket' => 'Varchar(100)',
	);

	public static $indexes = array(
		"DispatchedOn" => true
	);

	public static $searchable_fields = array(
		'OrderID' => array(
			'field' => 'NumericField',
			'title' => 'Order Number'
		),
		"Title" => "PartialMatchFilter",
		"Note" => "PartialMatchFilter",
		"DispatchedBy" => "PartialMatchFilter",
		'DispatchTicket' => 'PartialMatchFilter'
	);

	public static $summary_fields = array(
		"DispatchedOn" => "Date",
		"OrderID" => "Order ID",
		"EmailCustomerNice" => "Customer Emailed",
		"EmailSentNice" => "Email Sent"
	);

	public static $singular_name = "Order Log Physical Dispatch Entry";
		function i18n_singular_name() { return _t("OrderStatusLog.ORDERLOGPHYSICALDISPATCHENTRY", "Order Log Physical Dispatch Entry");}

	public static $plural_name = "Order Log Physical Dispatch Entries";
		function i18n_plural_name() { return _t("OrderStatusLog.ORDERLOGPHYSICALDISPATCHENTRIES", "Order Log Physical Dispatch Entries");}


	public static $default_sort = "\"DispatchedOn\" DESC, \"Created\" DESC";

	function populateDefaults() {
		parent::populateDefaults();
		$sc = DataObject::get_one("SiteConfig");
		if($sc) {
			$this->Title = $sc->DispatchEmailSubject;
		}
		$this->DispatchedOn =  date('Y-m-d');
		$this->DispatchedBy =  Member::currentUserID();
	}

	/**
	*
	*@return FieldSet
	**/
	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->replaceField("EmailSent", $fields->dataFieldByName("EmailSent")->performReadonlyTransformation());
		$fields->replaceField("DispatchedOn", new TextField("DispatchedOn", "Dispatched on (Year - month - date): "));
		return $fields;
	}

	function scaffoldSearchFields(){
		$fields = parent::scaffoldSearchFields();
		$fields->replaceField("OrderID", new NumericField("OrderID", "Order Number"));
		return $fields;
	}

	function onBeforeWrite() {
		parent::onBeforeWrite();
		if(!$this->Title) {
			$sc = DataObject::get_one("SiteConfig");
			if($sc) {
				$this->Title = $sc->DispatchEmailSubject;
			}
		}
		if(!$this->DispatchedOn) {
			$this->DispatchedOn = DBField::create('Date', date('Y-m-d'));
		}
	}

	/**
	*
	*@return String
	*@To do: move formatting to template
	**/
	function CustomerNote() {
		return $this->renderWith("LogDispatchPhysicalOrderCustomerNote");
	}
}

/**
 *@Description: We use this payment check class to double check that payment has arrived against
 * the order placed.  We do this independently of Order as a double-check.  It is important
 * that we do this because the main risk in an e-commerce operation is a fake payment.
 * Any e-commerce operator may set up their own policies on what a payment check
 * entails exactly.  It could include a bank reconciliation or even a phone call to the customer.
 * it is important here that we do not add any payment details. Rather, all we have is a tickbox
 * to state that the checks have been run.

 **/
class OrderStatusLog_PaymentCheck extends OrderStatusLog {

	public static $defaults = array(
		"InternalUseOnly" => true
	);

	public static $db = array(
		'PaymentConfirmed' => "Boolean",
	);

	/**
	*
	*@return Boolean
	**/
	public function canDelete($member = null) {
		return false;
	}

	protected static $true_and_false_definitions = array(
		"yes" => 1,
		"no" => 0
	);
		static function set_true_and_false_definitions(array $a) {self::$true_and_false_definitions = $a;}
		static function get_true_and_false_definitions() {return self::$true_and_false_definitions;}

	public static $searchable_fields = array(
		'OrderID' => array(
			'field' => 'NumericField',
			'title' => 'Order Number'
		),
		"PaymentConfirmed" => true
	);

	public static $summary_fields = array(
		"Created" => "Date",
		"PaymentConfirmedNice" => "Payment Confirmed"
	);

	public static $casting = array(
		"PaymentConfirmedNice" => "Varchar"
	);

	function PaymentConfirmedNice() {if($this->PaymentConfirmed) {return _t("OrderStatusLog.YES", "yes");}return _t("OrderStatusLog.No", "no");}

	public static $singular_name = "Payment Confirmation";
		function i18n_singular_name() { return _t("OrderStatusLog.PAYMENTCONFIRMATION", "Payment Confirmation");}

	public static $plural_name = "Payment Confirmations";
		function i18n_plural_name() { return _t("OrderStatusLog.PAYMENTCONFIRMATIONS", "Payment Confirmations");}

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName("Title");
		$fields->removeByName("Note");
		$fields->addFieldsToTab('Root.Main', new CheckboxField("PaymentConfirmed", _t("OrderStatusLog.CONFIRMED", "Payment is confirmed")));
		return $fields;
	}

	function scaffoldSearchFields(){
		$fields = parent::scaffoldSearchFields();
		$fields->replaceField("OrderID", new NumericField("OrderID", "Order Number"));
		return $fields;
	}

	function onBeforeWrite() {
		parent::onBeforeWrite();
	}


	/**
	*
	*@return String
	**/
	function CustomerNote() {
		if($this->Author()) {
			if($this->PaymentConfirmed) {
				return _t("OrderStatus.PAYMENTCONFIRMEDBY", "Payment Confirmed by: ").$this->Author()->getTitle()." | ".$this->Created;
			}
			else {
				return _t("OrderStatus.PAYMENTDECLINEDBY", "Payment DECLINED by: ").$this->Author()->getTitle()." | ".$this->Created;
			}
		}
	}

}

