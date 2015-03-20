<?php
/**
 * Created by PhpStorm.
 * User: Juan Carlos Conde
 * Date: 6/27/14
 * Time: 8:08 PM
 */ 
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS `{$this->getTable('onetree_abitabmail/abitabmail')}`;
CREATE TABLE `{$this->getTable('onetree_abitabmail/abitabmail')}` (
  `abitabmail_id` INT(11) NOT NULL AUTO_INCREMENT,
  `content` TEXT NOT NULL,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`abitabmail_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE `{$installer->getTable('sales/quote_payment')}` ADD `card_identity` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `{$installer->getTable('sales/order_payment')}` ADD `card_identity` VARCHAR( 255 ) NOT NULL;

");

$installer->endSetup();