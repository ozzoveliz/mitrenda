<?php

class Onetree_BillOfSaleNumber_Model_Billofsalenumber extends Mage_Core_Model_Abstract {

    private static $in_order_methods = array('ocauru', 'firstdata', 'visanet');

    protected function _construct()
    {
        parent::_construct();
        $this->_init('billofsalenumber/billofsalenumber');
    }

    public static function canAddNumber($payment) {
        $apply = false;

        if(in_array(strtolower($payment->getMethodInstance()->getCode()), self::$in_order_methods)){
            $apply = true;
        }

        return $apply;
    }
    public function validate(){
        $errors = false;

        if (!Zend_Validate::is( trim($this->getBillNumber()) , 'NotEmpty')) {
            $errors[] = Mage::helper('billofsalenumber')->__('Bill can not be empty');
        }

        $bill = $this->getBillNumber();
        if (strlen($bill) && !Zend_Validate::is($bill, 'StringLength', array(7))) {
            $errors[] = Mage::helper('customer')->__('The minimum password length is %s', 7);
        }
        return $errors;
    }
}