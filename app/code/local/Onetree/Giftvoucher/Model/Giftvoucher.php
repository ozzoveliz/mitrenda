<?php
/**
 * Created by PhpStorm.
 * User: Juan Carlos Conde
 * Date: 12/20/14
 * Time: 9:13 AM
 */ 
class Onetree_Giftvoucher_Model_Giftvoucher extends Magestore_Giftvoucher_Model_Giftvoucher
{
    public function getBalanceFormated() {
        $currency = Mage::getModel('directory/currency')->load($this->getCurrency());
        $balance = $currency->format($this->getBalance(),array('precision'=>0));
        $balance = str_replace('$U', '$', $balance);
        return $balance;
    }
}