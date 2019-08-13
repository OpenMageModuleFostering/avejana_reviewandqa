<?php

class Avejana_ReviewAndQA_Block_Adminhtml_Orderfldmap_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('customerfldmap_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('reviewandqa')->__('Order Fields Mapping'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('reviewandqa')->__('Order Fields Mapping'),
          'title'     => Mage::helper('reviewandqa')->__('Order Fields Mapping'),
          'content'   => $this->getLayout()->createBlock('reviewandqa/adminhtml_orderfldmap_edit_tab_form')->toHtml(),
      ));
      /*
      $this->addTab('form_section_2', array(
          'label'     => Mage::helper('reviewandqa')->__('Customer Fields Mapping 2'),
          'title'     => Mage::helper('reviewandqa')->__('Customer Fields Mapping 2'),
          'content'   => $this->getLayout()->createBlock('reviewandqa/adminhtml_productfldmap_edit_tab_form')->toHtml(),
      ));
      */
     
      return parent::_beforeToHtml();
  }
}