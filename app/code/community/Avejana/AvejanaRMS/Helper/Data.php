<?php
class Avejana_AvejanaRMS_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getCompanyId(){
        return Mage::getStoreConfig('avejanasetting/avejanaconfiguration/companyid');
    }
    
	public function getUserId(){
        return Mage::getStoreConfig('avejanasetting/avejanaconfiguration/companyid');
    }
    	
	public function getApiKey(){
        return Mage::getStoreConfig('avejanasetting/avejanaconfiguration/avejanakey');
    }    
	
	public function getUserUrl(){
        return Mage::getStoreConfig('avejanasetting/avejanaconfiguration/companyurl');
    }    
    
    public function getCompanyUrl(){
        return 'https://company.avejana.com';
    }  
	
	public function averageRating($productid){
        
		try{
		
		$url 			= 	Mage::helper('avejanarms')->getCompanyUrl().'/api/reviewreply';	
		$averagerating 	=	0;
		$returnarr  	=  	array();

		$data = array(

			'CompanyID' => Mage::helper('avejanarms')->getCompanyId(),

			'ProductID' => $productid

		);

		$header_arr = array(
			"content-type: application/x-www-form-urlencoded",
			"rest-ajevana-key: ".Mage::helper('avejanarms')->getApiKey()."",

			"user-id: ".Mage::helper('avejanarms')->getUserId().""

		); 

		$final_url = $url . "?" . http_build_query($data);

		$ajax_response = $this->callGETCurl($final_url, $data, $header_arr);

		$response = json_decode($ajax_response);
			
		if($response){
			if($response->status=='success'){
				$totalreviewcount=count($response->message);
				$totalratings=0;
				foreach($response->message as $reviews){
					$totalratings=$totalratings+$reviews->Ratings;
				}
				$averagerating=($totalratings/$totalreviewcount)*20;
				$returnarr['avgrating']		=	$averagerating;
				$returnarr['reviewcount']	=	$totalreviewcount;
			}else{
				$returnarr = array(); 
			}
		}
		return $returnarr;
		}catch(Exception $e){
			print_r($e);
		}
			
	}
	
	public function totalRating($productid){
        
		try{
			$url = Mage::helper('avejanarms')->getCompanyUrl().'/api/reviewreply';	
			$totalreviewcount=0;

			$data = array(

				'CompanyID' => Mage::helper('avejanarms')->getCompanyId(),

				'ProductID' => $productid

			);

			$header_arr = array(
				"content-type: application/x-www-form-urlencoded",
				"rest-ajevana-key: ".Mage::helper('avejanarms')->getApiKey()."",

				"user-id: ".Mage::helper('avejanarms')->getUserId().""

			); 

			$final_url = $url . "?" . http_build_query($data);

			$ajax_response = $this->callGETCurl($final_url, $data, $header_arr);

			$response = json_decode($ajax_response);
			//echo 1;print_r($response);
			if($response){
				if($response->status=='success'){
					$totalreviewcount=count($response->message);

				}else{
					$totalreviewcount = 0;
				}
			}
			return $totalreviewcount;
		}catch(Exception $e){
			print_r($e);
		}
			
	}
	
	public function getReviews($productid){
        
		try{
			$url = Mage::helper('avejanarms')->getCompanyUrl().'/api/reviewreply';	
			$_reviews=0;

			$data = array(

				'CompanyID' => Mage::helper('avejanarms')->getCompanyId(),

				'ProductID' => $productid

			);

			$header_arr = array(
				"content-type: application/x-www-form-urlencoded",
				"rest-ajevana-key: ".Mage::helper('avejanarms')->getApiKey()."",

				"user-id: ".Mage::helper('avejanarms')->getUserId().""

			); 

			$final_url = $url . "?" . http_build_query($data);

			$ajax_response = $this->callGETCurl($final_url, $data, $header_arr);

			$response = json_decode($ajax_response);
			if($response){
				if($response->status=='success'){
					$_reviews=$response->message;

				}else{
					$_reviews = 0;
				}
			}
			return $_reviews;
		}catch(Exception $e){
			print_r($e);
		}
			
	}

	public function getLogoShow(){
        
		try{
			$url = Mage::helper('avejanarms')->getCompanyUrl().'/api/displaylogo';	
			$islogoshow=0;

			$data = array(

				'CompanyID' => Mage::helper('avejanarms')->getCompanyId(),

			);

			$header_arr = array(
				
				"content-type: application/x-www-form-urlencoded",
				
				"rest-ajevana-key: ".Mage::helper('avejanarms')->getApiKey()."",

				"user-id: ".Mage::helper('avejanarms')->getUserId().""

			); 

			$final_url = $url . "?" . http_build_query($data);

			$ajax_response = $this->callGETCurl($final_url, $data, $header_arr);

			$response = json_decode($ajax_response);
			//print_r($response);
			if($response){
				if($response->status=='success'){
					$islogoshow=$response->message;

				}else{
					$islogoshow = 0;
				}
			}
			return $islogoshow;
		}catch(Exception $e){
			print_r($e);
		}
			
	}
	
	
	public function getRichSnippet(){
        
		try{
			$url = Mage::helper('avejanarms')->getCompanyUrl().'/api/snippets';	
			$is_rich_snippet_show=0;

			$data = array(

				'CompanyID' => Mage::helper('avejanarms')->getCompanyId(),

			);

			$header_arr = array(
				
				"content-type: application/x-www-form-urlencoded",
				
				"rest-ajevana-key: ".Mage::helper('avejanarms')->getApiKey()."",

				"user-id: ".Mage::helper('avejanarms')->getUserId().""

			); 

			$final_url = $url . "?" . http_build_query($data);

			$ajax_response = $this->callGETCurl($final_url, $data, $header_arr);

			$response = json_decode($ajax_response);
			//print_r($response);
			if($response){
				if($response->status=='success'){
					$is_rich_snippet_show = $response->message;

				}else{
					$is_rich_snippet_show = 0;
				}
			}
			return $is_rich_snippet_show;
		}catch(Exception $e){
			print_r($e);
		}
			
	}
	
	public function getPageView($productid){
        
		try{
			$userIP = 	Mage::helper('core/http')->getRemoteAddr();
			$url 	= 	Mage::helper('avejanarms')->getUserUrl().'/api/pageviews/';	
			$_reviews=0;

			$data = array(

				'CompanyID' => Mage::helper('avejanarms')->getCompanyId(),

				'ProductID' => $productid,
				
				'IP'  => $userIP

			);

			$header_arr = array(
				"content-type: application/x-www-form-urlencoded",
				"rest-ajevana-key: ".Mage::helper('avejanarms')->getApiKey()."",

				"user-id: ".Mage::helper('avejanarms')->getUserId().""

			); 


			$ajax_response = $this->callPUTCurl($url, $data, $header_arr);

			
			return true;
		}catch(Exception $e){
			print_r($e);
		}
			
	}
	
	public function getUsersnurl(){
        
		try{
			$url = Mage::helper('avejanarms')->getCompanyUrl().'/api/get_company_url/';	
			$is_rich_snippet_show=0;

			$data = array(

				'CompanyID' => Mage::helper('avejanarms')->getCompanyId(),
				
				'rest-ajevana-key' => Mage::helper('avejanarms')->getApiKey(),

			);

			$header_arr = array(
				
				"content-type: application/x-www-form-urlencoded",
				
				"rest-ajevana-key: ".Mage::helper('avejanarms')->getApiKey()."",

				"user-id: ".Mage::helper('avejanarms')->getUserId().""

			); 

			$final_url = $url . "?" . http_build_query($data);

			$ajax_response = $this->callGETCurl($final_url, $data, $header_arr);

			$response = json_decode($ajax_response);
			print_r($ajax_response);
			if($response){
				if($response->status=='success'){
					$is_rich_snippet_show = $response->message;

				}else{
					$is_rich_snippet_show = 0;
				}
			}
			return $is_rich_snippet_show;
		}catch(Exception $e){
			print_r($e);
		}
			
	}
	
	
	public function callGETCurl($url, $data, $header_arr){

		

		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_HEADER, false);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $header_arr);

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		$response = curl_exec($ch);
		//print_r(http_build_query($data));
		//print_r($response);
		

		return $response;

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

	
	
}
	 