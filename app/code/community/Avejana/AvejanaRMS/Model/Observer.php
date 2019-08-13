<?php

class Avejana_AvejanaRMS_Model_Observer{

	
	public function handle_adminSystemConfigChangedSection($observer){
			
			$resource = Mage::getSingleton('core/resource');
			$writeConnection = $resource->getConnection('core_write');
			$table = $resource->getTableName('core_config_data');
			
			$url 				= 	Mage::helper('avejanarms')->getCompanyUrl().'/api/get_company_url';
			$sectiondata 		= 	Mage::app()->getRequest()->getParams();
			$is_module_active	=	$sectiondata['groups']['avejanaconfiguration']['fields']['active']['value'];
			$companyid			=	$sectiondata['groups']['avejanaconfiguration']['fields']['companyid']['value'];
			$avejanakey			=	$sectiondata['groups']['avejanaconfiguration']['fields']['avejanakey']['value'];
			
			if($is_module_active==1){

				$query = "UPDATE {$table} SET value = 1 WHERE path = 'advanced/modules_disable_output/Mage_Review' OR path = 'advanced/modules_disable_output/Mage_Rating'";

				$writeConnection->query($query);
			}else{
				
				$query = "UPDATE {$table} SET value = 0 WHERE path = 'advanced/modules_disable_output/Mage_Review' OR path = 'advanced/modules_disable_output/Mage_Rating'";

				$writeConnection->query($query);
				
			}
			
			$userurl=$this->getCompanyUrl($url, $companyid, $avejanakey);
			
			Mage::getConfig()->saveConfig('avejanasetting/avejanaconfiguration/companyurl', $userurl, 'default', 0);
			
			$this->exportProductCron(); /****export all product to avejana*****/
	}
	

	public function exportProductCron(){			
	
		$productModel =  Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('avejana_product_import',0);
		$productData = $productModel->getData();
		foreach($productData as $product){
			$product_id = $product['entity_id'];
			$product_model = Mage::getModel('catalog/product')->load($product_id);
			$attribute_value = $product_model->getResource()->getAttribute('avejana_product_import')->getFrontend()->getValue($product_model);
			if($attribute_value =='No')
			{
					
				$this->export_product_to_avejana($product_model);
			}
			
		}		
		return true;

	}

	public function export_review_to_avejana($observer){

		try
		{
			$url = Mage::helper('avejanarms')->getCompanyUrl().'/api/review/';

			$event = $observer->getEvent();
			$reviewdata = $event->getreviewdata();

			$data = array(

				'FromCompany' => Mage::helper('avejanarms')->getCompanyId(),

				'ProductID' => $reviewdata['product_id'],

				'InternalReviewID' => rand(10,10000),

				'IsPrivate' => 3,

				'UserName' =>$reviewdata['name'],

				'UserEmail' => $reviewdata['email'],

				'Title' => $reviewdata['title'],

				'Ratings' => $reviewdata['rating'],

				'Description' => $reviewdata['comment'],


			);

			$header_arr = array(

				"content-type: application/x-www-form-urlencoded",

				"rest-ajevana-key: ".Mage::helper('avejanarms')->getApiKey()."",

				"user-id: ".Mage::helper('avejanarms')->getUserId().""
			); 


			$ajax_response = $this->callPUTCurl($url, $data, $header_arr);
			return true;
					//print_r($ajax_response);exit;


		}catch(Exception $e)
		{
			print_r($e);
		}


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

				"user_id: ".Mage::helper('avejanarms')->getUserId().""
			); 
			
			$ajax_response = $this->callPUTCurl($url, $data, $header_arr);

			$response = json_decode($ajax_response);
			//print_r($ajax_response);die;
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
	
	/************Export product to avejana when save in admin*******************/
	
	public function export_product_to_avejana_after_save($observer){	

		$product 	= $observer->getEvent()->getProduct();

		$product_id = $product->getId();

		$product	= Mage::getModel('catalog/product')->load($product_id);

		
		$this->export_product_to_avejana($product);

		return true;

	}

	
	/************send Order API when order placed in magento*************************/
	public function send_salesdata_to_avejana($observer){
		
		$url = Mage::helper('avejanarms')->getUserUrl().'/api/sales/';
		
		$con	=	Mage::getSingleton('core/resource');
    		 
		$write	=	$con->getConnection('core_write');

		$order = $observer->getOrder();

		$increment_id = $order->getIncrementId();
		
		$orderId 	= $order->getId();

		$order_date = date_format(date_create($order->getCreatedAtStoreDate()),"Y-m-d");

		$customer_id = $order->getCustomerId();

		$privacy = '1';

		$customer_name 	= 	$order->getCustomerName();

		$customer_email = 	$order->getCustomerEmail();
		
		$customer_dob	=	$order->getCustomerDob();
		
		$customer_gender=	$order->getCustomerGender();

		$data = array(

			'FromCompany' => Mage::helper('avejanarms')->getCompanyId(),

			'OrderID' => $increment_id,

			'OrderDate' => $order_date,

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

			$product_price = $item->getPrice();

			$pid = $item->getProductId();

			$product= Mage::getModel('catalog/product')->load($pid);

			$this->export_product_to_avejana($product);/*********Product export to avejana************/

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
//echo '<pre>'; print_r($ajax_response);
			
		}
		
		/**********Update database value of order exported to avejana*************/
			if($orderId){
				$query= "update sales_flat_order set order_exported_to_avejana = 1 where entity_id=$orderId";
				$write->query($query);
			}
			return true;
	}

	public function export_order_to_avejana_place($observer){

		


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
	
	/**
     * Upload Sales Data to Avejana after button click
     *
     */
    
	 public function uploadsales($observer){	
		 
		try{
			
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
		 return true;
		// Mage::getSingleton('adminhtml/session')->addSuccess("Orders has been exported to Avajana");
		//$this->_redirectReferer();
		 
	}
	
	public function getCompanyUrl($url, $companyid, $avejanakey){
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
			"REST-AJEVANA-KEY: $avejanakey",
			"user_id: $companyid"
		  ),
		));

		$ajax_response = curl_exec($curl);
		$err = curl_error($curl);
		$response = json_decode($ajax_response);
		curl_close($curl);
		//print_r($response);die;
		if($response->status=='success'){
			return $response->message;
		}else{
			
			 Mage::getSingleton('adminhtml/session')->addError(Mage::helper('avejanarms')->__('Incorrect Company ID and/or AveJana Key'));
			return true;
		}
	}
	
}

