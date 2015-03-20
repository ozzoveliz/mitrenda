<?php

class Onetree_FirstData_Model_Observer extends Onetree_FirstData_Model_Payment{
	
	public function addInstallments($observer){
		
		$block = $observer->getEvent()->getBlock();
		$storeId = Mage::app()->getStore()->getStoreId();
		
		if($block->getType() == 'checkout/onepage_payment_methods' && Mage::getStoreConfig('payment/firstdata/installments', $storeId)){
			$block->getChild('payment.method.'.$this->_code)->setTemplate('firstdata/installments.phtml');
		}
	}
	
	public function addAdminInstallmentsInfo($observer){
	
		$payment = $observer->getEvent()->getPayment();
		$order = $payment->getOrder();
		
		$code = $payment->getMethodInstance()->getCode();
		$firstdataCode = Mage::getSingleton('firstdata/payment')->getCode();
		
		$attCode = Onetree_FirstData_Model_Payment::INSTALLMENT_ATTRIBUTE_CODE;
				
		if($code == $firstdataCode && $order->getData($attCode)){			
			$transport = $observer->getEvent()->getTransport();
			$transport->setData(array(Mage::helper('firstdata')->__('Installments')=>$order->getData($attCode)));
		}
	
		return $this;
	}
	
	public function addInstallmentsToOrder($observer){
	
		$order = $observer->getEvent()->getOrder();
		$request = Mage::app()->getRequest();
		
		$firstdataCode = Mage::getSingleton('firstdata/payment')->getCode();
		$attCode = Onetree_FirstData_Model_Payment::INSTALLMENT_ATTRIBUTE_CODE;
		
		if($order->getPayment()->getMethod() == $firstdataCode && $request->getParam($attCode)){
			$order->addData(array($attCode=>(int)$request->getParam($attCode)));
		}
		
		return $order;
	}

    public function allowMethodForIp($observer){
        $method = $observer->getMethodInstance();
        $check  = $observer->getResult();

        if($method->getCode() == 'firstdata' && $check->isAvailable && $method->getConfigData('enable_allow_ips')){

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