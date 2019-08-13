<?php

class Avejana_ReviewAndQA_Block_Adminhtml_Orderfldmap_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {	
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('orderfldmap_form', array('legend'=>Mage::helper('reviewandqa')->__('Orders Fields Mapping')));
      $Mage_fld = Mage::getSingleton('reviewandqa/orderfldmap')->getMageFieldsArray();
      $Erp_fld = Mage::getSingleton('reviewandqa/orderfldmap')->getErpFieldsArray();	
      
      $registrRecord = Mage::registry('reviewandqa_data')->getData();
      //print_r($registrRecord);exit;
      //$selectVal = Mage::getModel('reviewandqa/customerfldmap')->load($recordId['id'])->getData('add_type');
      
      $Mage_fld_selected = $registrRecord['mage_field_of'].' : '.$registrRecord['mage_field_label'].' : '.$registrRecord['mage_field'];
      //$Erp_fld_selected = $registrRecord['sf_field_of'].' : '.$registrRecord['sf_field_label'].' : '.$registrRecord['sf_field'];
      $Erp_fld_selected = $registrRecord['sf_field'];
      $Defualt_value = $registrRecord['default_value'];
      
		/*
      	if($this->getRequest()->getParam('entity_id')){
	  
	  	}
	  	*/
     	
		$fieldset->addField('mage_field', 'select', array(
          'label'     => Mage::helper('reviewandqa')->__('Mage Fields:'),
		  'class'     => 'required-entry',
		  'name'	=>'mage_field',
		  'values'    => $Mage_fld,
		  'value'    => $Mage_fld_selected
		));
		
		$fieldset->addField('sf_field', 'text', array(
		  'label'     => Mage::helper('reviewandqa')->__('Avejana Fields:'),
		  'class'     => 'required-entry',
		  'name'	  =>'sf_field',
		 'value'    => $Erp_fld_selected
		));	
		
		$fieldset->addField('default_value', 'text', array(
		  'label'     => Mage::helper('reviewandqa')->__('Default Values:'),
		  'name'	  =>'default_value',
		  'value'    => $Defualt_value
		));
			
	/*	
     if ( Mage::getSingleton('adminhtml/session')->get<Module>Data() ){
	      $form->setValues(Mage::getSingleton('adminhtml/session')->get<Module>Data());
	      Mage::getSingleton('adminhtml/session')->set<Module>Data(null);
	 } elseif ( Mage::registry('<module>_data') ) {
	     $form->setValues(Mage::registry('<module>_data')->getData());
	 }
	 
	*/
	/*
	if ( Mage::getSingleton('adminhtml/session')->getMagerpsyncData() )
	{
		$form->setValues(Mage::getSingleton('adminhtml/session')->getMagerpsyncData());
		Mage::getSingleton('adminhtml/session')->getMagerpsyncData(null);
	} elseif ( Mage::registry('reviewandqa_data') ) {
	  	$form->setValues(Mage::registry('reviewandqa_data')->getData());
	}
	*/
      return parent::_prepareForm();
  }
}