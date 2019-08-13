<?php

class Avejana_ReviewAndQA_Block_Product_View_Reviews extends Mage_Review_Block_Product_View_List
{
    public function __construct()
    {
        parent::__construct();
        
        $answers = Mage::getSingleton('core/session')->getAvejanaReplies(); 		
		$answers = unserialize($answers);
		
		foreach( $answers as $answer ){
			if($answer->Reply != ''){
				$review_ids[] = $answer->ReviewID;
			}
		}
		
		$reviewsEntries = $this->getReviewsCollection()
            ->addFieldToFilter('main_table.review_id', array('in' => $review_ids));
			
        $this->setEntries($reviewsEntries);
        
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
		
		$pager = $this->getLayout()->createBlock('page/html_pager', 'reviews.pager');
        $pager->setAvailableLimit(array(5=>5, 10=>10, 20=>20, 50=>50));    
        $pager->setCollection($this->getEntries());
        
		$this->setChild('pager', $pager);
		$this->getEntries()->load();
		
        return $this;
    }
    
    public function getAnswerFromAvejana($qnaid){
		
		$answers = Mage::getSingleton('core/session')->getAvejanaAnswers(); 
		$answers = unserialize($answers);
		
		$qa = array();
		foreach($answers as $answer){
			$answer = (array)$answer;
			if($answer['QNAID'] == $qnaid){
				$qa = $answer;
				return $qa;
			}
		}
		return $qa;
	}
    
	public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
