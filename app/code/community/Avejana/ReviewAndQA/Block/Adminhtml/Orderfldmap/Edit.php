<?php

class Avejana_ReviewAndQA_Block_Adminhtml_Orderfldmap_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'reviewandqa';
        $this->_controller = 'adminhtml_orderfldmap';
        
        $this->_updateButton('save', 'label', Mage::helper('reviewandqa')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('reviewandqa')->__('Delete Item'));

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('payment_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'reviewandqa_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'reviewandqa_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('reviewandqa_data') && Mage::registry('reviewandqa_data')->getId() ) {
            return Mage::helper('reviewandqa')->__("Edit Item %s", $this->htmlEscape(Mage::registry('reviewandqa_data')->getTitle()));
        } else {
            return Mage::helper('reviewandqa')->__('Order Fields Mapping');
        }
    }
}