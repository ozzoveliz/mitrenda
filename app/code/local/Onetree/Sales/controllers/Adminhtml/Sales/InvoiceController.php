<?php
/**
 * Created by PhpStorm.
 * User: Juan Carlos Conde
 * Date: 11/28/14
 * Time: 11:14 AM
 */
require_once 'Mage/Adminhtml/controllers/Sales/InvoiceController.php';


class Onetree_Sales_Adminhtml_Sales_InvoiceController extends Mage_Adminhtml_Sales_InvoiceController
{
    /**
     * Export order grid to CSV format
     */
    public function exportCsv2Action()
    {
        $fileName   = 'invoicesCustomCsv2.txt';
        $grid       = $this->getLayout()->createBlock('onetree_sales/adminhtml_sales_invoice_gridcsv2');
        $str = str_replace("\n","\r\n", $grid->getCsv());
        $this->_prepareDownloadResponse($fileName, $str);
    }
}