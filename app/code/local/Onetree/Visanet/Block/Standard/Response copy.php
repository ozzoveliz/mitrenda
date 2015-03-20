<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */
class Onetree_Visanet_Block_Standard_Response extends Mage_Core_Block_Abstract
{
    public function evalResponse()
    {

        $standard = Mage::getModel('visanet/standard');
        
        $arrayIn = Mage::app()->getRequest()->getParams(); //Array que trae la info de Visa.
        $arrayOut = Array();
        
        $llavePublicaFirma = $standard->getConfig()->getVposSignPub();
        $llavePrivadaCifrado = $standard->getConfig()->getComCryptoPri();
        $VI = $standard->getConfig()->getVi();
        
        $vpos = Mage::getModel('visanet/vpos');

//        $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
//        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        
/**
 * Estos se va despues de hacer pruebas
 */        
//        $order->addStatusToHistory('OK',"Visa autorizó este pago. Orden: ".$orderId, true);
//        $order->setState(Mage_Sales_Model_Order::STATE_COMPLETE, Mage_Sales_Model_Order::STATE_COMPLETE, "Mensaje: ##. Codigo autorizacion: ##", true);
//        $order->save();
//        
//        $invoice = $order->prepareInvoice();
//        $invoice->register()->capture();
//        $invoice->sendEmail(true, $this->__('the bill was paid'));
//        Mage::getModel('core/resource_transaction')
//                ->addObject($invoice)
//                ->addObject($invoice->getOrder())
//                ->save();
//        $order->save();
/**
 * hasta aca se tiene que ir.
 */
        

//        $order->sendOrderUpdateEmail(true, __('The bill was paid (test):').' '.$orderId);
//        zend_debug::dump($order->getTotalPayd());
//        exit();
//        zend_debug::dump($order);
        
        if($vpos->VPOSResponse($arrayIn,$arrayOut,$llavePublicaFirma,$llavePrivadaCifrado,$VI)){
//            zend_debug::dump($arrayOut);
//            exit();
            //La salida esta en $arrayOut con todos los parámetros decifrados devueltos por el VPOS
            $order = Mage::getModel('sales/order')->loadByIncrementId($arrayOut['purchaseOperationNumber']);
            $resultadoAutorizacion = $arrayOut['authorizationResult'];
            
            if($resultadoAutorizacion == "00") {
                $order->addStatusToHistory('OK',"Visa autorizó este pago. Orden: ".$arrayOut['purchaseOperationNumber'], true);
                $order->setState(Mage_Sales_Model_Order::STATE_COMPLETE, Mage_Sales_Model_Order::STATE_COMPLETE, "Mensaje: ".$arrayOut['errorMessage'].". Codigo autorizacion: ".$arrayOut['authorizationCode'], false);
                $order->save();
                
                $invoice = $order->prepareInvoice();
                $invoice->register()->capture();
                $invoice->sendEmail(true, $this->__('the bill was paid'));
                Mage::getModel('core/resource_transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder())
                        ->save();
                $order->save();

                
                Mage::app()->getResponse()->setRedirect('/checkout/onepage/success');
//                echo "success";
//                exit();
            } else {
                $order->addStatusToHistory('Error',"Error al pagar con visa. Orden: ".$arrayOut['purchaseOperationNumber'], true);
                $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, Mage_Sales_Model_Order::STATE_CANCELED, $arrayOut['errorCode']." : ".$arrayOut['errorMessage'], false);
                $order->save();
                Mage::app()->getResponse()->setRedirect('/checkout/onepage/failure');
//                echo "failure";
//                exit();
            }
        }else{
            
            exit('error');
            //Puede haber un problema de mala configuración de las llaves, vector de
            //inicializacion o el VPOS no ha enviado valores correctos
        }
       
    }
}
