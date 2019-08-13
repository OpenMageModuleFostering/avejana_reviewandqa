<?php
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

	$write = Mage::getSingleton('core/resource')->getConnection('core_write');
	
	$ratings = Mage::getModel('rating/rating')->getCollection();
	foreach($ratings as $rating){
		$write->delete(
		"rating_store",
		"rating_id = '".$rating->getId()."' AND store_id != 0"
		);
	}   
	
	$stores = Mage::getModel('core/store')->getCollection()->addFieldToFilter('is_active', 1);

	//print_r($stores->getData());exit;  
	$ratingCodes = array();
	$store_ids = array();
	foreach($stores as $store){
		$store_ids[] = $store->getId();
		$ratingCodes[$store->getId()] = 'Overall Rating5';
	}
	
	$ratingModel = Mage::getModel('rating/rating');
    $ratingModel->setRatingCode('Overall Rating5')
    	->setRatingCodes($ratingCodes)  
        ->setStores($store_ids)
		->setPosition(0)
		->setEntityId(1)
		->save();
	
	$ratingId = $ratingModel->getId();
	/*
	foreach($stores as $store){
		$fields_arr = array('rating_id' => $ratingId, 'store_id' => $store->getId());
		$write->insert('rating_store', $fields_arr);
	}
	*/
	for($i = 1; $i < 6; $i++){
		$optionModel = Mage::getModel('rating/rating_option');        
        $optionModel->setCode($i)
            ->setValue($i)
            ->setRatingId($ratingId)
            ->setPosition($i)
            ->save();        
	}
	
$installer->endSetup();
