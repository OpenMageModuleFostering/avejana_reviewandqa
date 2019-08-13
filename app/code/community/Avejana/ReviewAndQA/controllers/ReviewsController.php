<?php


class Avejana_ReviewAndQA_ReviewsController extends Mage_Core_Controller_Front_Action{

	protected function _helper(){

		return Mage::helper('reviewandqa');

	}
	
	public function indexAction(){


		$this->loadLayout();


		$this->renderLayout();


	}
	/**
     * Submit new review action
     *
     */
    public function postAction()
    {
    	
        if (!$this->_validateFormKey()) {
            // returns to the product item page
            $this->_redirectReferer();
            return;
        }

        if ($data = Mage::getSingleton('review/session')->getFormData(true)) {
            $rating = array();
            if (isset($data['ratings']) && is_array($data['ratings'])) {
                $rating = $data['ratings'];
            }
        } else {
            $data   = $this->getRequest()->getPost();
            $rating = $this->getRequest()->getParam('ratings', array());
        }

        if (($product = $this->_initProduct()) && !empty($data)) {
            $session = Mage::getSingleton('core/session');
            /* @var $session Mage_Core_Model_Session */
            $product_id = $product->getId();
             $IsAvejanaProductPushed = $product->getAvejanaProductImport();
            if($IsAvejanaProductPushed != '1'){ 
            
            	$product_model = Mage::getModel('catalog/product')->load($product_id);
				
				$this->export_product_to_avejana($product_id);
			}
			
        
            $review = Mage::getModel('review/review')->setData($this->_cropReviewData($data));
            /* @var $review Mage_Review_Model_Review */

            $validate = $review->validate();
            if ($validate === true) {
                try {
                    $review->setEntityId($review->getEntityIdByCode(Mage_Review_Model_Review::ENTITY_PRODUCT_CODE))
                        ->setEntityPkValue($product->getId())
                        ->setStatusId(Mage_Review_Model_Review::STATUS_PENDING)
                        ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->setStores(array(Mage::app()->getStore()->getId()))
                        ->save();

                    foreach ($rating as $ratingId => $optionId) {
                        Mage::getModel('rating/rating')
                        ->setRatingId($ratingId)
                        ->setReviewId($review->getId())
                        ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                        ->addOptionVote($optionId, $product->getId());
                    }

                    $review->aggregate();
                    $session->addSuccess($this->__('Your review has been accepted for moderation.'));
                    Mage::dispatchEvent('controller_action_postdispatch_review_product_post', array('product'=>$product, 'reviews'=>$review));
                }
                catch (Exception $e) {
                    $session->setFormData($data);
                    $session->addError($this->__('Unable to post the review.'));
                }
            }
            else {
                $session->setFormData($data);
                if (is_array($validate)) {
                    foreach ($validate as $errorMessage) {
                        $session->addError($errorMessage);
                    }
                }
                else {
                    $session->addError($this->__('Unable to post the review.'));
                }
            }
        }

        if ($redirectUrl = Mage::getSingleton('review/session')->getRedirectUrl(true)) {
            $this->_redirectUrl($redirectUrl);
            return;
        }
        
        $this->_redirectReferer();
        
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

		$ajax_response = $this->callPUTCurl($url, $data, $header_arr);

		$response = json_decode($ajax_response);
		//print_r($response);
		$status = $response->status;
		$attr = $product->getResource()->getAttribute('avejana_product_import');
		$status = $response->status;
		if($status !='failure')
		{
		$product->setData('avejana_product_import',1);
		$product->getResource()->saveAttribute($product,'avejana_product_import');
		} 	
		return true;

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
     * Initialize and check product
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _initProduct()
    {
        Mage::dispatchEvent('review_controller_product_init_before', array('controller_action'=>$this));
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $productId  = (int) $this->getRequest()->getParam('id');

        $product = $this->_loadProduct($productId);
        if (!$product) {
            return false;
        }

        if ($categoryId) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            Mage::register('current_category', $category);
        }

        try {
            Mage::dispatchEvent('review_controller_product_init', array('product'=>$product));
            Mage::dispatchEvent('review_controller_product_init_after', array(
                'product'           => $product,
                'controller_action' => $this
            ));
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return false;
        }

        return $product;
    }
	
	/**
     * Load product model with data by passed id.
     * Return false if product was not loaded or has incorrect status.
     *
     * @param int $productId
     * @return bool|Mage_Catalog_Model_Product
     */
    protected function _loadProduct($productId)
    {
        if (!$productId) {
            return false;
        }

        $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($productId);
        /* @var $product Mage_Catalog_Model_Product */
        if (!$product->getId() || !$product->isVisibleInCatalog() || !$product->isVisibleInSiteVisibility()) {
            return false;
        }

        Mage::register('current_product', $product);
        Mage::register('product', $product);

        return $product;
    }
    
    /**
     * Crops POST values
     * @param array $reviewData
     * @return array
     */
    protected function _cropReviewData(array $reviewData)
    {
        $croppedValues = array();
        $allowedKeys = array_fill_keys(array('detail', 'title', 'nickname'), true);

        foreach ($reviewData as $key => $value) {
            if (isset($allowedKeys[$key])) {
                $croppedValues[$key] = $value;
            }
        }

        return $croppedValues;
    }
}