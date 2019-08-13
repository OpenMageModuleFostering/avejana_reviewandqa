<?php

class Avejana_ReviewAndQA_Model_Replies extends Mage_Core_Model_Abstract

{
	public function _construct()
    {
        parent::_construct();
        $this->_init('reviewandqa/replies');
    }
    
    public function getReplies($review_id){
		$replies = $this->getCollection()
					->addFieldToFilter('review_id',array('eq'=>$review_id));
		return $replies;
	}   
	public function getReplyFromAvejana($review_id){
		
		$answers = Mage::getSingleton('core/session')->getAvejanaReplies(); 		
		$answers = unserialize($answers);
		//print_r($answers);exit;
		$qa = array();
		foreach($answers as $answer){
			$answer = (array)$answer;
			if($answer['ReviewID'] == $review_id){
				$qa = $answer;
				return $qa;
			}
		}
		return $qa;
	}
}

