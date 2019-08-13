<?php

class Avejana_AvejanaRMS_Block_Helper extends Mage_Review_Block_Helper
{
    protected $_availableTemplates = array(
        'default' => 'avejanarms/helper/summary.phtml',
        'short'   => 'avejanarms/helper/summary_short.phtml'
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
		$avgratingandcount =array();
		$_product 	= 	Mage::registry('current_product');
		$product		=	Mage::getModel('catalog/product')->load($_product->getId());
		$avgratingandcount['reviewcount'] = $product->getAvejanaTotalreview();
		$avgratingandcount['avgrating'] = $product->getAvejanaAveragerating();
		
		return $avgratingandcount;
    }

    public function getAvejanaReviewsCount()
    {	
    	
         $_product 	= 	Mage::registry('current_product');
		$totalrating		=	Mage::helper('avejanarms')->totalRating($_product->getId());
		return $totalrating;
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
