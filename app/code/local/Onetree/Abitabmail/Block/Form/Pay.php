<?php
/**
 * Created by PhpStorm.
 * User: Juan Carlos Conde
 * Date: 7/9/14
 * Time: 7:05 PM
 */
class Onetree_Abitabmail_Block_Form_Pay extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('abitabmail/form/pay.phtml');
    }
}
