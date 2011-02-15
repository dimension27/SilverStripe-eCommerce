<?php


/**
 * @Description: Email spefically for communicating with customer about order.
 * @package: ecommerce
 * @authors: Silverstripe, Jeremy, Nicolaas
 **/

class Order_Email extends Email {

	protected static $send_all_emails_plain = false;
		function set_send_all_emails_plain($b) {self::$send_all_emails_plain = $b;}
		function get_send_all_emails_plain() {return self::$send_all_emails_plain;}

	protected static $css_file_location = "ecommerce/css/EcommerceEmailReceiptStyle.css";
		function set_css_file_location($s) {self::$css_file_location = $s;}
		function get_css_file_location() {return self::$css_file_location;}

	protected static $copy_to_admin_for_all_emails = true;
		function set_copy_to_admin_for_all_emails($b) {self::$copy_to_admin_for_all_emails = $b;}
		function get_copy_to_admin_for_all_emails() {return self::$copy_to_admin_for_all_emails;}

	public function send($messageID = null, $order, $resend = false) {
		if(!$this->hasBeenSent($order) || $resend) {
			if(self::get_copy_to_admin_for_all_emails()) {
				$this->setBcc(Email::getAdminEmail());
			}
			if(self::get_send_all_emails_plain()) {
				$result = parent::sendPlain($messageID);
			}
			else {
				$result = parent::send($messageID);
			}
			$this->createRecord($result, $order);
			return $result;
		}
	}

	protected function createRecord($result, $order) {
		$obj = new OrderEmailRecord();
		$obj->From = $this->from;
		$obj->To = $this->to;
		$obj->Subject = $this->subject;
		$obj->Content = $this->body;
		$obj->Result = $result ? 1 : 0;
		$obj->OrderID = $order->ID;
		$obj->OrderStepID = $order->StatusID;
		if(Email::$send_all_emails_to) {
			$obj->To .= Email::$send_all_emails_to;
		}
		$obj->write();
	}

	function hasBeenSent($order) {
		return DataObject::get_one("OrderEmailRecord", "\"OrderEmailRecord\".\"OrderID\" = ".$order->ID." AND \"OrderEmailRecord\".\"OrderStepID\" = ".intval($order->StatusID)." AND  \"OrderEmailRecord\".\"Result\" = 1");
	}

	/**
	 * @author Mark Guinn
	 */

	protected function parseVariables($isPlain = false) {
		require_once(Director::baseFolder() . '/ecommerce/thirdparty/Emogrifier.php');
		parent::parseVariables($isPlain);
		// if it's an html email, filter it through emogrifier
		$cssFileLocation = Director::baseFolder()."/".self::get_css_file_location();
		$cssFileHandler = fopen($cssFileLocation, 'r');
		$css = fread($cssFileHandler,  filesize($cssFileLocation));
		fclose($cssFileHandler);
		$emog = new Emogrifier($this->body, $css);
		$this->body = $emog->emogrify();
	}


}
