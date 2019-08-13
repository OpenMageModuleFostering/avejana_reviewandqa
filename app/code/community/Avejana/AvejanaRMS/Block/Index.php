<?php   
class Avejana_AvejanaRMS_Block_Index extends Mage_Core_Block_Template{   


	public function getAvejanaReviesListing($productid)
	{
			$url = Mage::helper('avejanarms')->getCompanyUrl().'/api/reviewreply/';	


			$data = array(

				'CompanyID' => Mage::helper('avejanarms')->getCompanyId(),

				'ProductID' => $productid

			);

			$header_arr = array(

				"rest-ajevana-key: ".Mage::helper('avejanarms')->getApiKey()."",

				"user-id: ".Mage::helper('avejanarms')->getUserId().""

			); 

			$final_url = $url . "?" . http_build_query($data);



			$ajax_response = $this->callGETCurl($final_url, $data, $header_arr);


			$response = json_decode($ajax_response);
			//print_r($ajax_response);exit;
			if($response){
				$status = $response->status;

				if($status == 'success'){

					return $response->message;

				}else{

					//$this->session->addError('');
					//Mage::getSingleton('catalog/session')->addError('fail to open replies on reviews');

				}
			}
	}
	
	
	public function getAvejanaQuestionAnswerListing($productid)
	{
			$url = Mage::helper('avejanarms')->getCompanyUrl().'/api/answer/';	

			//echo $productid;die;

			$data = array(

				'CompanyID' => Mage::helper('avejanarms')->getCompanyId(),

				'ProductID' => $productid

			);

			$header_arr = array(

				"rest-ajevana-key: ".Mage::helper('avejanarms')->getApiKey()."",

				"user-id: ".Mage::helper('avejanarms')->getUserId().""

			); 

			$final_url = $url . "?" . http_build_query($data);



			$ajax_response = $this->callGETCurl($final_url, $data, $header_arr);


			$response = json_decode($ajax_response);
			if($response){
				$status = $response->status;

				if($status == 'success'){

					return $response->message;

				}else{

					//Mage::getSingleton('catalog/session')->addError('fail to open replies of Questions');

				}
			}
	}
	
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