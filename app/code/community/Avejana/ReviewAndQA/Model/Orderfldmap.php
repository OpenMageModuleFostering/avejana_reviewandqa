<?php

class Avejana_ReviewAndQA_Model_Orderfldmap extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('reviewandqa/orderfldmap');
    }
    
    public function getMageFieldsArray() {
        
	    $attributeArray = array();
		$attributeArray[] = array(
            'label' => 'Select a Magento Field',
            'value' => ''
        );
	    
    	$resource = Mage::getSingleton('core/resource');
	   	$readConnection = $resource->getConnection('core_read');  
	    $tableName = $resource->getTableName('sales/order');
		$tablefields = $readConnection->describeTable($tableName);
		foreach($tablefields as $tablefield){
			$fieldCode = $tablefield['COLUMN_NAME'];
	    	$fieldLabel = $this->getFieldsLabel($fieldCode);
	        $attributeArray[] = array(
                'label' => 'Order : '.$fieldLabel,
                'value' => 'Order : '.$fieldLabel.' : '.$fieldCode
            );
	    }	
	    
	    $tableName = $resource->getTableName('sales/order_item');
		$tablefields = $readConnection->describeTable($tableName);
		foreach($tablefields as $tablefield){
			$fieldCode = $tablefield['COLUMN_NAME'];
	    	$fieldLabel = $this->getFieldsLabel($fieldCode);
	        $attributeArray[] = array(
                'label' => 'Product : '.$fieldLabel,
                'value' => 'Product : '.$fieldLabel.' : '.$fieldCode
            );
	    }
	    $tableName = $resource->getTableName('sales/order_payment');
		$tablefields = $readConnection->describeTable($tableName);
		foreach($tablefields as $tablefield){
			$fieldCode = $tablefield['COLUMN_NAME'];
	    	$fieldLabel = $this->getFieldsLabel($fieldCode);
	        $attributeArray[] = array(
                'label' => 'Payment : '.$fieldLabel,
                'value' => 'Payment : '.$fieldLabel.' : '.$fieldCode
            );
	    }
        /*
        $attributes = Mage::getModel('customer/entity_attribute_collection')->addVisibleFilter();
    	foreach ($attributes as $attribute) {
            if (($attLabel = $attribute->getFrontendLabel()))
            	$attCode = $attribute->getData('attribute_code');
                //$result[$attribute->getId()] = $label;
                $attributeArray[] = array(
	                'label' => 'Customer : '.$attLabel,
	                'value' => 'Customer : '.$attLabel.' : '.$attCode
	            );
        }
        */
         
	    $tableName = $resource->getTableName('sales/order_address');
		$tablefields = $readConnection->describeTable($tableName);
		foreach($tablefields as $tablefield){
			$fieldCode = $tablefield['COLUMN_NAME'];
	    	$fieldLabel = $this->getFieldsLabel($fieldCode);
	        $attributeArray[] = array(
                'label' => 'Billing : '.$fieldLabel,
                'value' => 'Billing : '.$fieldLabel.' : '.$fieldCode
            );
	    }
	    
	    foreach($tablefields as $tablefield){
			$fieldCode = $tablefield['COLUMN_NAME'];
	    	$fieldLabel = $this->getFieldsLabel($fieldCode);
	        $attributeArray[] = array(
                'label' => 'Shipping : '.$fieldLabel,
                'value' => 'Shipping : '.$fieldLabel.' : '.$fieldCode
            );
	    }
        /*
    	$attributes = Mage::getResourceModel('customer/address_attribute_collection')->addVisibleFilter();
        foreach ($attributes as $attribute) {
            if (($attLabel = $attribute->getFrontendLabel()))
            	$attCode = $attribute->getData('attribute_code');
                //$result[$attribute->getId()] = $label;
                $attributeArray[] = array(
	                'label' => 'Billing : '.$attLabel,
	                'value' => 'Billing : '.$attLabel.' : '.$attCode
	            );
        }
        
        foreach ($attributes as $attribute) {
            if (($attLabel = $attribute->getFrontendLabel()))
            	$attCode = $attribute->getData('attribute_code');
                //$result[$attribute->getId()] = $label;
                $attributeArray[] = array(
	                'label' => 'Shipping : '.$attLabel,
	                'value' => 'Shipping : '.$attLabel.' : '.$attCode
	            );
        }
        */
	    return $attributeArray;
    }
    
    public function getErpFieldsArray() {
    	
    	$attributeArray[] = array(
            'label' => 'Select a Avejana Field',
            'value' => ''
        );
       
		//Mage::helper('reviewandqa/connection')->getSocketConnect();
        //$sfdc = Mage::getSingleton('adminhtml/session')->getSFDC();
        //$sfdc = unserialize(serialize($sfdc));
        //print_r($sfdc);
        /*
        $sf_order_object = Mage::getStoreConfig('salesconfig/order_integration/integ_type');
        $stuff = $sfdc->describeSObject($sf_order_object);
		$fields = get_object_vars($stuff);
    	foreach ($fields['fields'] as $field){
		    $keys=array_keys(get_object_vars($field));
		    
		    $f = get_object_vars($field);
		    $attributeArray[] = array(
	            'label' => $sf_order_object.' : '.$f['label'],
	            'value' => $sf_order_object.' : '.$f['label'].' : '.$f['name']
	        );

		}
		*/
		/*
		$stuff = $sfdc->describeSObject('Contact');
		$fields = get_object_vars($stuff);
    	foreach ($fields['fields'] as $field){
		    $keys = array_keys(get_object_vars($field));
		    
		    $f = get_object_vars($field);
		    $attributeArray[] = array(
	            'label' => 'Contact : '.$f['label'],
	            'value' => 'Contact : '.$f['label'].' : '.$f['name']
	        );

		}
		
		$stuff = $sfdc->describeSObject('Billing_and_Project_Address__c');
		$fields = get_object_vars($stuff);
    	foreach ($fields['fields'] as $field){
		    $keys = array_keys(get_object_vars($field));
		    
		    $f = get_object_vars($field);
		    $attributeArray[] = array(
	            'label' => 'SFCA : '.$f['label'],
	            'value' => 'Billing : '.$f['label'].' : '.$f['name']
	        );

		}
		*/
		
        return $attributeArray;
        
    }
    
    public function getFieldsLabel($fieldname) {
    	$fieldname_array = explode("_",$fieldname);
    	$fieldlable_array = array();
    	
    	foreach($fieldname_array as $fieldname_block){
			$fieldlabel_array[] = ucfirst($fieldname_block);
		}
		$fieldlabel = implode(" ", $fieldlabel_array);
		return $fieldlabel;
		
    }
}