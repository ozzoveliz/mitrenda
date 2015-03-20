<?php

class Onetree_Cart_Model_Observer extends Varien_Object {

    public function afterAddToCart(Varien_Event_Observer $observer) {
        $checkout = Mage::app()->getRequest()->getParam('checkout');
        
        if (isset($checkout)) {
            $response = $observer->getResponse();
            $response->setRedirect(Mage::getUrl('checkout/onepage'));
            Mage::getSingleton('checkout/session')->setNoCartRedirect(true);
        }
    }

}
