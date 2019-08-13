<?php
class Avejana_ReviewAndQA_Block_Adminhtml_Questions_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_controller = 'questions';

        $this->_updateButton('save', 'label', Mage::helper('reviewandqa')->__('Save Answer'));
        $this->_updateButton('delete', 'label', Mage::helper('reviewandqa')->__('Delete Question'));

    }

    public function getHeaderText()
    {
        if( Mage::registry('questions') && Mage::registry('questions')->getId() ) {
            return Mage::helper('reviewandqa')->__("Edit Answer", $this->htmlEscape(Mage::registry('questions')->getTitle()));
        } else {
            return Mage::helper('reviewandqa')->__('New Question');
        }
    }
    
    
    /*
    * Overrided method because the way the name of the block form is constructed is wrong for local/community modules
    * Eg: $this->_blockGroup . '/' . $this->_controller . '_' . $this->_mode . '_form' => adminhtml/questions_edit_form
    * we need 'reviewandqa/adminhtml_questions_edit_form'
    */    
    protected function _prepareLayout()
    { 
        if ($this->_blockGroup && $this->_controller && $this->_mode) {
            $this->setChild('form', $this->getLayout()->createBlock('reviewandqa/adminhtml_questions_edit_form'));
        }
        return parent::_prepareLayout();
    }
}
