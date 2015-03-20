<?php
/**
 * Created by PhpStorm.
 * User: Juan Carlos Conde
 * Date: 7/8/14
 * Time: 4:06 AM
 */ 
class Onetree_BillOfSaleNumber_Model_Resource_Billofsalenumber extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('billofsalenumber/billofsalenumber','entity_id');
    }

}