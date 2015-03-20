<?php
    class Onetree_Sales_Model_Renderer_Trackingcolumngrid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
    {
        public function render(Varien_Object $row)
        {
            $order = $row->getId();
            $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')->setOrderFilter($order)->load();
            $tracknums = array();
            foreach ($shipmentCollection as $shipment){     
                foreach($shipment->getAllTracks() as $tracknum) {
                    $tracknums[]=$tracknum->getNumber();   
                }
            }
            return implode(",",$tracknums) ;
        }
    }
