<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Onetree_Sales_Block_Adminhtml_Sales_Creditmemo_Gridcsv2 extends Mage_Adminhtml_Block_Sales_Creditmemo_Grid
{
    public function __construct()
    {
        parent::__construct();
//        $this->setId('sales_invoice_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->addFilterToMap('method','sales_flat_order_payment.method');
        $collection->addFilterToMap('billofsale_number','sales_flat_order.billofsale_number');
        $filter_id = explode(",",$this->getRequest()->getParam('internal_creditmemo_ids',false));
        if($filter_id && $filter_id[0]!="") {
            $collection->addAttributeToFilter('main_table.entity_id',array('in' => $filter_id));
        }

        $collection->getSelect()->resetJoinLeft();
        $collection->getSelect()->joinLeft('sales_flat_order_payment', 'main_table.order_id = sales_flat_order_payment.parent_id','method');
        $collection->getSelect()->joinLeft('sales_flat_order', 'main_table.order_id = sales_flat_order.entity_id','billofsale_number');
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('sales')->__('Dia'),
            'index'     => 'created_at',
            'format'    => 'd',
            'type'      => 'date',
        ));

        $this->addColumn('payment_method', array(
            'header' => Mage::helper('sales')->__('Debe'),
            'index'     => 'method',
            'type'      => 'text',
            'renderer' => 'Onetree_Sales_Model_Renderer_Paymentcolumngrid'
        ));

        $this->addColumn('haber', array(
            'header'    => Mage::helper('sales')->__('Haber'),
            'index'     => 'haber',
            'type'      => 'text',
            'renderer' => new Onetree_Sales_Model_Renderer_Value(array('510101'))
        ));

        $this->addColumn('centro', array(
            'header'    => Mage::helper('sales')->__('Centro'),
            'index'     => 'centro',
            'type'      => 'text',
            'renderer' => new Onetree_Sales_Model_Renderer_Value()
        ));
        $this->addColumn('numero', array(
            'header'    => Mage::helper('sales')->__('Numero'),
            'index'     => 'numero',
            'type'      => 'text',
            'renderer' => new Onetree_Sales_Model_Renderer_Value()
        ));
        $this->addColumn('codigo', array(
            'header'    => Mage::helper('sales')->__('Codigo'),
            'index'     => 'codigo',
            'type'      => 'text',
            'renderer' => new Onetree_Sales_Model_Renderer_Value()
        ));

        $this->addColumn('serie', array(
            'header'    => Mage::helper('sales')->__('Serie'),
            'index'     => 'serie',
            'type'      => 'text',
            'renderer' => new Onetree_Sales_Model_Renderer_Serie()
        ));
        $this->addColumn('numero_comp', array(
            'header'    => Mage::helper('sales')->__('Numero Comp'),
            'index'     => 'numero_comp',
            'type'      => 'text',
            'renderer' => new Onetree_Sales_Model_Renderer_Value()
        ));
        $this->addColumn('letra', array(
            'header'    => Mage::helper('sales')->__('Letra'),
            'index'     => 'letra',
            'type'      => 'text',
            'renderer' => new Onetree_Sales_Model_Renderer_Value()
        ));

        $this->addColumn('concepto', array(
            'header'    => Mage::helper('sales')->__('Concepto'),
            'index'     => 'concepto',
            'type'      => 'text',
            'width'     => '20%',
            'renderer' => new Onetree_Sales_Model_Renderer_Conceptomemo()
        ));
        $this->addColumn('rut', array(
            'header'    => Mage::helper('sales')->__('RUT'),
            'index'     => 'rut',
            'type'      => 'text',
            'width'     => '5%',
            'renderer' => new Onetree_Sales_Model_Renderer_Value()
        ));
        $this->addColumn('moneda', array(
            'header'    => Mage::helper('sales')->__('Moneda'),
            'index'     => 'moneda',
            'type'      => 'text',
            'width'     => '5%',
            'renderer' => new Onetree_Sales_Model_Renderer_Value(array('0'))
        ));
        $this->addColumn('grand_total', array(
            'header'    => Mage::helper('customer')->__('Total'),
            'index'     => 'grand_total',
            'type'      => 'currency',
            'align'     => 'right',
//            'currency'  => 'order_currency_code',
            'renderer' => new Onetree_Sales_Model_Renderer_Currencymemo()
        ));

        $this->addColumn('codigo_iva', array(
            'header'    => Mage::helper('sales')->__('Codigo IVA'),
            'index'     => 'codigo_iva',
            'type'      => 'text',
            'renderer' => new Onetree_Sales_Model_Renderer_Value(array('2'))
        ));
        $this->addColumn('iva', array(
            'header'    => Mage::helper('sales')->__('IVA'),
            'index'     => 'iva',
            'type'      => 'currency',
            'align'     => 'right',
            'renderer' => new Onetree_Sales_Model_Renderer_Ivamemo()
        ));
        $this->addColumn('codigo_imp', array(
            'header'    => Mage::helper('sales')->__('Codigo IMP'),
            'index'     => 'codigo_imp',
            'type'      => 'text',
            'renderer' => new Onetree_Sales_Model_Renderer_Value()
        ));
        $this->addColumn('impuesto', array(
            'header'    => Mage::helper('sales')->__('Impuesto'),
            'index'     => 'impuesto',
            'type'      => 'text',
            'renderer' => new Onetree_Sales_Model_Renderer_Value()
        ));
        $this->addColumn('cotizacion', array(
            'header'    => Mage::helper('sales')->__('Cotizacion'),
            'index'     => 'cotizacion',
            'type'      => 'text',
            'renderer' => new Onetree_Sales_Model_Renderer_Value()
        ));
        $this->addColumn('libro', array(
            'header'    => Mage::helper('sales')->__('Libro'),
            'index'     => 'libro',
            'type'      => 'text',
            'renderer' => new Onetree_Sales_Model_Renderer_Value(array('V'))
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('Custom CSV2'));

        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }
}
