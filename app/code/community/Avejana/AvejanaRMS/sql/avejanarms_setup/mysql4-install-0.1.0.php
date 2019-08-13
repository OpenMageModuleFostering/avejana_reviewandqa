<?php
$installer = $this;
$installer->startSetup();
$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('avejana')};
CREATE TABLE {$this->getTable('avejana')} (
  `avejana_id` int(11) unsigned NOT NULL auto_increment,
  `isproductexport` smallint(6) NOT NULL default '0',
  `islogoapiactive` smallint(6) NOT NULL default '0',
  `iscompanyurlgot` smallint(6) NOT NULL default '0',
  `isrichsnippetactive` smallint(6) NOT NULL default '0',
  `companyurl` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`avejana_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 



$installer1= Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer1->startSetup();
$installer1->addAttribute('catalog_product', 'avejana_product_import', array(
		'group'                   => 'General',
		'input'                   => 'select',
		'type'                    => 'int',
		'label'                   => 'Avejana Product Imported',
		'source'                  => 'eav/entity_attribute_source_boolean',
		'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
		'visible'                 => 1,
		'required'                => 0,
		'visible_on_front'        => 0,
		'is_html_allowed_on_front'=> 0,
		'is_configurable'         => 0,
		'searchable'              => 0,
		'filterable'              => 1,
		'comparable'              => 0,
		'unique'                  => false,
		'user_defined'            => false,
		'default'                 => 0,
		'is_user_defined'         => false,
		'used_in_product_listing' => true
	));

$installer1->endSetup();


$installer2 = new Mage_Sales_Model_Mysql4_Setup;
$installer2->startSetup();

$installer2->addAttribute("order", "order_exported_to_avejana", array('type'=>'boolean','default'=>0));
$installer2->endSetup();
	 