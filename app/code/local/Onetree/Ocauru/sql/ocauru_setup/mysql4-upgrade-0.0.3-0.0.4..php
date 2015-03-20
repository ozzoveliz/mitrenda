<?php

$installer = $this;
$installer->startSetup();

$definition = array(
                    'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '1',
                    'comment'   =>  'Is Final Customer'
                );

$installer  ->getConnection()
            ->modifyColumn($installer->getTable('customer/customer_group'),'customer_group_is_end_user',$definition);

$customerGroups = Mage::getResourceModel('customer/group_collection');
foreach ($customerGroups as $group){
    $group->setData('customer_group_is_end_user',1);
    $group->save();
}

$installer->endSetup();