<?php
class Avejana_AvejanaRMS_QaController extends Mage_Core_Controller_Front_Action{
    
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
     * Submit new Question action
     *
     */
    public function qapostAction(){

		try{

				$data   		= 	$this->getRequest()->getPost();
				$product_id    	= 	$data['product_id'];
				$session        =   Mage::getSingleton('catalog/session');
				$qaapiurl 		= 	Mage::helper('avejanarms')->getCompanyUrl().'/api/question/';

				if(!empty($data) && ($product_id)){
					$product_model			= 	Mage::getModel('catalog/product')->load($product_id);
					$IsAvejanaProductPushed = 	$product_model->getAvejanaProductImport();

					if(empty($IsAvejanaProductPushed) || ($IsAvejanaProductPushed != '1')){ 

						$this->export_product_to_avejana($product_model);
					}

				$postdata = array(

					'FromCompany' => Mage::helper('avejanarms')->getCompanyId(),

					'ProductID' => $data['product_id'],

					'InternalQNAID' => rand(10,10000),

					'IsPrivate' => 3,

					'UserName' =>$data['name'],

					'UserEmail' => $data['email'],

					'Question' => $data['question']


				);

				$header_arr = array(

					"content-type: application/x-www-form-urlencoded",

					"rest-ajevana-key: ".Mage::helper('avejanarms')->getApiKey()."",

					"user-id: ".Mage::helper('avejanarms')->getUserId().""
				); 


				$ajax_response = $this->callPUTCurl($qaapiurl, $postdata, $header_arr);	
				//print_r($ajax_response);die;
				$ajax_response = json_decode($ajax_response);
				if($ajax_response){
					$status = $ajax_response->status;

					if($status !='failure')
					{
						$session->addSuccess(Mage::helper('avejanarms')->__('Thank You! We\'ll reply to your question as soon as possible '));
					} 	
				}

			}
		}catch(Exception $e){
			print_r($e);
			$session->addError($this->__('Unable to post the Question.'));
		}
	
		$this->_redirectReferer();
	}
	
	/**
     * Export Single product to avejan.
     *
     */
    
	 public function export_product_to_avejana($product){	

		 echo $url = Mage::helper('avejanarms')->getUserUrl().'/api/product/';

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
			//print_r($response);die;
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
        
		try{
			$ch = curl_init($url);

			curl_setopt($ch, CURLOPT_HEADER, false);

			curl_setopt($ch, CURLOPT_HTTPHEADER, $header_arr);

			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			curl_setopt($ch, CURLOPT_POST, true);

			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
			
			$response = curl_exec($ch);
		
			return $response;

		}catch(Exception $e){
			print_r($e);
		}
		

	}
	
	
	
	/**
     * Upload Sales Data to Avejana after button click
     *
     */
    
	 public function uploadsalesAction(){	
		try{
			 echo $url = Mage::helper('avejanarms')->getUserUrl().'/api/product/';
			 echo 'upload sales data';
			 Mage::getSingleton('adminhtml/session')->addSuccess("Message");
			$this->_redirectReferer();
		
		}catch(Exception $e){
			print_r($e);
		}

	}
}