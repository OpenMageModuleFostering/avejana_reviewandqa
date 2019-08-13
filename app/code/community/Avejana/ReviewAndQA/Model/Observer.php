<?php
class Avejana_ReviewAndQA_Model_Observer{
	
	public $session;
	
	public function __construct(){
		$this->session = Mage::getSingleton('core/session');
	}
	
	protected function _helper(){
		return Mage::helper('reviewandqa');
	}
	
	public function export_review_to_avejana($observer){
		$url = Mage::getStoreConfig('apiconfig/review_api/send_url');
		
		$product_id = Mage::app()->getRequest()->getParam('id');
		$review = Mage::getModel('review/review')->getCollection()->getLastItem();
		$ratings = Mage::app()->getRequest()->getParam('ratings');		
		
		//$product_id = $product->getId();
		$review_title = trim($review->getTitle());
		$review_detail = trim($review->getDetail());
		$review_id = $review->getReviewId();
		$avg_ratting = $this->getAverageRatings($ratings);
		$customer_id = $review->getCustomerId();
		$privacy = '1';
		
		$customer_name = Mage::app()->getRequest()->getPost('nickname');
		$customer_email = Mage::app()->getRequest()->getPost('email');
		/*
		if($customer_id){
		$customer = Mage::getModel('customer/customer')->load($customer_id);
		$customer_name = $customer->getName();
		$customer_email = $customer->getEmail();
		}
		*/
		$data = array(
			'FromCompany' => $this->_helper()->getCompanyId(),
			'ProductID' => $product_id,
			'InternalReviewID' => $review_id,
			'IsPrivate' => $privacy,
			'UserName' =>$customer_name,
			'UserEmail' => $customer_email,
			'Title' => $review_title,
			'Ratings' => $avg_ratting,
			'Description' => $review_detail,
			'IsReviewUpdated' => 1,
		);
		$header_arr = array(
			"content-type: application/x-www-form-urlencoded",
			"rest-ajevana-key: ".$this->_helper()->getApiKey()."",
			"user-id: ".$this->_helper()->getUserId().""
		); 
		//print_r($data);exit;
		$ajax_response = $this->callPUTCurl($url, $data, $header_arr);
		//print_r($ajax_response);exit;
		
		return true;
	}
	
	public function get_reviews_replies_from_avejana($observer){	
		$url = Mage::getStoreConfig('apiconfig/review_api/reply_url');		
		$replies = '';
		Mage::getSingleton('core/session')->setAvejanaReplies(serialize($replies));
		
		$product_id = Mage::app()->getRequest()->getParam('id');
		$data = array(
			'CompanyID' => $this->_helper()->getCompanyId(),
			'ProductID' => $product_id
		);
		$header_arr = array(
			"rest-ajevana-key: ".$this->_helper()->getApiKey()."",
			"user-id: ".$this->_helper()->getUserId().""
		); 
		$final_url = $url . "?" . http_build_query($data);
		
		$ajax_response = $this->callGETCurl($final_url, $data, $header_arr);
		//echo '<pre>'; print_r($ajax_response);exit;
		$response = json_decode($ajax_response);
		$status = $response->status;
		$message = (array)$response->message;
		
		if($status == 'success'){
			//$this->session->addSuccess($status);
			$replies = $message;
			$replies['pid'] = $product_id;
		}else{
			//$this->session->addError('fail to open replies on reviews');
		}
		//echo '<pre>';print_r($replies);exit;
		$this->session->setAvejanaReplies(serialize($replies));
		return true;
	}
	
	public function export_question_to_avejana($observer){	
		$url = Mage::getStoreConfig('apiconfig/qa_api/send_url');
		$questions = $observer->getQuestions();		
		$product = $observer->getProduct();	
		
		$product_id = $questions->getProductId();		
		$question = trim($questions->getQuestion());
		$question_id = $questions->getQuestionsId();		
		$customer_id = false;
		$privacy = '1';
		$customer_name = 'guest';
		$customer_email = 'guset_user@gmail.com';
		if($customer_id){
			$customer = Mage::getModel('customer/customer')->load($customer_id);
			$customer_name = $customer->getName();
			$customer_email = $customer->getEmail();
		}
		$customer_email = $questions->getEmail();
		$customer_name =$questions->getName();
		$data = array(
			'FromCompany' => $this->_helper()->getCompanyId(),
			'ProductID' => $product_id,
			'InternalQNAID' => $question_id,
			'IsPrivate' => $privacy,
			'UserName' =>$customer_name,
			'UserEmail' => $customer_email,
			'Question' => $question
		);
		$header_arr = array(
			"content-type: application/x-www-form-urlencoded",
			"rest-ajevana-key: ".$this->_helper()->getApiKey()."",
			"user-id: ".$this->_helper()->getUserId().""
		); 
		
		//print_r($data);exit;
		$ajax_response = $this->callPUTCurl($url, $data, $header_arr);
		//print_r($ajax_response);exit;
		
		return true;
	}
	
	public function get_questions_answers_from_avejana($observer){	
		$url = Mage::getStoreConfig('apiconfig/qa_api/reply_url');
		$answers = array();
		
		$product_id = Mage::app()->getRequest()->getParam('id');		
		$data = array(
			'CompanyID' => $this->_helper()->getCompanyId(),
			'ProductID' => $product_id
		);
		$header_arr = array(
			"rest-ajevana-key: ".$this->_helper()->getApiKey()."",
			"user-id: ".$this->_helper()->getUserId().""
		);  
		$final_url = $url . "?" . http_build_query($data);
		
		$ajax_response = $this->callGETCurl($final_url, $data, $header_arr);
		$response = json_decode($ajax_response);
		$status = $response->status;
		$message = (array)$response->message;
		
		if($status == 'success'){
			//$this->session->addSuccess($status);
			$answers = $message;
		}else{
			//$this->session->addError('fail to open answres on questions');
		}
		$this->session->setAvejanaAnswers(serialize($answers));
		
		return true;
	}
	
	public function send_product_view_count($observer){	
		$url = Mage::getStoreConfig('apiconfig/pageviews_api/send_url');
		$replies = '';
		
		$product_id = Mage::app()->getRequest()->getParam('id');
		$remote_addr = Mage::helper('core/http')->getRemoteAddr(false);
		$data = array(
			'CompanyID' => $this->_helper()->getCompanyId(),
			'ProductID' => $product_id,
			'IP' => $remote_addr
		);
		$header_arr = array(
			"rest-ajevana-key: ".$this->_helper()->getApiKey()."",
			"user-id: ".$this->_helper()->getUserId().""
		); 
		$final_url = $url . "?" . http_build_query($data);
		
		$ajax_response = $this->callPUTCurl($final_url, $data, $header_arr);
		return true;
	}
	
	public function export_product_to_avejana($product_id){	
		$url = Mage::getStoreConfig('apiconfig/product_create_api/send_url');
		$product = Mage::getModel('catalog/product')->load($product_id);
		$data = array(
			'CompanyID' => $this->_helper()->getCompanyId(),
			'ProductID' => $product->getId(),
			'ProductURL' => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'/index.php/'.$product->getUrlPath(),
			'ProductName' => $product->getName(),
			'ProductDescription' => $product->getDescription(),
			'ProductPictureURL' => $product->getImageUrl(),
			'ProductPrice' => $product->getPrice(),
		);
		$header_arr = array(
			"content-type: application/x-www-form-urlencoded",
			"rest-ajevana-key: ".$this->_helper()->getApiKey()."",
			"user-id: ".".$this->_helper()->getUserId().".""
		); 
		//$url = "http://brand.avejana.com/api/product";
		
		$ajax_response = $this->callPUTCurl($url, $data, $header_arr);
		$response = json_decode($ajax_response);
		$status = $response->status;
		
		return true;
	}
	
	public function export_product_to_avejana_after_save($observer){	
		$product = $observer->getEvent()->getProduct();
		$product_id = $product->getId();
		//echo $product_id;exit;
		$this->export_product_to_avejana($product_id);
		return true;
	}
	
	public function export_order_to_avejana_after_place($observer){
		$url = Mage::getStoreConfig('apiconfig/sales_api/send_url');
		
		$order = $observer->getOrder();
		$increment_id = $order->getIncrementId();
		$order_date = date_format(date_create($order->getCreatedAtStoreDate()),"Y-m-d");
		$customer_id = $order->getCustomerId();
		$privacy = '1';
		$customer_name = $order->getCustomerName();
		$customer_email = $order->getCustomerEmail();
		$data = array(
			'FromCompany' => $this->_helper()->getCompanyId(),
			'OrderID' => $increment_id,
			'OrderDate' => $order_date,
			'CustomerName' => $customer_name,
			'CustomerEmail' => $customer_email,
		);
		$header_arr = array(
			"content-type: application/x-www-form-urlencoded",
			"rest-ajevana-key: ".$this->_helper()->getApiKey()."",
			"user-id: ".$this->_helper()->getUserId().""
		);
		$items = $order->getAllItems();
			
		$product_price  = 0;
		foreach($items as $item){			
			if($product_price == 0){
				$product_price = $item->getPrice();
			}
			$pid = $item->getProductId();
			$product= Mage::getModel('catalog/product')->load($pid);
			
			$attributeSetModel = Mage::getModel("eav/entity_attribute_set")->load($product->getAttributeSetId());
			$attributeSetName  = $attributeSetModel->getAttributeSetName();

			$parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($product->getId());

			if ($product->getTypeId() == 'simple' && empty($parentIds) && $attributeSetName == 'Default') {
					
					$data['ProductID'] = $item->getProductId();
			}else{
					if(sizeof($parentIds) > 0){
						$productId = $parentIds[0];
					}else{
						$productId = $item->getProductId();
					}
					$data['ProductID'] = $productId;
					
			}	
			
			$data['Price'] = number_format((float)$product_price, 2, '.', '');
			$data['Quantity'] = $item->getQtyOrdered();
			$data['Type'] = $item->getQtyOrdered();
			$ajax_response = $this->callPUTCurl($url, $data, $header_arr);
			
		}
		return true;
	}
	
	public function exportProductCron(){			
		$productModel = Mage::getModel('catalog/product');
		$products = $productModel->getCollection();
		foreach($products as $product){
			$product_id = $product->getId();
			$this->export_product_to_avejana($product_id);
		}
		return true;
	}
	
	public function add_snippets_on_product_page(){
		$node = Mage::getConfig()->getNode('global/blocks/catalog/rewrite');
		$richNode = Mage::getConfig()->getNode('global/blocks/catalog/rewrite/product_view');
		$node->appendChild($richNode);       
	}
	
	/**
	* Getting average of ratings
	* @param array $ratings
	* 
	* @return response average of all reviews values between 1 to 5
	*/
	public function getAverageRatings($ratings){
		$avg = 0;
		$count = 0;	
		foreach( $ratings as $rating ){
			$org_ratting = $rating%5;
			if($org_ratting == 0){
				$org_ratting = 5;
			}
			$avg += $org_ratting;
			$count++;
		}
		$avg_ratting = $avg/$count;
		
		return $avg_ratting;
	}
	/**
	* call ajax by cURL with PUT method
	* 
	* @param url $url
	* @param array $data
	* @param array $header_arr
	* 
	* @return response response from cURL
	*/
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
	
	/**
	* call ajax by cURL with GET method
	* 
	* @param string $url
	* @param array $data
	* @param array $header_arr
	* 
	* @return response response from cURL
	*/
	public function callGETCurl($url, $data, $header_arr){
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header_arr);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		$response = curl_exec($ch);
		
		return $response;
	}
}
