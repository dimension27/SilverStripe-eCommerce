<?php

/**
 * @authors: Silverstripe, Jeremy, Nicolaas
 *
 **/

class CartResponse extends EcommerceResponse {


	/**
	 * Builds json object to be returned via ajax.
	 *
	 *@return JSON
	 **/
	public function ReturnCartData($status, $message = "", $data = null) {
		$this->addHeader('Content-Type', 'application/json');
		if($status != "success") {
			$this->setStatusCode(400, "not successfull: ".$status." --- ".$message);
		}
		$currentOrder = ShoppingCart::current_order();
		$currentOrder->calculateModifiers($force = true);
		$js = array ();
		if ($items = $currentOrder->Items()) {
			foreach ($items as $item) {
				$item->updateForAjax($js);
			}
		}
		if ($modifiers = $currentOrder->Modifiers()) {
			foreach ($modifiers as $modifier) {
				$modifier->updateForAjax($js);
			}
		}
		$currentOrder->updateForAjax($js);
		if($message) {
			$js[] = array(
				"id" => $currentOrder->TableMessageID(),
				"parameter" => "innerHTML",
				"value" => $message,
				"isOrderMessage" => true,
				"messageClass" => $status
			);
			$js[] = array(
				"id" => $currentOrder->TableMessageID(),
				"parameter" => "hide",
				"value" => 0
			);
		}
		else {
			$js[] = array(
				"id" => $currentOrder->TableMessageID(),
				"parameter" => "hide",
				"value" => 1
			);
		}
		return Convert::array2json($js);
	}

}
