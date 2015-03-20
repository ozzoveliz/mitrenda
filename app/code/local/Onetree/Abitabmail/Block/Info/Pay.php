<?php
/**
 * Created by PhpStorm.
 * User: Juan Carlos Conde
 * Date: 7/9/14
 * Time: 8:46 PM
 */
class Onetree_Abitabmail_Block_Info_Pay extends Mage_Payment_Block_Info
{
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $info = $this->getInfo();
        $transport = new Varien_Object();
        $transport = parent::_prepareSpecificInformation($transport);
        $transport->addData(array(
            Mage::helper('payment')->__('Card Identity') => $info->getCardIdentity()
        ));
        return $transport;
    }
}