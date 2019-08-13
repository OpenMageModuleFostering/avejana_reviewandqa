<?php

/**
 * Product Reviews Page
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Avejana_ReviewAndQA_Block_Product_View extends Mage_Review_Block_Product_View 
{
  
    public function getReviewsSummaryHtml(Mage_Catalog_Model_Product $product, $templateType = false, $displayIfNoReviews = false)
    {
    	//echo 'Pratik';exit;
        return
            $this->getLayout()->createBlock('rating/entity_detailed')
                ->setEntityId($this->getProduct()->getId())
                ->toHtml()
            .
            $this->getLayout()->getBlock('product_review_list.count')
                ->assign('count',count($this->getAvejanaReviewsCollection()))
                ->toHtml()
            ;
    }
	
	public function getAvejanaReviewsCollection(){
		$answers = Mage::getSingleton('core/session')->getAvejanaReplies(); 		
		$answers1 = unserialize($answers);
		//print_r($answers);exit;
		return $answers1;
	}
	
    public function getReviewsCollection()
    {
        if (null === $this->_reviewsCollection) {
            $this->_reviewsCollection = Mage::getModel('review/review')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
                ->addEntityFilter('product', $this->getProduct()->getId())
                ->setDateOrder();
        }
        return $this->_reviewsCollection;
    }

    
}
