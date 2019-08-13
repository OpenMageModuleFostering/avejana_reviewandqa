<?php

class Avejana_ReviewAndQA_Block_Product_View_Reviews extends Mage_Review_Block_Product_View_List{
	protected $_forceHasOptions = false;

	protected function _helper(){
		return Mage::helper('reviewandqa');
	}
    
	public function getProductId(){
		return Mage::registry('product')->getId();
	}

	protected function _prepareLayout(){
		parent::_prepareLayout();
		/*
		$answers = Mage::getSingleton('core/session')->getAvejanaReplies(); 		
		$answers = unserialize($answers);
		$avejanaAvgRating = 0;
		Mage::getSingleton('core/session')->setAvejanaAvgRating($avejanaAvgRating);
		foreach( $answers as $key => $answer ){
			if($key == 'pid'){				
				continue;
			}
			if($answer->IsReviewUpdated == 0){
				//echo $avejanaAvgRating;
				$review_ids[] = $this->insertAvejanaReview($answer);
				$avejanaAvgRating = $avejanaAvgRating + (int)($answer->Ratings);
				//echo $avejanaAvgRating;
				continue;
			}
			if( $answer->IsActive != '0'){
				$review_ids[] = $answer->ReviewID;
				$avejanaAvgRating = $avejanaAvgRating + (int)($answer->Ratings);
				//echo $avejanaAvgRating;
			}
			//echo $avejanaAvgRating;
		}
		//echo count($review_ids);
		//echo $avejanaAvgRating;exit;
		$avejanaAvgRating = $avejanaAvgRating*20/count($review_ids);
		Mage::getSingleton('core/session')->setAvejanaAvgRating($avejanaAvgRating);
		//print_r($review_ids);exit;		
		*/
		$reviewCollection = $this->getReviewsCollection();
		//->addFieldToFilter('main_table.review_id', array('in' => $review_ids));
		
		//echo '<pre>'; print_r($reviewCollection->getData());
		$pager = $this->getLayout()->createBlock('page/html_pager', 'reviews.pager');
		$pager->setAvailableLimit(array(5=>5, 10=>10, 20=>20, 50=>50));    
		$pager->setCollection($reviewCollection);
        
		$this->setChild('pager', $pager);
      		
		return $this;
	}

	public function insertAvejanaReview($answer){
		//echo '<pre>'. $answer->Title;print_r($answer);/*
		if($answer->Title != '' or $answer->Description != '' or $answer->Title != ''){
			
			$customer_id = '';
			$customer = Mage::getModel("customer/customer")
			->setWebsiteId(Mage::app()->getWebsite('admin')->getId())
			->loadByEmail($answer->UserEmail);
       
			
			
			
			$review = Mage::getModel('review/review')-> setNickname($answer->UserName)
			->setTitle($answer->Title)
			->setDetail($answer->Description)
			->setEntityId(1)
			->setEntityPkValue($this->getProductId())
			->setStatusId(Mage_Review_Model_Review::STATUS_APPROVED)
			->setStoreId(Mage::app()->getStore()->getId())
			//->setCustomerId($customer_id)
			->setStores(array(Mage::app()->getStore()->getId()))
			->save();
	        	
			//print_r($review->getData());
	        
			$ratingCollection = Mage::getModel('rating/rating')->getCollection()
			->addFieldToFilter('main_table.rating_code', 'Overall Rating')
			->addFieldToFilter('main_table.entity_id', 1);
			foreach($ratingCollection as  $rating){
				$rating_id = $rating->getRatingId();
			}
			$ratingOptionCollection = Mage::getModel('rating/rating_option')->getCollection()
			->addFieldToFilter('main_table.rating_id', $rating_id)
			->addFieldToFilter('main_table.value', $answer->Ratings);
			foreach($ratingOptionCollection as $ratingOption){
				$ratingOption_id = $ratingOption->getOptionId();
			}
			Mage::getModel('rating/rating')
			->setRatingId($rating_id)
			->setReviewId($review->getId())
			//->setCustomerId($customer_id)
			->addOptionVote($ratingOption_id, $this->getProductId());
			
			$review->aggregate();
			
			if($review->getId() != ''){
				$data = array(
					'InternalReviewID' => $answer->InternalReviewID,
					'ReviewID' => $review->getId(),
					'CompanyID' => $this->_helper()->getCompanyId(),
					'IsReviewUpdated' => 1,
					
				);
				//echo '<pre>'; print_r($data);
				$header_arr = array(
					"content-type: application/x-www-form-urlencoded",
					"rest-ajevana-key: ".$this->_helper()->getApiKey()."",
					"user-id: ".$this->_helper()->getUserId().""
				); 
				//echo '<pre>'; print_r($header_arr);
				$url = 'http://company.avejana.com/api/set_review_status';
				$ajax_response = $this->callPUTCurl($url, $data, $header_arr);
				//print_r($ajax_response);
			}
			return $review->getId();
		}
		return true;
	}

	protected function _beforeToHtml(){
		$this->getReviewsCollection()
		->load()
		->addRateVotes();
		return parent::_beforeToHtml();
	} 

	public function getReviewUrl($id){
		return Mage::getUrl('review/product/view', array('id' => $id));
	}
    
	public function getAvejanaReviewsCollection(){
		$answers = Mage::getSingleton('core/session')->getAvejanaReplies(); 		
		$answers1 = unserialize($answers);
		//print_r($answers);exit;
		return $answers1;
	}
	
	public function getAggregateRatingSummary(){
		//return $this->getProduct()->getRatingSummary();
		//return Mage::getModel('rating/rating')->getEntitySummary($this->getProduct()->getId())->getData('sum');
		$RatingOb=Mage::getModel('rating/rating')->getEntitySummary($this->getProduct()->getId());
		return  $RatingOb->getSum()/$RatingOb->getCount();
   
	}

	
	public function getReviewsCollection(){
		$answers = Mage::getSingleton('core/session')->getAvejanaReplies(); 		
		$answers = unserialize($answers);
		$avejanaAvgRating = 0;
		$updatedReviewIds = array();
		Mage::getSingleton('core/session')->setAvejanaAvgRating($avejanaAvgRating);
		Mage::getSingleton('core/session')->setUpdatedReviewIds($updatedReviewIds);
		foreach( $answers as $key => $answer ){
			//echo $answer->ReviewID;
			
			if($answer->IsReviewUpdated == '0'){
				//echo $avejanaAvgRating;
				$updatedReviewId = $this->insertAvejanaReview($answer);
				$review_ids[] = $updatedReviewId;
				$updatedReviewIds[] = $updatedReviewId;
				$avejanaAvgRating = $avejanaAvgRating + (int)($answer->Ratings);
				//echo $avejanaAvgRating;
				continue;
			}
			else if($answer->IsActive != '0'){
				$review_ids[] = $answer->ReviewID;
				$avejanaAvgRating = $avejanaAvgRating + (int)($answer->Ratings);
				//echo $avejanaAvgRating;
			}
			//echo $avejanaAvgRating;
		}
		//echo count($review_ids);
		//echo $avejanaAvgRating;exit;
		$avejanaAvgRating = $avejanaAvgRating * 20 / count($review_ids);
		Mage::getSingleton('core/session')->setAvejanaAvgRating($avejanaAvgRating);
		Mage::getSingleton('core/session')->setUpdatedReviewIds($updatedReviewIds);
		//print_r($review_ids);
		
		if(null === $this->_reviewsCollection){
			$this->_reviewsCollection = Mage::getModel('review/review')->getCollection()
			->addStoreFilter(Mage::app()->getStore()->getId())
			->addEntityFilter('product', $this->getProduct()->getId())
			->addFieldToFilter('main_table.review_id', array('in' => $review_ids));
		}
		return $this->_reviewsCollection;
	}
	
	public function getAvejanaAvgRating(){
		
		return $this->_reviewsCollection;
	}
    
	public function getLogoStatus(){	
		$logo_status = '0';
		$logo_status_url = 'http://company.avejana.com/api/snippets';
		$answers = array();
		
		$data = array(
			'CompanyID' => $this->_helper()->getCompanyId()
		);
		$header_arr = array(
			"rest-ajevana-key: ".$this->_helper()->getApiKey()."",
			"user-id: ".$this->_helper()->getUserId().""
		);  
		$final_url = $logo_status_url . "?" . http_build_query($data);
		$ajax_response = $this->_helper()->callGETCurl($final_url, $data, $header_arr);
		$response = json_decode($ajax_response);		
		
		$status = $response->status;
		if($status == 'success'){
			$logo_status = $response->message;
		}
		//echo $logo_status;exit;
		return $logo_status;
	}
	
	public function getPagerHtml(){
		return $this->getChildHtml('pager');
	}
	
	public function callPUTCurl($url, $data, $header_arr){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header_arr);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		$response = curl_exec($ch);
		
		return $response;
	}
}
