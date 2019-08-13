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
class Avejana_AvejanaRMS_Block_Catalog_Product_View_Markup extends Mage_Catalog_Block_Product_View
{
    const CACHE_TAG = 'MP_RS_MARKUP';

    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addData(array(
            'cache_lifetime' => 3600,
            'cache_tags' => array(Mage_Catalog_Model_Product::CACHE_TAG . "_" . $this->getProduct()->getId()),
            'cache_key' => self::CACHE_TAG . '_' . Mage::app()->getStore()->getCode() . '_' . $this->getProduct()->getId(),
        ));
    }

    /**
     * Rich snippets helper
     *
     * @return Avejana_ReviewAndQA_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('avejanarms');
    }

    
    

    
}