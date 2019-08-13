<?php

class Avejana_ReviewAndQA_Block_Product_View_Tabs extends Mage_Core_Block_Template{
	protected $_tabs = array();


	/**
	* Add tab to the container
	*
	* @param string $title
	* @param string $block
	* @param string $template
	*/
	function addTab($alias, $title, $block, $template){

		if(!$title || !$block || !$template){
			return false;
		}

		$this->_tabs[] = array(
			'alias' => $alias,
			'title' => $title
		);

		$this->setChild($alias,
			$this->getLayout()->createBlock($block, $alias)
			->setTemplate($template)
		);
	}

	/**
	* Add cms block tab to the container
	*
	* @param string $title
	* @param string $block
	* @param string $template
	*/
	function addCmsTab($alias, $title, $block, $id){

		if(!$title || !$block || !$id){
			return false;
		}

		$this->_tabs[] = array(
			'alias' => $alias,
			'title' => $title
		);

		$this->setChild($alias,
			$this->getLayout()->createBlock('cms/block')->setBlockId($id)
		);
	}

	function getTabs(){
		
		if(Mage::helper('reviewandqa')->descriptionTab()){
			$this->addTab('description',
				$this->__('Description'),
				'catalog/product_view_description',
				'reviewandqa/product/view/description.phtml');
		}
        
		if(Mage::helper('reviewandqa')->additionalTab()){
			$this->addTab('additional',
				$this->__('Additional Information'),
				'catalog/product_view_attributes',
				'reviewandqa/product/view/attributes.phtml');
		}
        
		if(Mage::helper('reviewandqa')->reviewsTab()){
			$this->addTab('reviews',
				$this->__('Product Review(s)'),
				'reviewandqa/product_view_reviews',
				'reviewandqa/product/view/reviews.phtml');
		}
        
		if(Mage::helper('reviewandqa')->questionsTab()){
			$this->addTab('questions',
				$this->__('Product QA(s)'),
				'reviewandqa/product_view_questions',
				'reviewandqa/product/view/questions.phtml');
		}
        
		return $this->_tabs;
	}
}
