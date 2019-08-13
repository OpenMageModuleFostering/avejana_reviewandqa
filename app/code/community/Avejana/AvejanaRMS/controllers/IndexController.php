<?php
class Avejana_AvejanaRMS_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Titlename"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("titlename", array(
                "label" => $this->__("Titlename"),
                "title" => $this->__("Titlename")
		   ));

      $this->renderLayout(); 
	  
    }
	
	/**
     * Submit new review action
     *
     */
    public function reviewpostAction(){

		try{
			
			$data   		= 	$this->getRequest()->getPost();
			$product_id    	= 	$data['product_id'];
			$session        =   Mage::getSingleton('core/session');
			if(!empty($data) && ($product_id)){
				$product_model			= 	Mage::getModel('catalog/product')->load($product_id);
				$IsAvejanaProductPushed = 	$product_model->getAvejanaProductImport();

				if(empty($IsAvejanaProductPushed) || ($IsAvejanaProductPushed != '1')){ 

					$this->export_product_to_avejana($product_model);
				}
				
				Mage::dispatchEvent('controller_action_postdispatch_review_product_post', array('reviewdata'=> $data));
				$session->addSuccess($this->__('Your review has been accepted for moderation.'));
			}
		}catch(Exception $e){
			print_r($e);
			$session->addError($this->__('Unable to post the review.'));
		}
	
		$this->_redirectReferer();
	}
	
	/**
     * Export Single product to avejan.
     *
     */
    
	 public function export_product_to_avejana($product){	

		 $url = Mage::helper('avejanarms')->getUserUrl().'/api/product/';

		try{
			$imageurl  = $product->getImage();
			if($imageurl == 'no_selection')
			{
				 
				$imageurl=Mage::getSingleton('catalog/product_media_config')->getBaseMediaUrl(). '/placeholder/' .Mage::getStoreConfig("catalog/placeholder/small_image_placeholder");
			}
			else
			{
				 $imageurl=Mage::getModel('catalog/product_media_config')->getMediaUrl($product->getImage());
			}
			
			$data = array(

				'CompanyID' => Mage::helper('avejanarms')->getCompanyId(),

				'ProductID' => $product->getId(),

				'ProductURL' => $product->getProductUrl(),

				'ProductName' => $product->getName(),

				'ProductDescription' => $product->getDescription(),

				'ProductPictureURL' => $imageurl,

				'ProductPrice' => $product->getPrice(),

			);

			$header_arr = array(

				"content-type: application/x-www-form-urlencoded",

				"rest-ajevana-key: ".Mage::helper('avejanarms')->getApiKey()."",

				"user-id: ".Mage::helper('avejanarms')->getUserId().""
			); 
			
			$ajax_response = $this->callPUTCurl($url, $data, $header_arr);

			$response = json_decode($ajax_response);
			if($response){
				$status = $response->status;
				$attr = $product->getResource()->getAttribute('avejana_product_import');
				$status = $response->status;
				if($status !='failure')
				{
					$product->setData('avejana_product_import',1);
					$product->getResource()->saveAttribute($product,'avejana_product_import');
				} 	
			}
			return true;
		}catch(Exception $e){
			print_r($e);
		}

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
	
	
	
	/**
     * Upload Sales Data to Avejana after button click
     *
     */
    
	 public function uploadsalesAction(){	
		 
		try{
			
			Mage::dispatchEvent('controller_action_postdispatch_allsales_data_to_avejana', array('salesdata'=> 1));
//$this->_redirectReferer();
			$url 	= 	Mage::helper('avejanarms')->getUserUrl().'/api/sales/';
			 $con	=	Mage::getSingleton('core/resource');
    		 $write	=	$con->getConnection('core_write');

			 $orders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('order_exported_to_avejana', 0)->setOrder('created_at', 'desc'); 
				foreach($orders as $order)
				{	
					$exported 		= 	$order->getOrderExportToAvejana();
					$orderId 		= 	$order->getId();
					$increment_id 	= 	$order->getIncrementId();
					$customer_id 	= 	$order->getCustomerId();
					$customer_name 	= 	$order->getCustomerName();
					$customer_email = 	$order->getCustomerEmail();
					$customer_dob	=	$order->getCustomerDob();
					$customer_gender=	$order->getCustomerGender();
				 	$order_date		=	$order->getCreatedAt();
						$data = array(

							'FromCompany' => Mage::helper('avejanarms')->getCompanyId(),

							'OrderID' => $increment_id,

							'OrderDate' => date("Y-m-d", strtotime($order_date)),

							'CustomerName' => $customer_name,

							'CustomerEmail' => $customer_email,

							'BirthDate' => $customer_dob,

							'GenderID' => $customer_gender,

						);

						$header_arr = array(

							"content-type: application/x-www-form-urlencoded",

							"rest-ajevana-key: ".Mage::helper('avejanarms')->getApiKey()."",

							"user-id: ".Mage::helper('avejanarms')->getUserId().""
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



						if ($product->getTypeId() == 'simple' && empty($parentIds)) {



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



						$ajax_response = $this->callPUTCurl($url, $data, $header_arr);
						print_r($ajax_response);

					}
					
					/**********Update database value of order exported to avejana*************/
					if($orderId){
						$query= "update sales_flat_order set order_exported_to_avejana = 1 where entity_id=$orderId";
						$write->query($query);
					}
					
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('avejanarms')->__('Incorrect Company ID and/or AveJana Key'));
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('avejanarms')->__('Incorrect Company ID and/or AveJana Key'));
			}catch(Exception $e){
				print_r($e);
			}
		 
		// Mage::getSingleton('adminhtml/session')->addSuccess("Orders has been exported to Avajana");
		$this->_redirectReferer();
	}
	
	
	public function reviewloadAction(){

		try{
			$productid	=	$this->getRequest()->getPost('productid');//die('kk');
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
					
					$totalratings=0;
					foreach($response->message as $reviews){
						$totalratings=$totalratings+$reviews->Ratings;
					}
					//$product					=	Mage::getModel('catalog/product')->load($productid);
					$totalreviewcount			=	count($response->message);
					$averagerating				=	($totalratings/$totalreviewcount)*20;
					
					$action->updateAttributes(array($productid), array(
						'avejana_averagerating' => $averagerating
					), $store_id);
					
					$action->updateAttributes(array($productid), array(
						'avejana_totalreview' => $totalreviewcount
					), $store_id);
					
					echo $this->getLayout()->createBlock('avejanarms/index')->setTemplate('avejanarms/generatedreview.phtml')->toHtml();
				}else{
					$action->updateAttributes(array($productid), array(
						'avejana_averagerating' => 0
					), $store_id);
					
					$action->updateAttributes(array($productid), array(
						'avejana_totalreview' => 0
					), $store_id);
					$returnarr = array(); 
				}
				die;
			}
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

		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

		$response = curl_exec($ch);

		

		return $response;

	}

}