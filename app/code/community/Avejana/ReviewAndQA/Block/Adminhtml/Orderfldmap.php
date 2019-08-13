<?php
class Avejana_ReviewAndQA_Block_Adminhtml_Orderfldmap extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
  	
    $this->_controller = 'adminhtml_orderfldmap';
    $this->_blockGroup = 'reviewandqa';
    $this->_headerText = Mage::helper('reviewandqa')->__('Orders Fields Mapping');
    $this->_addButtonLabel = Mage::helper('reviewandqa')->__('Add New Field');
    /*$this->_addButton('sync_product', array(
    	'label'   => Mage::helper('reviewandqa')->__('Update Normal Products On Salesforce'),
    	'class'   => 'update_normal_products save'
    ));*/
    parent::__construct();
    //$this->removeButton('add');
  }
}