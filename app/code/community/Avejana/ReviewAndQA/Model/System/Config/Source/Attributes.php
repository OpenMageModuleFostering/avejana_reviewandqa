<?php

class Avejana_ReviewAndQA_Adminhtml_Model_System_Config_Source_Attributes
{
    public function toOptionArray()
    {
        
        $collection = Mage::getResourceModel('eav/entity_attribute_collection')
			->setEntityTypeFilter( Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId() )
            ->load();

        $array = array();

        $array[] = array(
            'label' => 'Please select',
            'value' => ''
        );

        foreach ($collection as $attribute) {
			if($attribute->getIsVisible() && $attribute->getFrontendInput() == 'text' OR $attribute->getIsVisible() && $attribute->getFrontendInput() == 'textarea'):
            $array[] = array(
               'label' => $attribute->getFrontendLabel(),
               'value' => $attribute->getAttributeCode()
            );
			endif;
        }

        return $array;

    }
 }




