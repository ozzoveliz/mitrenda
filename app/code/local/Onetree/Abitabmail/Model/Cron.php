<?php
/**
 * Created by PhpStorm.
 * User: Juan Carlos Conde
 * Date: 7/22/14
 * Time: 2:41 AM
 */

class Onetree_Abitabmail_Model_Cron
{
    /**
     * Current Magento store
     *
     * @var Mage_Core_Model_Store
     * @access protected
     */
    protected $_store;

    /**
     * Get HELPER instance
     *
     * @param string $which
     * @return object
     */
    protected function _helper($which = 'data')
    {
        return Mage::helper('onetree_abitab/'.$which);
    }
    protected function _orderConfig()
    {
        return Mage::getSingleton('sales/order_config');
    }

    public function exportOrders()
    {
        $pending = $this->_orderConfig()->getStateDefaultStatus(Mage_Sales_Model_Order::STATE_NEW);
        $this->_processExportOrders($pending);
        return $this;
    }
    public function reExportOrders()
    {
        $pendingPayment = $this->_orderConfig()->getStateDefaultStatus(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
        $this->_processExportOrders($pendingPayment);
        return $this;

    }
    /**
     * Process EXPORT tasks
     *
     */

    protected function _processExportOrders($status = 'pending')
    {
        $store = Mage::app()->getStore()->getId();
        if (!Mage::getStoreConfig('payment/abitabmail/active',$store)) {
            return $this;
        }

        $collection = $this->_getEntityModel($status);

        /*[START] if no order to export then continue*/
        if($collection->count() == 0) return $this;
        /*[END]*/

        // modelo que genera el numero de lote para abitab
        $model = Mage::getModel('onetree_abitabmail/mail');
        $model->setContent('');
        $model->setCreatedAt(Mage::getModel('core/date')->gmtDate());
        $model->save();
        // lote para el nombre del mail de abitab
        $lote = $model->getId();

        //datos de la empresa enviados por abitab
        $identifier = Mage::getStoreConfig('payment/abitabmail/identifier');
        $companyNumber = Mage::getStoreConfig('payment/abitabmail/company_number');
        $subCompanyNumber = Mage::getStoreConfig('payment/abitabmail/sub_company_number');
        $currencyType = Mage::getStoreConfig('payment/abitabmail/currency_type');

        //Always when use date for something internal implementation, use Mage::app()->Locate()
        //$date = new Zend_Date();
        $date = Mage::app()->getLocale()->date();

        // format dd/mm/yyyy
        $today = $date->get(Zend_Date::DAY)."/".$date->get(Zend_Date::MONTH)."/".$date->get(Zend_Date::YEAR);
        // format ddmmyy
        $todayShort = $date->get(Zend_Date::DAY).$date->get(Zend_Date::MONTH).$date->get(Zend_Date::YEAR_SHORT);
        // agregar 2 dias para la fecha de expiracion de la orden
        $date->addDay(Mage::getStoreConfig('payment/abitabmail/limit_day'));
        $expireDate = $date->get(Zend_Date::DAY)."/".$date->get(Zend_Date::MONTH)."/".$date->get(Zend_Date::YEAR);

        // CABEZAL DEL MAIL abitab
        $cabezal = "13|16|{$currencyType}"."\r\n";

        // DETALLE DEL MAIL abitab
        $totales = 0;
        $detalles = "";
        $i = 0;
        $ordersTosend = array();
        $filename = $identifier .'_'. $todayShort .'_'. $lote .'.ONL';
        foreach($collection as $order) {

            $dateCreatedAt = $order->getCreatedAt();
            $createdAt = Mage::app()->getLocale()->date($dateCreatedAt);
            $createdAt->addDay(Mage::getStoreConfig('payment/abitabmail/limit_day'));

            //$now = new Zend_Date();
            $now = Mage::app()->getLocale()->date();

            /*Comparing now with expiration date, if now is lasted as expirationDate order must to be canceled*/
            if($now->compareDate($createdAt) === 1){

                //$order->cancel()->save();
                //$order->addStatusHistoryComment('Order was expired to be enabled to pay in ABITAB');
                continue;
            }
            /*if ($createdAt->get() < $now->get()) {
                $order->cancel()->save();
                continue;
            }*/

            $payment = $order->getPayment();
            $paymentMethod = $payment->getMethodInstance();
            $info = $paymentMethod->getInfoInstance();

            // formato del precio con una presicion de 2 decimales
            $importe = Mage::getModel('directory/currency')->format(
                $order->getData('grand_total'),
                array('display'=>Zend_Currency::NO_SYMBOL,'precision'=>2),
                false
            );
            // quitando el separador decimal para abitab
            $importe = str_replace(array(',','.'),'',$importe);

            $customerName = strtoupper($order->getCustomerFirstname());
            $customerLastName = strtoupper($order->getCustomerLastname());

            $incrementId = "order: " . $order->getIncrementId();
            $detalle = array(
                0 => "C",
                1 => strtoupper($identifier),
                2 => $companyNumber,
                3 => $subCompanyNumber,
                4 => $lote,
                5 => $info->getCardIdentity(),
                6 => strtoupper($customerName),
                7 => strtoupper($customerLastName),
                8 => $info->getCardIdentity(),
                9 => "1",
                10 => $expireDate,
                11 => " ",
                12 => $today,
                13 => $importe,
                14 => " ", 15 => " ",
                16 => $currencyType,
                17 => " ", 18 => " ", 19 => " ", 20 => " ", 21 => " ", 22 => " ", 23 => " ", 24 => " ",
                25 => " ", 26 => " ", 27 => " ", 28 => " ", 29 => " ", 30 => " ", 31 => " ", 32 => $incrementId, 33 => " ",
                34 => "1"
            );


            //[START change the line break to window format]
            /*$detalles .= implode('|',$detalle)."\n";*/
            $detalles .= implode('|',$detalle)."\r\n";
            //[END]

            $i += 1;
            $totales += $order->getData('grand_total');

            /*Add orders to aux array if the emails is sent succesfully then the order will be status change*/
            $ordersTosend[] = $order;


        }

        // FOOTER DEL MAIL abitab
        /*format totals*/
        $totales = Mage::getModel('directory/currency')->format(
            $totales,
            array('display'=>Zend_Currency::NO_SYMBOL,'precision'=>2),
            false
        );

        $importTotal = str_replace(array(',','.'),'',$totales);

        $totalesText = "#|{$currencyType}|{$i}|{$importTotal}";
        // fin datos para abitab

        // armando adjunto para el mail
        $attachment = $cabezal.$detalles.$totalesText."\r\n";


        try {
            // si no existen detalles para enviar
            if (empty($detalles)) {
                $model->setContent('no existen ordenes para ser enviados')->save();
                return $this;
            }

            $config = array(
                'ssl' => Mage::getStoreConfig('payment/abitabmail/smtp_ssl'),
                'port' => Mage::getStoreConfig('payment/abitabmail/smtp_port'),
                'auth' => Mage::getStoreConfig('payment/abitabmail/smtp_auth'),
                'username' => Mage::getStoreConfig('payment/abitabmail/smtp_username'),
                'password' => Mage::getStoreConfig('payment/abitabmail/smtp_password')
            );

            $transport = new Zend_Mail_Transport_Smtp(Mage::getStoreConfig('payment/abitabmail/smtp'), $config);
            //Use this if you do not want to specify $transport evertime you use Zend_Mail->send()
            //Zend_Mail::setDefaultTransport($transport);

            // preparando el correo para ser enviado
            $mail = new Zend_Mail('ansi');
            $recipients = Mage::getStoreConfig('payment/abitabmail/abitab_mails');
            $recipients_cc = explode(',',Mage::getStoreConfig('payment/abitabmail/abitab_mails_cc'));

            $mail->setBodyHtml('CODEMPRESA=MTD')
                ->setSubject('Soporte Mitrenda')
                ->addTo($recipients)
                ->addCc($recipients_cc)
                ->setFrom(Mage::getStoreConfig('trans_email/ident_sales/email'), Mage::getStoreConfig('trans_email/ident_sales/name'));

            /*$mail->createAttachment(
                $attachment,
                Zend_Mime::TYPE_OCTETSTREAM,
                Zend_Mime::DISPOSITION_ATTACHMENT,
                Zend_Mime::ENCODING_BASE64,
                $filename
            );*/

            $mp = new Zend_Mime_Part($attachment);
            $mimeType    = Zend_Mime::TYPE_TEXT;
            $disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
            $encoding    = Zend_Mime::ENCODING_BASE64;
            $charset       = 'ansi';

            $mp->encoding = $encoding;
            $mp->type = $mimeType;
            $mp->disposition = $disposition;
            $mp->filename = $filename;
            $mp->charset = $charset;

            $mail->addAttachment($mp);

            $mail->send($transport);

            $model->setContent($attachment)->save();

            foreach($ordersTosend as $order){
                /*[START] change the state-status of the order to processing*/
                //$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING,true,'Orden enviada en el lote: '.$filename);
                $order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, 'pending_payment','Orden enviada en el lote: '.$filename);
                $order->save();
                /*[END]*/
            }

        } catch (Exception $e) {
            Mage::logException($e);
            $model->setContent($e->getCode() . ": " . $e->getMessage());
        }

        return $this;
    }

    protected function _getEntityModel($status = 'pending')
    {
        $model = null;

        $model = Mage::getModel('sales/order')->getCollection();
        $model->getSelect()->join(
            array("p" => $model->getResource()->getTable("sales/order_payment")),
            "p.parent_id = main_table.entity_id",
            array()
        );
        $model->addFieldToFilter("method", Onetree_Abitabmail_Model_Method_Mail::ABITABMAIL);
        $model->addFieldToFilter('status', array('eq' => array($status)));

        if($status == 'pending_payment'){
            $date   = Mage::app()->getLocale()->date();
            $date   = $date->subDay(1);
            /* matter only to date as orders  exceed limit date will be canceled later*/
            $to     = date('Y-m-d 23:59:59',$date->getTimestamp());
            
            $model->addAttributeToFilter('created_at',array('to'=>$to));
        }


        return $model;
    }
}