<?php
class Avejana_ReviewAndQA_Model_Questions extends Mage_Core_Model_Abstract{
	protected function _construct(){
		$this->_init('reviewandqa/questions');
	}
    
	public function loadExtra($id){
		$collection = $this->getCollection()
		->joinProducts()
		->joinStore()
		->addFieldToFilter('questions_id', $id);
        
		if($collection->getSize()){
			return $collection->getFirstItem();
		}  
        
		return false;
	}
}
