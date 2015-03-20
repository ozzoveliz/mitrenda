<?php
/**
 * Created by PhpStorm.
 * User: Juan Carlos Conde
 * Date: 6/28/14
 * Time: 10:11 PM
 */
class Onetree_Abitabmail_Model_Method_Mail extends Mage_Payment_Model_Method_Abstract
{
    const ABITABMAIL = 'abitabmail';
    protected $_code = 'abitabmail';

    protected $_isInitializeNeeded      = true;
    protected $_canUseInternal          = true;
    protected $_canUseForMultishipping  = false;

    protected $_formBlockType = 'onetree_abitabmail/form_pay';
    protected $_infoBlockType = 'onetree_abitabmail/info_pay';

//    public function getOrderPlaceRedirectUrl() {
//        return Mage::getUrl('mygateway/payment/redirect', array('_secure' => true));
//    }

    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();
        $info->setCardIdentity($data->getCardIdentity());
        return $this;
    }


    public function validate()
    {
        parent::validate();

        $info = $this->getInfoInstance();

        $cardIdentity = $info->getCardIdentity();
        $errorMsg = '';
        if(empty($cardIdentity)){
            $errorMsg = $this->_getHelper()->__('Card Identity is a required field');
            Mage::throwException($errorMsg);
        }
        $validator = new Zend_Validate_Digits();
        if (!$validator->isValid($cardIdentity)) {
            $errorMsg .= $this->_getHelper()->__("Card Identity must contain only digits");
            Mage::throwException($errorMsg);
        }
        $validator = new Zend_Validate_StringLength(array('min'=>7, 'max'=>8));
        if (!$validator->isValid($cardIdentity)) {
            $errorMsg .= $this->_getHelper()->__("Card Identity must contain between 7 and 8 digits");
            Mage::throwException($errorMsg);
        }

        if (strlen($cardIdentity) == 7) {
            $cardIdentity = "0".$cardIdentity;
        }

        $ci = $cardIdentity;
        $ci = str_split($ci);
        $s = 2*$ci[0] + 9*$ci[1] + 8*$ci[2] + 7*$ci[3] + 6*$ci[4] + 3*$ci[5] + 4*$ci[6];
        $M = $s % 10;
        $h = (10 - $M) % 10;

        if ($h == $ci[7]) {
            return $this;
        } else {
            $errorMsg = $this->_getHelper()->__('The card identity is invalid, please try again.');
            Mage::throwException($errorMsg);
        }
    }
}