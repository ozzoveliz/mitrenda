<?php

$installer = $this;

$orderTable     = $this->getTable('sales/order');
$invoiceTable   = $this->getTable('sales/invoice');
$orderGridTable = $this->getTable('sales/order_grid');
$tables = array($orderTable,$invoiceTable,$orderGridTable);

$installer->startSetup();

foreach($tables as $table){
    $installer->getConnection()->modifyColumn($table,'billofsale_number','TEXT NOT NULL');
}

$installer->endSetup();