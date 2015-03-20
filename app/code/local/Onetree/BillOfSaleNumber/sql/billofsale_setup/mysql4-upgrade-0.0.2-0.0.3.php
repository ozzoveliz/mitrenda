<?php

$installer = $this;

$installer->startSetup();

$installer->run("
    ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `billofsale_number` INT(11) DEFAULT NULL;
    ALTER TABLE  `".$this->getTable('sales/invoice')."` ADD  `billofsale_number` INT(11) DEFAULT NULL;
");

$installer->endSetup();