<?php
$installer3= Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer3->startSetup();
$installer3->addAttribute('catalog_product', 'avejana_averagerating', array(
		'group'           => 'General',
            'label'           => 'Avejana Average Rating',
            'input'           => 'text',
            'type'            => 'int',
            'required'        => 0,
            'visible_on_front'=> 1,
            'filterable'      => 0,
            'searchable'      => 0,
            'comparable'      => 0,
            'user_defined'    => 1,
            'is_configurable' => 1,
            'global'          => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'note'            => 'Do not enter value in this attribute, this is disabled',
		'unique'                  => false,
		'user_defined'            => false,
		'default'                 => 0,
		'is_user_defined'         => false,
		'used_in_product_listing' => true
	));


$installer3->endSetup();


$installer4= Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer4->startSetup();
$installer4->addAttribute('catalog_product', 'avejana_totalreview', array(
		'group'           => 'General',
		'label'           => 'Avejana Total Review',
		'input'           => 'text',
		'type'            => 'int',
		'required'        => 0,
		'visible_on_front'=> 1,
		'filterable'      => 0,
		'searchable'      => 0,
		'comparable'      => 0,
		'user_defined'    => 1,
		'is_configurable' => 1,
		'global'          => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
		'note'            => 'Do not enter value in this attribute, this is disabled',
		'unique'                  => false,
		'user_defined'            => false,
		'default'                 => 0,
		'is_user_defined'         => false,
		'used_in_product_listing' => true
	));

$installer4->endSetup();

$installer5= Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer5->startSetup();
$installer5->addAttribute('catalog_product', 'avejana_totalqa', array(
		'group'           => 'General',
		'label'           => 'Avejana Total Question Answer',
		'input'           => 'text',
		'type'            => 'int',
		'required'        => 0,
		'visible_on_front'=> 1,
		'filterable'      => 0,
		'searchable'      => 0,
		'comparable'      => 0,
		'user_defined'    => 1,
		'is_configurable' => 1,
		'global'          => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
		'note'            => 'Do not enter value in this attribute, this is disabled',
		'unique'                  => false,
		'user_defined'            => false,
		'default'                 => 0,
		'is_user_defined'         => false,
		'used_in_product_listing' => true
	));

$installer5->endSetup();
