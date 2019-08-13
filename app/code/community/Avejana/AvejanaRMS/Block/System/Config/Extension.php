<?php

class Avejana_AvejanaRMS_Block_System_Config_Extension
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<h5 style="margin:15px 0;">' . Mage::helper('avejanarms')->__('You will find these details in your AveJana Account Extension Settings. If you have not signed up with AveJana then ') . ' <a target="_blank" href="https://avejana.com/contact-avejana/" >' . Mage::helper('avejanarms')->__('Sign up Now!' ) . '</a> </h5>';

        return $html;
    }
}