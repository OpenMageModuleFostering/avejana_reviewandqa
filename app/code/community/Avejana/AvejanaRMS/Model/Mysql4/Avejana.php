<?php
class Avejana_AvejanaRMS_Model_Mysql4_Avejana extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("avejanarms/avejana", "avejana_id");
    }
}