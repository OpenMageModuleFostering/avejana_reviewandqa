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

		

		$answers = Mage::getSingleton('core/session')->getAvejanaReplies(); 		

		$answers = unserialize($answers);  
//echo "<pre>";print_r($answers);exit;
		$avejanaAvgRating = 0;

		$inactive_review = array();

		$approve_review = array();

		Mage::getSingleton('core/session')->setAvejanaAvgRating($avejanaAvgRating);

		foreach( $answers as $key => $answer ){						

				$key = (string)$key;

				if($key != 'pid'){

					

						$IsReviewUpdated = (int)$answer->IsReviewUpdated;

						$IsReviewActive = (int)$answer->IsActive;

						

						if($IsReviewUpdated == '0' && $IsReviewActive == '1'){

							//echo $avejanaAvgRating

							$review_ids[] = (int)$this->insertAvejanaReview($answer);

							$avejanaAvgRating = $avejanaAvgRating + (int)($answer->Ratings);

							//echo $avejanaAvgRating;

							continue;

						}						

						if( $IsReviewActive != '0'){							

							$review_ids[] = (int)$answer->ReviewID;

							$avejanaAvgRating = $avejanaAvgRating + (int)($answer->Ratings);

							$approve_review[] = (int)$answer->ReviewID;

						}

						$IsReviewActive = (string)$answer->IsActive;

						if($IsReviewActive == "0"){ 

							$inactive_review[] = (int)$answer->ReviewID;

						}

				}

			

		}

		

		// unpublish review code start	

		if(sizeof($inactive_review) > 0){			

		   

		   $reviews_disable = Mage::getModel('review/review')->getCollection()->addFieldToFilter('main_table.review_id', array('in' => $inactive_review))->setDateOrder()->getItems();

		   

		   foreach($reviews_disable as $_review){		

				$reviewId =  (int)$_review->getReviewId();

				$review = Mage::getModel('review/review')->load($reviewId);

			

				try {

					$review->setStatusId(Mage_Review_Model_Review::STATUS_PENDING)->setStoreId(Mage::app()->getStore()->getId())->save();	

				} catch (Exception $e){

					//echo $e->getMessage();   

				} 

										

			} 					

		}

		

		// approve review in magento

		if(sizeof($approve_review) > 0){			

		   

		   $reviews_disable = Mage::getModel('review/review')->getCollection()->addFieldToFilter('main_table.review_id', array('in' => $approve_review))->setDateOrder()->getItems();

		   

		   foreach($reviews_disable as $_review){	

				$reviewId = (int)$_review->getReviewId();

				$review = Mage::getModel('review/review')->load($reviewId);

				

				try {

					$review->setStatusId(Mage_Review_Model_Review::STATUS_APPROVED)->setStoreId(Mage::app()->getStore()->getId())->save();

				} catch (Exception $e){ 

				} 

			} 					

		}

		

		//$avejanaAvgRating = $avejanaAvgRating*20/count($review_ids);

		//echo sizeof($review_ids);exit;

		Mage::getSingleton('core/session')->setAvejanaTotalReviews(sizeof($review_ids));

		if(sizeof($review_ids) > 0){

			

			

			Mage::getSingleton('core/session')->setAvejanaAvgRating($avejanaAvgRating);		

					$all_review = Mage::getModel('review/review')->getCollection()->addFieldToFilter('main_table.review_id', array('in' => $review_ids))->setDateOrder()->getItems();		$AwejanaReviewDiffrence = array();		$mageIDArr = array();				

					foreach($all_review as $_review){								

						$StatusId = $_review->getStatusId();				

						if($StatusId == 0){					 

							$_review->setStatusId(1)->save();				

						}				

						$mageIDArr[] = $_review->getReviewId();		

					} 						

							

					$reviewCollection = $this->getReviewsCollection()->addFieldToFilter('main_table.review_id', array('in' => $review_ids))->SetOrder('main_table.review_id', 'DESC');		

					//$reviewCollection = $this->getReviewsCollection()->addFieldToFilter('main_table.review_id', array('in' => $review_ids))->SetOrder('main_table.created_at', 'DESC');		

					$pager = $this->getLayout()->createBlock('page/html_pager', 'reviews.pager');

					$pager->setAvailableLimit(array(5=>5, 10=>10, 20=>20, 50=>50));    

					$pager->setCollection($reviewCollection); 

			

					$this->setChild('pager', $pager);

      	}	

		return $this;

	}



	public function insertAvejanaReview($answer){

		

		if($answer->Title != '' or $answer->Description != '' or $answer->Title != ''){

			

			$customer_id = '';

			$customer = Mage::getModel("customer/customer")

			->setWebsiteId(Mage::app()->getWebsite('admin')->getId())

			->loadByEmail($answer->UserEmail);

       

			

			

			$ReviewFrom = $answer->UserName;

			if($ReviewFrom == ""){

				$ReviewFrom = $answer->ReviewFrom;

			}

			

			$awejana_review_date = $answer->ReviewDate;

			$date = date('Y-m-d H:i:s', strtotime($awejana_review_date));

			$review = Mage::getModel('review/review')-> setNickname($ReviewFrom)

			->setTitle($answer->Title)

			->setDetail($answer->Description)

			->setNickname($ReviewFrom)

			->setEntityId(1)

			->setEntityPkValue($this->getProductId())

			->setStatusId(Mage_Review_Model_Review::STATUS_APPROVED)

			->setStoreId(Mage::app()->getStore()->getId())

			->setCreatedAt($date)

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

				

				$header_arr = array(

					"content-type: application/x-www-form-urlencoded",

					"rest-ajevana-key: ".$this->_helper()->getApiKey()."",

					"user-id: ".$this->_helper()->getUserId().""

				); 

				

				$url = 'https://company.avejana.com/api/set_review_status';//Mage::getStoreConfig('apiconfig/review_api/update_url');

				$ajax_response = $this->callPUTCurl($url, $data, $header_arr);

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

		if(null === $this->_reviewsCollection){

			$this->_reviewsCollection = Mage::getModel('review/review')->getCollection()

			->addStoreFilter(Mage::app()->getStore()->getId())

			->addEntityFilter('product', $this->getProduct()->getId());

		}

		return $this->_reviewsCollection;

	}

	

	public function getAvejanaAvgRating(){

		

		return $this->_reviewsCollection;

	}

    

	public function getLogoStatus(){	

		$logo_status = '0';

		$logo_status_url = 'https://company.avejana.com/api/displaylogo';

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

