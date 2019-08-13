<?php   
class Avejana_AvejanaRMS_Block_Index extends Mage_Core_Block_Template{   


	public function getAvejanaReviesListing($productid){
	
			$url = Mage::helper('avejanarms')->getCompanyUrl().'/api/reviewreply/';	
			$store_id = Mage::app()->getStore()->getStoreId();
			$action = Mage::getModel('catalog/resource_product_action');
			$returnarr =array();

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

				if($response->status=='success'){
					$totalreviewcount=count($response->message);
					$totalratings=0;
					foreach($response->message as $reviews){
						$totalratings=$totalratings+$reviews->Ratings;
					}
					$averagerating=($totalratings/$totalreviewcount)*20;
					$returnarr['avgrating']		=	$averagerating;
					$returnarr['reviewcount']	=	$totalreviewcount;
					$returnarr['reviews']	=	$response->message;
					
					
					$action->updateAttributes(array($productid), array(
						'avejana_averagerating' => $averagerating
					), $store_id);
					
					$action->updateAttributes(array($productid), array(
						'avejana_totalreview' => $totalreviewcount
					), $store_id);
					
					return $returnarr;
				}else{
					$action->updateAttributes(array($productid), array(
						'avejana_averagerating' => 0
					), $store_id);
					
					$action->updateAttributes(array($productid), array(
						'avejana_totalreview' => 0
					), $store_id);
					$returnarr = array(); 
				}
			}
	}
	
	
	public function getAvejanaQuestionAnswerListing($productid)
	{
			$url = Mage::helper('avejanarms')->getCompanyUrl().'/api/answer/';	
			$store_id = Mage::app()->getStore()->getStoreId();
			$action = Mage::getModel('catalog/resource_product_action');

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

					
					$i=0; foreach($response->message as $_qa){
						if($_qa->Answer != ''){ 
							$i=$i+1;
						}
					}
					
					$action->updateAttributes(array($productid), array(
						'avejana_totalqa' => $i
					), $store_id);
					return $response->message;

				}else{
					$action->updateAttributes(array($productid), array(
						'avejana_totalqa' => 0
					), $store_id);
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