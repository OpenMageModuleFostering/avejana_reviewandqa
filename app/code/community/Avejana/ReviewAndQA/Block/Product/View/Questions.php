<?php
class Avejana_ReviewAndQA_Block_Product_View_Questions extends Mage_Core_Block_Template{

	public function __construct(){
		parent::__construct();
        
		$answers = Mage::getSingleton('core/session')->getAvejanaAnswers(); 
		$answers = unserialize($answers);
		foreach( $answers as $answer ){
			if($answer->Answer != ''){
				$qa_ids[] = $answer->QNAID;
			}
		}
		
		$questionsEntries = Mage::getModel('reviewandqa/questions')->getCollection()
			->addFieldToFilter('status', 'public')
			//->addFieldToFilter('answered_on', array('notnull' => true))
			->addFieldToFilter('product_id', Mage::registry('product')->getId())
			->addFieldToFilter('store_id', Mage::app()->getStore()->getId())
			->addFieldToFilter('questions_id', array('in' => $qa_ids))
			->setOrder('created_on','DESC');
			
		$this->setEntries($questionsEntries);
	}

	protected function _prepareLayout(){
		parent::_prepareLayout();
		
		$pager = $this->getLayout()->createBlock('page/html_pager', 'questions.pager');
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
	
	public function getPagerHtml(){
		return $this->getChildHtml('pager');
	}
}
