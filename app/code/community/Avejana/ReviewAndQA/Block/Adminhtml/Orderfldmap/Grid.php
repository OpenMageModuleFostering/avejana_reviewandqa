<?php

class Avejana_ReviewAndQA_Block_Adminhtml_Orderfldmap_Grid extends Mage_Adminhtml_Block_Widget_Grid{
	public function __construct(){	
		parent::__construct();
		$this->setId('OrderfldmapGrid');
		$this->setDefaultSort('entity_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection(){
		
		
		$collection =	Mage::getModel('reviewandqa/orderfldmap')->getCollection();
		//print_r($collection->getData());exit;
		$prefix = Mage::getConfig()->getTablePrefix();
		$this->setCollection($collection);
    	
		return parent::_prepareCollection();
	}

	protected function _prepareColumns(){
		$this->addColumn('entity_id', array(
				'header'    => Mage::helper('reviewandqa')->__('Mapping ID'),
				'align'     =>'left',
				'width'     => '100px',
				'filter_index' => 'entity_id',
				'index'     => 'entity_id',
			));

		$this->addColumn('mage_field',array(
				'header'  =>Mage::helper('reviewandqa')->__('Magento Field'),
				'align'   =>'left',
				'type'    => 'concat',    
				'index'   => array('mage_field_of', 'mage_field_label'),
				'filter_index' => 'mage_field',
				'separator'    => ' : ',
			));
			
		$this->addColumn('sf_field',array(
				'header'  =>Mage::helper('reviewandqa')->__('Avejana Field'),
				'align'   =>'left',	
				'index'   => 'sf_field',
				'filter_index' => 'sf_field',
				
			));

		$this->addColumn('default_value', array(
				'header'     => Mage::helper('reviewandqa')->__('Default Values:'),
				'align'   =>'left',	
				'index'   => 'default_value',
				'filter_index' => 'default_value',
			));	
	
		$this->addColumn(
			'action',
			array(
				'header'    => Mage::helper('reviewandqa')->__('Action'),
				'width'     => '50px',
				'type'      => 'action',
				'getter'    => 'getId',
				'actions'   => array(
					array(
						'caption' => Mage::helper('reviewandqa')->__('Edit'),
						'url'     => array('base' => '*/*/edit'),
						'field'   => 'entity_id'
					)
				),
				'filter'    => false,
				'sortable'  => false,
				'index'     => 'entity_id',
				'is_system' => true,
			)
		);
		$this->setAdditionalJavaScript($this->getScripts());

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction(){
		$this->setMassactionIdField('reviewandqa_id');
		$this->getMassactionBlock()->setFormFieldName('reviewandqa');

		$this->getMassactionBlock()->addItem('delete', array(
				'label'    => Mage::helper('reviewandqa')->__('Delete'),
				'url'      => $this->getUrl('*/*/massDelete'),
				'confirm'  => Mage::helper('reviewandqa')->__('Are you sure?')
			));
        
		return $this;
	}

	public function getRowUrl($row)
	{
	return $this->getUrl('*/*/edit', array('entity_id' => $row->getId()));
	}
	

}