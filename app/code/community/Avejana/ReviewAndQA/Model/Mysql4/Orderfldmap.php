<?php
class Avejana_ReviewAndQA_Model_Mysql4_Orderfldmap extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the entity_id refers to the key field in your database table.
        $this->_init('reviewandqa/orderfldmap', 'entity_id');
    }
}