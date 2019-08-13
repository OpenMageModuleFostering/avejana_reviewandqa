<?php

class Avejana_ReviewAndQA_Model_Mysql4_Orderfldmap_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('reviewandqa/orderfldmap');
    }
}