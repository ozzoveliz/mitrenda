<?php
/**
 * Created by PhpStorm.
 * User: Juan Carlos Conde
 * Date: 11/28/14
 * Time: 11:22 AM
 */ 
class Onetree_Sales_Block_Adminhtml_Sales_Invoice_Grid extends Mage_Adminhtml_Block_Sales_Invoice_Grid
{
    protected function _prepareColumns()
    {
        $this->addExportType('*/*/exportCsv2', Mage::helper('sales')->__('Custom CSV2'));

        return parent::_prepareColumns();
    }
}