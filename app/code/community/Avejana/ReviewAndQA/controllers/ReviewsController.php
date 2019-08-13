<?php
class Avejana_ReviewAndQA_ReviewsController extends Mage_Core_Controller_Front_Action{
	public function indexAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
}