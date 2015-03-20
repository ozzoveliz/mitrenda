<?php
/**
 * Created by PhpStorm.
 * User: Juan Carlos Conde
 * Date: 6/17/14
 * Time: 7:34 PM
 */ 
class Onetree_Sales_Block_Adminhtml_Sales_Order_Grid extends Onetree_BillOfSaleNumber_Block_Adminhtml_Sales_Order_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->addFilterToMap('product_name','sales_flat_order_item.name');
        $collection->addFilterToMap('method','sales_flat_order_payment.method');
        $collection->getSelect()->joinLeft('sales_flat_order_payment', 'main_table.entity_id = sales_flat_order_payment.parent_id','method');
        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumnAfter('payment_method', array(
            'header' => Mage::helper('sales')->__('Payment Method'),
            'index'     => 'method',
            'type'      => 'options',
            'options'       => array('abitabmail'=>'Abitab Mail','ocauru'=>'OCA','visanet' => 'Visa','firstdata'=>'MasterCard','checkmo'=>'Check on Money'),
        ),'grand_total');

        //Add by dav.q Traking de tiempos
        $this->addColumnAfter('tracking_tiempost', array(
            'header' => Mage::helper('sales')->__('Tracking Tiempost'),
            'index' => 'order_id',
            'renderer' => 'Onetree_Sales_Model_Renderer_Trackingcolumngrid'
        ),'payment_method');

        $this->addExportType('*/*/exportCsv1', Mage::helper('sales')->__('Custom CSV 1'));
        $this->addExportType('*/*/exportCsv2', Mage::helper('sales')->__('Custom CSV 2'));

        //return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
        return parent::_prepareColumns();
    }
}