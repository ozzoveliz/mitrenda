<?php

$installer = $this;

$installer->startSetup();

$installer->run("

	CREATE TABLE IF NOT EXISTS `{$this->getTable('billofsalenumber/billofsalenumber')}` (
	  `entity_id` int(10) unsigned NOT NULL auto_increment,
	  `bill_number` TEXT NOT NULL,
	  PRIMARY KEY  (`entity_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
$installer->endSetup();

$bill = Mage::getModel('billofsalenumber/billofsalenumber');
$bill->setData('bill_number','1000000');
$bill->save();