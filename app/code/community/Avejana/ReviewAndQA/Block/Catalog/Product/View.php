<?php

/**
 * Magpleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE.txt
 *
 * @category   Magpleasure
 * @package    Avejana_ReviewAndQA
 * @copyright  Copyright (c) 2014-2015 Magpleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE.txt
 */
class Avejana_ReviewAndQA_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View
{

    /**
     * Rich snippets helper
     *
     * @return Avejana_ReviewAndQA_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('reviewandqa');
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
    	$html = parent::_toHtml();
        if ($this->getSnippetsStatus() == '1') {
            $html = $this->_insertRichSnippets($html);
        }
        return $html;
    }

    /**
     * Insert product schema.org markup
     *
     * @param $html
     * @return mixed
     */
    protected function _insertRichSnippets($html)
    {
        $markup = $this->getLayout()->getBlock('mp.reviewandqa')->toHtml();
        $html = preg_replace('/(div.*?class=".*?product-view.*?".*?)>/', '$1 itemscope itemtype="http://schema.org/Product">' . $markup, $html);
        return $html;
    }
    
    
		public function getSnippetsStatus()
	    {	
	    	$snippets_status = '0';
	        $snippets_enable_url = 'http://company.avejana.com/api/snippets';
		
			$data = array(
				'CompanyID' => $this->_helper()->getCompanyId()
			);
			$header_arr = array(
				"rest-ajevana-key: ".$this->_helper()->getApiKey()."",
				"user-id: ".$this->_helper()->getUserId().""
			);  
			$final_url = $snippets_enable_url . "?" . http_build_query($data);
			$ajax_response = $this->_helper()->callGETCurl($final_url, $data, $header_arr);
			$response = json_decode($ajax_response);
			
			$status = $response->status;
			if($status == 'success'){
				$snippets_status = $response->message;
			}
	        return $snippets_status;
	    }
}