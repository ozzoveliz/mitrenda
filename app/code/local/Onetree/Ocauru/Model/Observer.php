<?php

class Onetree_Ocauru_Model_Observer extends Onetree_Ocauru_Model_Standard{
	
	public function addInstallments($observer){
		
		$block = $observer->getEvent()->getBlock();
		$storeId = Mage::app()->getStore()->getStoreId();
		
		if($block->getType() == 'checkout/onepage_payment_methods' && Mage::getStoreConfig('payment/ocauru/installments', $storeId)){
			$block->getChild('payment.method.'.$this->_code)->setTemplate('ocauru/installments.phtml');
		}	
	}
	
	public function addAdminInstallmentsInfo($observer){
		
		$payment = $observer->getEvent()->getPayment();
		$order = $payment->getOrder();
		
		$code = $payment->getMethodInstance()->getCode();
		$ocaCode = Mage::getSingleton('ocauru/standard')->getCode();
		
		$attCode = Onetree_Ocauru_Model_Standard::INSTALLMENT_ATTRIBUTE_CODE;
		
		if($code == $ocaCode && $order->getData($attCode)){
			$transport = $observer->getEvent()->getTransport();
			$transport->setData(array(Mage::helper('ocauru')->__('Installments')=>$order->getData($attCode)));			
		}

		return $this;
	}
	
	public function addInstallmentsToOrder($observer){
		
		$order = $observer->getEvent()->getOrder();
		$request = Mage::app()->getRequest();
		
		$ocaCode = Mage::getSingleton('ocauru/standard')->getCode();
		$attCode = Onetree_Ocauru_Model_Standard::INSTALLMENT_ATTRIBUTE_CODE;
		
		if($order->getPayment()->getMethod() == $ocaCode && $request->getParam($attCode)){
			$order->addData(array($attCode=>(int)$request->getParam($attCode)));
		}
		
		return $order;
	}
    public function allowMethodForIp($observer){
        $method = $observer->getMethodInstance();
        $check  = $observer->getResult();

        if($method->getCode() == 'ocauru' && $check->isAvailable && $method->getConfigData('enable_allow_ips')){

            $allowIps       = $method->getConfigData('allow_ips');
            $allowIps       = explode(',',$allowIps);

            if((is_array($allowIps) && count($allowIps) > 0)){
                $remoteId   = Mage::helper('core/http')->getRemoteAddr();
                $condition = in_array($remoteId,$allowIps);
                $check->isAvailable = $condition;
            }
        }

        return $this;
    }
}