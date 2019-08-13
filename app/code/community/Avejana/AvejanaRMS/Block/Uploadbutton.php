<?php 
class Avejana_AvejanaRMS_Block_Uploadbutton extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('avejanarms/index/uploadsales'); 

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('scalable')
                    ->setLabel('Upload Sales Data')
                    ->setOnClick("setLocation('$url')")
                    ->toHtml();

        return $html;
    }
}