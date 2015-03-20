<?php
/**
 * Created by PhpStorm.
 * User: Juan Carlos Conde
 * Date: 6/17/14
 * Time: 7:34 PM
 */ 
class Onetree_Sales_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getStatusTrack($tracknums) {
        if (count($tracknums) > 0) {
            try {
                $client = new SoapClient("http://200.71.12.229/axis2/services/WSTiempost?wsdl", array("trace" => 1, "exception" => 0, "connection_timeout" => 0));
                $params['cliente'] = '8622';
                $params['pwd'] = '*8622$$$';
                $params['claveExterna'] = $tracknums[0];
                $params['fecha'] = '';

                $result = $client->WSConsultarRemitoClaveExterna($params);
                if (isset($result->return->errorDescripcion) && !empty($result->return->errorDescripcion)) {
                    return $result->return->errorDescripcion;
                } else {
                    return $result->return->estadoDescripcion;
                }
            } catch (SoapFault $exc) {
                Mage::logException($exc->getMessage());
                return $this->__('No existe el remito');
            } catch (Exception $e) {
                Mage::logException($e->getMessage());
                return $this->__('No existe el remito');
            }
        } else {
            return $this->__('No existe el remito');
        }
    }
}