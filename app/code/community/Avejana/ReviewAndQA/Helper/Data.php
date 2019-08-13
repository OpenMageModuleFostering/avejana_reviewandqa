<?php

class Avejana_ReviewAndQA_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function descriptionTab(){
        return Mage::getStoreConfig('tabssection/general/description');
    }
    
    public function additionalTab(){
        return Mage::getStoreConfig('tabssection/general/additional');
    }
    
    public function reviewsTab(){
        return Mage::getStoreConfig('tabssection/general/reviews');
    }

    public function questionsTab(){
        return Mage::getStoreConfig('tabssection/general/questions');
    }
    
    public function getCompanyId(){
        return Mage::getStoreConfig('apiconfig/configurations/company_id');
    }
    
    public function getUserId(){
        return Mage::getStoreConfig('apiconfig/configurations/user_id');
    }
    
    public function getApiKey(){
        return Mage::getStoreConfig('apiconfig/configurations/api_key');
    }    
    
    public function getConfiguredPrice($product)
    {
        if (Mage_Catalog_Model_Product_Type::TYPE_BUNDLE != $product->getTypeId()) {
            return 0;
        }
        $priceModel = $product->getPriceModel();
        $bundleBlock = Mage::getSingleton('core/layout')->createBlock('bundle/catalog_product_view_type_bundle');
        $options = $bundleBlock->setProduct($product)->getOptions();
        $price = 0;
        /** @var Mage_Bundle_Model_Option $option */
        foreach ($options as $option) {
            $selection = $option->getDefaultSelection();
            if (null === $selection) {
                continue;
            }
            $price += $priceModel->getSelectionPreFinalPrice($product, $selection, $selection->getSelectionQty());
        }
        return $price;
    }

    public function getGroupedProductPrice($groupedProduct, $incTax = true)
    {
        $productIds = $groupedProduct->getTypeInstance()->getChildrenIds($groupedProduct->getId());
        $price = 0;
        foreach ($productIds as $ids) {
            foreach ($ids as $id) {
                $product = Mage::getModel('catalog/product')->load($id);
                if ($incTax) {
                    $price += Mage::helper('tax')->getPrice($product, $product->getPriceModel()->getFinalPrice(null, $product, true), true);
                } else {
                    $price += $product->getPriceModel()->getFinalPrice(null, $product, true);
                }
            }
        }
        return $price;
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