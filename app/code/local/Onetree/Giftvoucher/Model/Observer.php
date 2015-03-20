<?php

class Onetree_Giftvoucher_Model_Observer
{
    public function paymentMethodIsActive(Varien_Event_Observer $observer) {
        $event           = $observer->getEvent();
        $method          = $event->getMethodInstance();
        $result          = $event->getResult();
        if($method->getCode() == 'checkmo'){
            $cart = Mage::getSingleton('checkout/cart')->getQuote();
            $size = count($cart->getAllItems());
            if ($size > 0) {
                foreach ($cart->getAllItems() as $item) {
                    if ($item->getProduct()->getTypeId() == 'giftvoucher') {
                        $result->isAvailable = false;
                        return;
                    }
                }
            }
        }
    }
}