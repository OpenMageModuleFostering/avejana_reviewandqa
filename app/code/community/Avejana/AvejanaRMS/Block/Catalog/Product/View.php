<?php



/**


 * This source file is subject to the EULA

 * that is bundled with this package in the file LICENSE.txt.

 * It is also available through the world-wide-web at this URL:



 *

 * @category   Arjun Dhiman

 * @package    Avejana_AvejanaRMS


 */

class Avejana_AvejanaRMS_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View

{



    /**

     * Rich snippets helper

     *

     * @return Avejana_AvejanaRMS_Helper_Data

     */

    protected function _helper()

    {

        return Mage::helper('avejanarms');

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

        $markup = $this->getLayout()->getBlock('mp.avejanarms')->toHtml();

        $html = preg_replace('/(div.*?class=".*?product-view.*?".*?)>/', '$1 itemscope itemtype="http://schema.org/Product">' . $markup, $html);

        return $html;

    }

    

    

		public function getSnippetsStatus()

	    {	

	    	$snippets_status = '0';

            //$snippets_enable_url = Mage::getStoreConfig('apiconfig/snippets_api/send_url');

	        $snippets_enable_url = Mage::helper('avejanarms')->getCompanyUrl().'/api/snippets/';
			$data = array(

				'CompanyID' => Mage::helper('avejanarms')->getCompanyId()

			);

			$header_arr = array(

				"rest-ajevana-key: ".Mage::helper('avejanarms')->getApiKey()."",

				"user-id: ".Mage::helper('avejanarms')->getUserId().""

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