<?php

$installer = $this;
$installer->startSetup();

$attrCode = Onetree_FirstData_Model_Payment::INSTALLMENT_ATTRIBUTE_CODE;
$attrGroupName = 'General';
$attrLabel = 'Firstdata Installment';
$attrNote = 'If no option selected, then the product will not allow installments.';

$attrIdTest = $installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attrCode);
if ($attrIdTest === false) {
	
	$option = array();
	for($i=1;$i<13;$i++){
		$option['values'][] = ($i == 1)? 'no installments allowed' : $i;
	}
	
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attrCode, array(
        'group' => $attrGroupName,
        'sort_order' => 7,
        'type' => 'varchar',
        'backend' => 'eav/entity_attribute_backend_array',
        'frontend' => '',
        'label' => $attrLabel,
        'note' => $attrNote,
        'input' => 'multiselect',
    	'option'=>$option,
        'class' => '',
        'source' => '',
    	'searchable'=> false,
    	'filterable'=> false,
    	'comparable'=> false,
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => true,
        'default' => '0',
        'visible_on_front' => false,
        'unique' => false,
        'is_configurable' => false,
        'used_for_promo_rules' => true
    ));
}

$installer->getConnection()->addColumn($this->getTable('sales_flat_order'), $attrCode, 'int(10) NULL');

$installer->endSetup();