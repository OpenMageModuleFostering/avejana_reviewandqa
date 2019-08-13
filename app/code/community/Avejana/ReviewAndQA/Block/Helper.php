<?php

class Avejana_ReviewAndQA_Block_Helper extends Mage_Review_Block_Helper
{
    protected $_availableTemplates = array(
        'default' => 'reviewandqa/helper/summary.phtml',
        'short'   => 'reviewandqa/helper/summary_short.phtml'
    );

    public function getSummaryHtml($product, $templateType, $displayIfNoReviews)
    {
        // pick template among available
        if (empty($this->_availableTemplates[$templateType])) {
            $templateType = 'default';
        }
        $this->setTemplate($this->_availableTemplates[$templateType]);

        $this->setDisplayIfEmpty($displayIfNoReviews);

        if (!$product->getRatingSummary()) {
            Mage::getModel('review/review')
               ->getEntitySummary($product, Mage::app()->getStore()->getId());
        }
        $this->setProduct($product);

        return $this->toHtml();
    }

    public function getRatingSummary()
    {
        return $this->getProduct()->getRatingSummary()->getRatingSummary();
    }

    public function getAvejanaReviewsCount()
    {	
    	$answers = Mage::getSingleton('core/session')->getAvejanaReplies(); 		
		$answers = unserialize($answers);
		
		foreach( $answers as $answer ){
			if($answer->Reply != ''){
				$review_ids[] = $answer->ReviewID;
			}
		}
		
		$reviewCollection = Mage::getModel('review/review')->getCollection()
               ->addFieldToFilter('main_table.review_id', array('in' => $review_ids));
               
        return count($reviewCollection);
    }

    public function getReviewsUrl()
    {
        return Mage::getUrl('review/product/list', array(
           'id'        => $this->getProduct()->getId(),
           'category'  => $this->getProduct()->getCategoryId()
        ));
    }

    /**
     * Add an available template by type
     *
     * It should be called before getSummaryHtml()
     *
     * @param string $type
     * @param string $template
     */
    public function addTemplate($type, $template)
    {
        $this->_availableTemplates[$type] = $template;
    }
}
