<?php 

class Avejana_AvejanaRMS_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{   
    public function validateAction()
    {
		echo 'zzzzzz';

		print_r($this->getRequest()->getPost());die;
		
		Mage::log('Test: action is working!'); //To check if action is working
        //do something here...
    }
}