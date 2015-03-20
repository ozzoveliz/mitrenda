<?php
/**
 * Created by PhpStorm.
 * User: Juan Carlos Conde
 * Date: 8/18/14
 * Time: 2:50 PM
 */
require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php';


class Onetree_Sales_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController
{
    /**
     * Export order grid to CSV format
     */
    public function exportCsv1Action()
    {
        $fileName   = 'orders1.csv';
        $grid       = $this->getLayout()->createBlock('onetree_sales/adminhtml_sales_order_gridcsv1');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    public function exportCsv2Action()
    {
        /*Header
        ;DIA ;DEBE;HABER;-CENTRO...;-NUMERO;-CODIGO...;SERIE;-NUMERO COMPR;-LETRA;CONCEPTO;-RUT;MNEDA;TOTAL;CODIGOIVA;-IVA;-CODIMP;-IMP;-COTIZ;LIBRO;-14 COSAS MAS
        */

//        $fileName   = 'orders2.csv';
        $csv_data = array();
        $var_csv = new Varien_File_Csv();

        $path = Mage::getBaseDir('var') . DS . 'export' . DS;
        $name = md5(microtime());
        $fileName = $path . DS . 'order2_' . $name . '.csv';

     
        $collection = Mage::getResourceModel('sales/order_grid_collection');
        $collection->addFieldToSelect('increment_id');
        $collection->addFieldToSelect('billing_name');
        $collection->addFieldToSelect('status');
        $collection->addFieldToSelect('grand_total');
        $collection->addFieldToSelect('created_at');
        
        $filter_id = explode(",",$this->getRequest()->getParam('internal_order_ids',false));
        if($filter_id && $filter_id[0]!=""){
            $collection->addAttributeToFilter('entity_id',array('in' => $filter_id));
        }
        
 
        $collection->getSelect()->resetJoinLeft();
        $collection->getSelect()->joinLeft('sales_flat_order_payment', 'main_table.entity_id = sales_flat_order_payment.parent_id','method');
        $collection->getSelect()->joinLeft('sales_flat_order_address', 'main_table.entity_id = sales_flat_order_address.parent_id', array('city','street','address_type'));
        $collection->getSelect()->joinLeft(array('sales_flat_order_item'), 'main_table.entity_id = sales_flat_order_item.order_id', array('sku','name','qty_ordered','base_row_total_incl_tax'));
        $collection->getSelect()->group(array('sales_flat_order_item.order_id','sales_flat_order_item.sku'));
        $collection->getSelect()->where('sales_flat_order_address.address_type = ?', 'billing');
        $collection->setOrder('sales_flat_order_item.order_id', 'DESC');
        
        //Mage::log($collection->getSelect()->__toString());
        $order_id = -999;
        //header
        $row_data['dia'] = 'DIA';
        $row_data['debe'] = 'DEBE';
        $row_data['haber'] = 'HABER';
        $row_data['centro'] = 'CENTRO';
        $row_data['numero'] = 'NUMERO';
        $row_data['codigo'] = 'CODIGO';
        $row_data['serie'] = 'SERIE';
        $row_data['numerocomp'] = 'NUMERO COMP';
        $row_data['letra'] = '';
        $row_data['concepto'] = 'CONCEPTO';
        $row_data['rut'] = 'RUT';
        $row_data['mneda'] = 'MNEDIA';
        $row_data['total'] = 'TOTAL';
        $row_data['codigoiva'] ='CODIGO IVA';
        $row_data['iva'] = 'IVA';
        $row_data['codimp'] = 'COD IMP';
        $row_data['imp'] ='IMP';
        $row_data['cotiz'] = 'COTIZ';
        $row_data['libro'] = 'LIBRO';

        $csv_data[] = $row_data;
        $serie = 2;
        foreach ($collection as $row_colection){
            $row_data = array();
            $row_order_id = (int)$row_colection->getData('increment_id');
            //29,1120001,,,,,1,,,F100000986 TIEMPOST,,0,1970.00,2,,,,,V
            $debe = '1120001';
            $haber = '510101';
            $date = date_create($row_colection->getData('created_at'));
            $date = date_format($date, 'd/m/y');
            if($order_id != $row_order_id){
                if( $serie == 1){
                    $serie=2;
                }else {
                    $serie=1;
                }
                $row_data['dia'] = $date;
                $row_data['debe'] = $debe ;
                $row_data['haber'] = '';
                $row_data['centro'] = '';
                $row_data['numero'] = '';
                $row_data['codigo'] = '';
                $row_data['serie'] = $serie;
                $row_data['numerocomp'] = '';
                $row_data['letra'] = '';
                $row_data['concepto'] ='F'. $row_colection->getData('increment_id');
                $row_data['rut'] = '';
                $row_data['mneda'] = '0';
                $row_data['total'] = $row_colection->getData('grand_total');
                $row_data['codigoiva'] ='2';
                $row_data['iva'] = '';
                $row_data['codimp'] = '';
                $row_data['imp'] ='';
                $row_data['cotiz'] = '';
                $row_data['libro'] = 'L';

               $csv_data[] = $row_data;
               $order_id = $row_order_id;
            }

            $row_data['dia'] =  $date;
            $row_data['debe'] = '';
            $row_data['haber'] = $haber;
            $row_data['centro'] = '';
            $row_data['numero'] = '';
            $row_data['codigo'] = '';
            $row_data['serie'] = $serie;
            $row_data['numerocomp'] = '';
            $row_data['letra'] = '';
            $row_data['concepto'] = $row_colection->getData('increment_id').' Sku: '.$row_colection->getData('sku').' '.$row_colection->getData('name');
            $row_data['rut'] = '';
            $row_data['mneda'] = '0';
            $row_data['total'] = $row_colection->getData('base_row_total_incl_tax');
            $row_data['codigoiva'] ='0';
            $row_data['iva'] = '';
            $row_data['codimp'] = '';
            $row_data['imp'] ='';
            $row_data['cotiz'] = '';
            $row_data['libro'] = 'V';

            $csv_data[] = $row_data;
        }
     
        $var_csv->saveData($fileName, $csv_data);
        $this->_prepareDownloadResponse('orders2.csv', array('type' => 'filename', 'value' => $fileName));
    }
}