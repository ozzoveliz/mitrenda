<?php

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->addColumn($installer->getTable('customer/customer_group'), 
        'customer_group_is_end_user', 
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
            'comment'   =>  'Is End User'
        )
    );

$installer->endSetup();