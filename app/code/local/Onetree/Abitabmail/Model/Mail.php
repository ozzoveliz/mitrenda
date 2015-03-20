<?php
/**
 * Created by PhpStorm.
 * User: Juan Carlos Conde
 * Date: 7/8/14
 * Time: 4:06 AM
 */

/**
 * @method string getContent()
 * @method Onetree_Abitabmail_Model_Mail setContent(string $value)
 * @method int getAbitabmailId()
 * @method Onetree_Abitabmail_Model_Mail setAbitabmailId(int $value)
 * @method string getCreatedAt()
 * @method Onetree_Abitabmail_Model_Mail setCreatedAt(string $value)
 */
class Onetree_Abitabmail_Model_Mail extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('onetree_abitabmail/mail');
    }

}