<?php
/**
 * Created by PhpStorm.
 * User: TOSHIBA
 * Date: 5/31/14
 * Time: 7:44 AM
 */
require_once 'Mage/Catalog/controllers/CategoryController.php';


class Onetree_Catalog_CategoryController extends Mage_Catalog_CategoryController {
    public function lookbookAjaxAction(){ 
        $this->getResponse()->setHeader('Content-type', 'application/html');
        if ($category = $this->_initCatagory()) {
            $block = $this->getLayout()->createBlock('catalog/product_list','lookbook-ajax');
            $block->setTemplate('catalog/product/lookbook_list_ajax.phtml');
            $this->getResponse()->setBody($block->toHtml());
        }  
    }
}