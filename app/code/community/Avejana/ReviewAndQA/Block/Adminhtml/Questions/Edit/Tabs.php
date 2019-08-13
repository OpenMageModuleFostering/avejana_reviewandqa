<?php
class Avejana_ReviewAndQA_Block_Adminhtml_Questions_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('questions_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('reviewandqa')->__('General'));
    }
    
    protected function _prepareLayout()
    {
        /*$this->getLayout()->getBlock('head')
            ->addJs('pws/relatedproductsets/productLink.js');*/

        parent::_prepareLayout();
    }
   

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('reviewandqa')->__('General'),
            'title'     => Mage::helper('reviewandqa')->__('General'),
            'content'   => $this->getLayout()->createBlock('reviewandqa/adminhtml_questions_edit_tab_form')->toHtml(),
        ));
        
       
        return parent::_beforeToHtml();
    }
}
