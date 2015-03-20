<?php

$installer = $this;

$installer->startSetup();

$installer->run("
    ALTER TABLE  `".$this->getTable('sales/order_grid')."` ADD  `billofsale_number` INT(11) DEFAULT NULL;
");

$installer->endSetup();