<?php
/**
 * Created by PhpStorm.
 * User: Juan Carlos Conde
 * Date: 7/8/14
 * Time: 4:06 AM
 */ 
class Onetree_Abitabmail_Model_Resource_Mail_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('onetree_abitabmail/mail');
    }

}