<?php
/**
 * Created by PhpStorm.
 * User: Juan Carlos Conde
 * Date: 6/17/14
 * Time: 7:34 PM
 */ 
class Onetree_Sales_Block_Adminhtml_Sales_Order_Gridcsv1 extends Mage_Adminhtml_Block_Sales_Order_Grid
{
    protected function _prepareCollection()
    {
        /** @var $collection Mage_Sales_Model_Resource_Order_Grid_Collection */
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->addFieldToSelect('increment_id');
        $collection->addFieldToSelect('billing_name');
        $collection->addFieldToSelect('status');
        
        $filter_id = explode(",",$this->getRequest()->getParam('internal_order_ids',false));
        if($filter_id && $filter_id[0]!=""){
            $collection->addAttributeToFilter('entity_id',array('in' => $filter_id));
        }
        
        $collection->getSelect()->resetJoinLeft();
        $collection->getSelect()->joinLeft('sales_flat_order_payment', 'main_table.entity_id = sales_flat_order_payment.parent_id','method');
        $collection->getSelect()->joinLeft('sales_flat_order_address', 'main_table.entity_id = sales_flat_order_address.parent_id', array('city','street','address_type'));
        $collection->getSelect()->joinLeft(array('sales_flat_order_item'), 'main_table.entity_id = sales_flat_order_item.order_id', array('sku','qty_ordered','base_row_total_incl_tax'));
        $collection->getSelect()->group(array('sales_flat_order_item.order_id','sales_flat_order_item.sku'));
        $collection->getSelect()->where('sales_flat_order_address.address_type = ?', 'shipping');
        $collection->setOrder('sales_flat_order_item.order_id', 'DESC');

//        Mage::log($collection->getSelect()->__toString());

        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Nombre'),
            'index' => 'billing_name',
        ));

        $this->addColumn('base_row_total_incl_tax', array(
            'header' => Mage::helper('sales')->__('Precio'),
            'index' => 'base_row_total_incl_tax',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
            'renderer' => 'Onetree_Sales_Model_Renderer_Preciocolumngrid'
        ));

        $this->addColumn('street', array(
            'header' => Mage::helper('sales')->__('Direccion'),
            'index' => 'street',
            'type'  => 'text',
            'width' => '70px',
            'renderer' => 'Onetree_Sales_Model_Renderer_Direccioncolumngrid'
        ));

        $this->addColumn('city', array(
            'header' => Mage::helper('sales')->__('Localidad'),
            'index' => 'city',
            'type'  => 'text',
            'width' => '70px'
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('sales')->__('Codigo'),
            'index' => 'sku',
            'type'  => 'text',
            'width' => '70px'
        ));

        $this->addColumn('qty_ordered', array(
            'header' => Mage::helper('sales')->__('Cantidad'),
            'index' => 'qty_ordered',
            'type'  => 'text',
            'width' => '70px'
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'*/sales_order/view'),
                            'field'   => 'order_id'
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
                ));
        }

//        $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));
//        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
//        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }
}