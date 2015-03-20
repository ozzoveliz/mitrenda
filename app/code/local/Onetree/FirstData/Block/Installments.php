<?php

class Onetree_FirstData_Block_Installments extends Mage_Core_Block_Template{
	
	protected $_allowedInstallments = array();
	protected $_checkout = null;
	protected $_quote    = null;
	
	public function getInstallmentsQty(){
		
		if(!$this->_allowedInstallments){
			$prod = Mage::getModel('catalog/product');
			foreach($this->getItems() as $item){
				$a = $prod->load($item->getProductId());
				$opts = $a->getAttributeText(Onetree_FirstData_Model_Payment::INSTALLMENT_ATTRIBUTE_CODE);
				if(count($opts)> 1 && (int)$opts[0] > 1){
					$this->_allowedInstallments = $opts;
				} 
			}				
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