<?php

class Avejana_ReviewAndQA_Model_Reviews extends Mage_Core_Model_Abstract

{	
    public function getReviewsCollection()
    {
        $collection = Mage::getModel('review/review')->getResourceCollection()->addStoreFilter(Mage::app()->getStore()->getId())->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
    	            ->setDateOrder();
		return $collection;
    }	

	public function getProductReview($productid)
	{
		$reviews = Mage::getModel('review/review')
        	        ->getResourceCollection()
            	    ->addStoreFilter(Mage::app()->getStore()->getId())
                	->addEntityFilter('product', $productid)
	                ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
    	            ->setDateOrder()
        	        ->addRateVotes(); 
		return $reviews;
	}
}

