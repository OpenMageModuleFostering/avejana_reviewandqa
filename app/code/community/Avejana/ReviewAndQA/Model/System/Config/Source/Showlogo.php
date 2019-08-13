<?php

class Avejana_ReviewAndQA_Model_System_Config_Source_Showlogo
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'top_left', 'label'=>Mage::helper('adminhtml')->__('Top left')),
            array('value'=>'top_center', 'label'=>Mage::helper('adminhtml')->__('Top center')),
            array('value'=>'top_right', 'label'=>Mage::helper('adminhtml')->__('Top right')),
            array('value'=>'bottom_left', 'label'=>Mage::helper('adminhtml')->__('Bottom left')),
            array('value'=>'bottom_center', 'label'=>Mage::helper('adminhtml')->__('Bottom center')),
            array('value'=>'bottom_right', 'label'=>Mage::helper('adminhtml')->__('Bottom right')),
        );
    }
}
