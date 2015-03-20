<?php
/**
 * Created by PhpStorm.
 * User: TOSHIBA
 * Date: 5/30/14
 * Time: 5:27 PM
 */ 
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = Mage::getResourceModel('catalog/setup', 'default_setup');

$installer->startSetup();

$data = array(
    'label'         => 'Is LookBook',
    'type'          => 'int', // multiselect uses comma - sep storage
    'input'         => 'select',
    'source'        => 'eav/entity_attribute_source_boolean',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false, // defaults to false; if true, define a group
    'group'         => 'General Information',
    'default'       => 0,
    'note'          => 'Establece la categoria como lookbook para cargar una plantilla diferente',
);
$installer->addAttribute('catalog_category', 'is_lookbook', $data);

$installer->endSetup();