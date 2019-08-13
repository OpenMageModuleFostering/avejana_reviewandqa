<?php
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
	DROP TABLE IF EXISTS {$this->getTable('avejana_questions')};
	CREATE TABLE {$this->getTable('avejana_questions')} (
	`questions_id` int(11) unsigned NOT NULL auto_increment,
	`name` varchar(255) NOT NULL default '',
	`email` varchar(255) NOT NULL default '',  
	`question` text NOT NULL default '',
	`answer` text NOT NULL default '',
	`status` enum('public','hidden') default 'public',
	`ip_address` varchar(255) NOT NULL default '',
	`store_id` smallint(5) unsigned NOT NULL default '0',
	`product_id` int unsigned not null,
	`created_on` datetime,
	`answered_on` datetime,
	PRIMARY KEY (`questions_id`),
	CONSTRAINT FOREIGN KEY (`product_id`) REFERENCES {$this->getTable('catalog_product_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

	DROP TABLE IF EXISTS {$this->getTable('avejana_orderfldmap')};
	CREATE TABLE {$this->getTable('avejana_orderfldmap')} (
	`entity_id` int(11) unsigned NOT NULL auto_increment,
	`mage_field_of` varchar(255) NOT NULL default '',
	`mage_field` varchar(255) NOT NULL default '',  
	`mage_field_label` varchar(255) NOT NULL default '',  
	`sf_field_of` varchar(255) NOT NULL default '',  
	`sf_field` varchar(255) NOT NULL default '',  
	`sf_field_label` varchar(255) NOT NULL default '',  
	`default_value` varchar(255) NOT NULL default '',  
	PRIMARY KEY (`entity_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

	");

	$write = Mage::getSingleton('core/resource')->getConnection('core_write');
	
	$ratings = Mage::getModel('rating/rating')->getCollection();
	foreach($ratings as $rating){
		$write->delete(
		"rating_store",
		"rating_id = '".$rating->getId()."' AND store_id != 0"
		);
	}   
	
	$stores = Mage::getModel('core/store')->getCollection()->addFieldToFilter('is_active', 1);
	$ratingCodes = array();
	$store_ids = array();
	foreach($stores as $store){
		$store_ids[] = $store->getId();
		$ratingCodes[$store->getId()] = 'Overall Rating';
	}
	
	$ratingModel = Mage::getModel('rating/rating');
    $ratingModel->setRatingCode('Overall Rating')
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
