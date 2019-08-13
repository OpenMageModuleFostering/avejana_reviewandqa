<?php
class Avejana_ReviewAndQA_Block_Adminhtml_Questions_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('questionsGrid');
        $this->setDefaultSort('created_on');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $store = $this->_getStore();

        $collection = Mage::getModel('reviewandqa/questions')
                            ->getCollection()
                            ->joinProducts()
                            ->setOrder('answered_on', 'ASC')
                            ->setOrder('created_on', 'ASC')
                      ;

        if ($store->getId()) {
            $collection->addFieldToFilter('main_table.store_id', $store->getId());
        }

        //echo $collection->getSelectSql();

        $this->setCollection($collection);
        $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);

        // make it possible to sort by product_name
        if ($columnId == 'product_name' ) {
            $this->_preparePage();

            $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
            $dir      = $this->getParam($this->getVarNameDir(), $this->_defaultDir);


            if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
                $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
                $this->_columns[$columnId]->setDir($dir);
                $collection->getSelect()->order('IFNULL(_table_product_name.value,_table_product_name2.value) '.$dir);
            }

        } 

        parent::_prepareCollection();

        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('questions_id', array(
            'header'    => Mage::helper('reviewandqa')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'questions_id',
        ));

        $this->addColumn('created_on', array(
            'header'    => Mage::helper('reviewandqa')->__('Added On'),
            'align'     =>'left',
            'type'		=> 'datetime',
            'index'     => 'created_on',
        ));

        $this->addColumn('product_name', array(
            'header'    => Mage::helper('reviewandqa')->__('Product Name'),
            'align'     =>'left',
            'index'     => 'product_name',
            'filter_condition_callback' => array($this, '_filterProductNameCondition')
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('reviewandqa')->__('Name'),
            'align'     =>'left',
            'index'     => 'name',
        ));

        $this->addColumn('email', array(
            'header'    => Mage::helper('reviewandqa')->__('Email'),
            'align'     =>'left',
            'index'     => 'email',
        ));

        $this->addColumn('question', array(
            'header'    => Mage::helper('reviewandqa')->__('Question'),
            'align'     =>'left',
            'index'     => 'question',
        ));


        $this->addColumn('answered_on', array(
            'header'    => Mage::helper('reviewandqa')->__('Answered On'),
            'align'     =>'left',
            'index'     => 'answered_on',
            'type'		=> 'datetime',
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('reviewandqa')->__('Status'),
            'align'     =>'left',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array('public'=>'public','hidden'=>'hidden'),
        ));

        return parent::_prepareColumns();
    }


    protected function _filterProductNameCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->getSelect()
            ->where('`_table_product_name`.`value` like ?', '%'.$value.'%')
            ->orWhere('`_table_product_name2`.`value` like ?', '%'.$value.'%');
    }


    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('questions_id');
        $this->getMassactionBlock()->setFormFieldName('questions');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('reviewandqa')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('reviewandqa')->__('Are you sure?')
        ));


        return $this;
    }


    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }



}
