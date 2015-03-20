<?php

class Onetree_Ocauru_Block_Installments extends Mage_Core_Block_Template{
	
	protected $_allowedInstallments = array();
	protected $_checkout = null;
	protected $_quote    = null;
	
	public function getInstallmentsQty() {
        $standard = Mage::getModel('ocauru/standard');
        $installments = $standard->getConfigData('installments');

        if(!empty($installments)) {
            $this->_allowedInstallments = explode(',', $installments);
        }

        return $this->_allowedInstallments;
    }
	
 	public function getCheckout(){
 		
        if (null === $this->_checkout) {
            $this->_checkout = Mage::getSingleton('checkout/session');
        }
        return $this->_checkout;
    }

    public function getQuote(){
    	
        if (null === $this->_quote) {
            $this->_quote = $this->getCheckout()->getQuote();
        }
        return $this->_quote;
    }

    public function getItems(){
    	
        return $this->getQuote()->getAllVisibleItems();
    }
}

?>