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
class Onetree_Ocauru_Block_Response extends Mage_Core_Block_Abstract
{
    public function evalResponse($order)
    {

        $arrayIn = Mage::app()->getRequest()->getParams(); //Array que trae la info de OCA.
        // Zend_Debug::dump($arrayIn);
        extract($arrayIn);
        //$order = Mage::getModel('sales/order')->loadByIncrementId($arrayIn['Info']);
        $standard = Mage::getModel('ocauru/standard');
        $installments	= $order->getData('oca_installment');

        if(!array_key_exists('Idtrn',$arrayIn)){
            $order->addStatusHistoryComment('No se registro valor Idtrn en orden');
            $order->save();
            return false;
        }

        $array_send["Idtrn"] = $arrayIn['Idtrn'];
        $array_send["Nrocom"] = $standard->getConfigData('nrocom');
        $array_send["Nroterm"] = $standard->getConfigData('nroterm');
        $array_send["Moneda"] = $standard->getConfigData('currency');
        $array_send['Importe']=number_format($order->getGrandTotal(),0)."00"; // El valor a oca deber ir con dos ceros al final y sin comas ni puntos.
        $array_send['Importe'] = str_replace(",", "", $array_send['Importe']); // Se le quitan las comas para cuando el numero es mayor a mil.
        $array_send['Plan'] = ($installments)? str_pad($installments, 3, "0", STR_PAD_LEFT) : '001';
        $array_send['Urlresponse'] = '';
        $array_send['Info'] = $arrayIn['Info']; // Este es el increment ID de la orden de Magento.
        $array_send['Tconn'] = 0;

        $options = array(
            CURLOPT_URL                             => $standard->getResponseUrl(),
            CURLOPT_HEADER                  => true,
            CURLOPT_POST                    => true,
            CURLOPT_POSTFIELDS              => $array_send,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_SSL_VERIFYHOST  => false,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_SSLCERT                 => '/var/www/cert/mitrenda.com.crt',
            CURLOPT_SSLKEY                  => '/var/www/cert/mitrenda.com.key',
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13 ( .NET CLR 3.5.30729)',
            CURLOPT_VERBOSE        => true,

        );
        /*		$options = array(
                                // CURLOPT_URL                          => 'https://compraswebcomercio.oca.com.uy/Inicio.aspx',
                                CURLOPT_URL                             => $this->getRequestUrl(),
                                //                  CURLOPT_URL            => 'https://compraswebcomercio.oca.com.uy/Inicio.aspx',
                                CURLOPT_HEADER                  => true,
                                CURLOPT_POST                    => true,
                                CURLOPT_POSTFIELDS              => $array_send,
                                CURLOPT_RETURNTRANSFER  => true,
                                CURLOPT_SSL_VERIFYHOST  => false,
                                CURLOPT_SSL_VERIFYPEER  => false,
                                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13 ( .NET CLR 3.5.30729)',
                                CURLOPT_VERBOSE        => true,
                                CURLOPT_SSLCERT  => '/etc/certs/notelapierdas.com.uy/cert.pem',
                                CURLOPT_SSLCERTPASSWD  => 'certificadooca',
                                //                CURLOPT_SSLCERTPASSWD  => 'OCA1234',
                                //                CURLOPT_SSLKEY         => '/var/www/html/notelapierdas.com.uy/key.pem'
                                //CURLOPT_SSLCERT                       => '/etc/certs/notelapierdas.com.uy/ntlp.crt'/*'/var/www/html/staging.ntlp/notelapierdas.com.uy.crt',
                                //CURLOPT_SSLKEY                        => '/etc/certs/notelapierdas.com.uy/ntlp.key'
                        );
        */
        /*$options = array(
                    CURLOPT_URL				=> $standard->getResponseUrl(),
                    CURLOPT_HEADER			=> true,
                    CURLOPT_POST			=> true,
                    CURLOPT_POSTFIELDS		=> $array_send,
                    CURLOPT_RETURNTRANSFER	=> true,
                    CURLOPT_SSL_VERIFYHOST	=> false,
                    CURLOPT_SSL_VERIFYPEER	=> false,
                    //		  CURLOPT_SSLCERT	 => '/var/www/html/notelapierdas.com.uy/ck.pem',
                    //		  CURLOPT_SSLCERTPASSWD  => 'OCA1234',
                    //		  CURLOPT_SSLKEY         => '/var/www/html/notelapierdas.com.uy/key.pem'
                    // CURLOPT_SSLCERT			=> '/var/www/html/notelapierdas.com.uy/gonza.crt',
                    // CURLOPT_SSLKEY			=> '/var/www/html/notelapierdas.com.uy/gonza.key'
                        //CURLOPT_SSLCERT                 => '/etc/ssl/2013/notelapierdas.com.uy.crt',
                        //CURLOPT_SSLKEY                  => '/etc/ssl/2013/notelapierdas.com.uy.key'
                        CURLOPT_SSLCERT                 => '/etc/certs/notelapierdas.com.uy/ntlp.crt',
                    CURLOPT_SSLKEY                  => '/etc/certs/notelapierdas.com.uy/ntlp.key'
                );*/

        Mage::log('[START] array to send',null,'oca_presentacion.log');
        Mage::log($array_send,null,'oca_presentacion.log');
        Mage::log('[END] array to send',null,'oca_presentacion.log');

        $arr = $standard->makeCurlConnection($options, $order, true);

        Mage::log('[START] array from OCA',null,'oca_presentacion.log');
        Mage::log($arr,null,'oca_presentacion.log');
        Mage::log('[END] array from OCA',null,'oca_presentacion.log');

        Mage::log('[START] array from OCA',null,'oca_presentacion.log');
        Mage::log($arrayIn,null,'oca_presentacion.log');
        Mage::log('[END] array from OCA',null,'oca_presentacion.log');

        $resultadoAutorizacion = $arrayIn['Rsp'];


        if ($resultadoAutorizacion == "0" && (isset($arr['Nrorsv']) && $arr['Nrorsv'] >0)) {
            $nroReserva = $arr['Nrorsv'];

            $invoice = $order->prepareInvoice();
            $invoice->register()->capture();

            Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();

            $order->addStatusHistoryComment('nro de Reserva:' . ' ' . $nroReserva);
            $order->save();
            return true;

        } else {

            if($resultadoAutorizacion === '1'){
                $message = 'Cliente Cancelo desde OCA, Idtrn ='. $arrayIn['Idtrn'];
            }else{
                $message = 'No Vino Nrorsv para la orden' . ' ' . $order->getIncrementId() . ' ' . 'Idtrn ='. $arrayIn['Idtrn'];
            }
            $order->addStatusHistoryComment($message);
            $order->save();
            return false;
            // exit("pago no aprobado");
        }

    }
}