<?php

/**
 * @Description: DataObject recording all order emails sent.
 *
 *
 * @authors: Silverstripe, Jeremy, Nicolaas
 *
 * @package: ecommerce
 * @sub-package: email
 *
 **/

class OrderEmailRecord extends DataObject {

	public static $db = array(
		"From" => "Varchar(255)",
		"To" => "Varchar(255)",
		"Subject" => "Varchar(255)",
		"Content" => "HTMLText",
		"Result" => "Boolean"
	);
	public static $has_one = array(
		"Order" => "Order",
		"OrderStep" => "OrderStep"
	);
	public static $casting = array(
		"RelatedStatus" => "Varchar",
		"ResultNice" => "Varchar"
	);
	public static $summary_fields = array(
		"Created" => "Send",
		"RelatedStatus" => "What",
		"From" => "From",
		"To" => "To",
		"Subject" => "Subject",
		"ResultNice" => "Sent Succesfully"
	);
	public static $searchable_fields = array(
		'OrderID' => array(
			'field' => 'NumericField',
			'title' => 'Order Number'
		),
		"From" => "PartialMatchFilter",
		"To" => "PartialMatchFilter",
		"Subject" => "PartialMatchFilter",
		"Result" => true
	);

	function ResultNice() {if($this->Result) {return _t("OrderEmailRecord.YES", "Yes");}return _t("OrderEmailRecord.NO", "No");}

	public static $singular_name = "Customer Email";
		function i18n_singular_name() { return _t("OrderEmailRecord.CUSTOMEREMAIL", "Customer Email");}

	public static $plural_name = "Customer Emails";
		function i18n_plural_name() { return _t("OrderEmailRecord.CUSTOMEREMAILS", "Customer Emails");}
	//CRUD settings
	public function canCreate($member = null) {return false;}
	public function canEdit($member = null) {return false;}
	public function canDelete($member = null) {return false;}
	//defaults
	public static $default_sort = "\"Created\" DESC";


	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->replaceField("OrderID", $fields->dataFieldByName("OrderID")->performReadonlyTransformation());
		return $fields;
	}

	function scaffoldSearchFields(){
		$fields = parent::scaffoldSearchFields();
		$fields->replaceField("OrderID", new NumericField("OrderID", "Order Number"));
		return $fields;
	}

	/**
	 *
	 *@ return String
	  **/
	function RelatedStatus() {
		if($this->OrderStepID) {
			$orderStep = DataObject::get_by_id("OrderStep", $this->OrderStepID);
			if($orderStep) {
				return $orderStep->Title;
			}
		}
	}


}
