<?php

class Magestore_Pdfinvoiceplus_Model_Entity_Pdfgenerator extends Magestore_Pdfinvoiceplus_Model_Entity_Abstractextend {

    const THE_START = '##productlist_start##';
    const THE_END = '##productlist_end##';
    public $pdfCollection;

    /**
     * The pdf proceesed template
     * @var string
     */
    protected $_pdfProcessedTemplate;

    /**
     * Load the pdf system
     * @return Mpdf Object - lib
     */
    protected function _construct()
    {
        $this->setPdfCollection();
        parent::_construct();
    }

    public function setTheSourceId($invoiceId)
    {
        $this->_sourceId = $invoiceId;
        return $this->_sourceId;
    }

    public function getTheSourceId()
    {
        return $this->_sourceId;
    }
     public function setPdfCollection()
    {
        $this->pdfCollection = Mage::getModel('pdfinvoiceplus/template')->getCollection();
        return $this;
    }

    public function getPdfCollection()
    {
        return $this->pdfCollection;
    }

    public function getTheStoreId()
    {
        if ($storeId = $this->getTheInvoice()->getOrder()->getStore()->getId())
        {
            return array(0, $storeId);
        }
        return array(0);
    }

    public function getFilteredCollection()
    {
        try {
            $pdfGeneratorTemplate = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
//            if ($this->templateId)
//            {
//                $pdfGeneratorTemplate = $this->getPdfCollection()
//                        ->addFieldToSelect('*')
//                        ->addFieldToFilter('template_id',$this->templateId);
//            }
//            else
//            {
//                $pdfGeneratorTemplate = $this->getPdfCollection()
//                        ->addFieldToSelect('*')
//                        ->addFieldToFilter('status', 1);
////                        ->addFieldToFilter('template_store_id', $this->getTheStoreId());
//            }
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            return null;
        }
        return $pdfGeneratorTemplate;
    }

    public function createTemplate()
    {
        $pdfCollection = $this->getFilteredCollection();
        if ($pdfCollection)
        {
            return $pdfCollection;
        }
//        foreach ($pdfCollection as $pdf)
//        {
//            $dataTemplate = $pdf;
//        }
//
//        if ($dataTemplate)
//        {
//            return $dataTemplate;
//        }
        return false;
    }

    public function getFileName() {
//        $template = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
//        if(Mage::app()->getRequest()->getParam('invoice_id')){
//            $filename = $template->getInvoiceFilename();
//            //zend_Debug::dump($filename);die();
//        }elseif(Mage::app()->getRequest()->getParam('creditmemo_id')){
//            $filename = $template->getCreditmemoFilename();
//        }else{
//            $filename = $template->getOrderFilename();
//            die('111');
//        }
//        zend_debug::dump($filename);
//        die('dtt');
//        $vars = Mage::helper('pdfinvoiceplus')->processAllVars(Mage::helper('pdfinvoiceplus/pdf')->collectVars());
////        zend_Debug::dump($vars);die('213');
//        $filter->setVariables($vars);
//        $filename = $filter->filter($filename);
       
//        return '$filename';
        if ($fileName = $this->createTemplate()->getData('invoice_filename'))
        {
            $templateVars = $this->getVars();
            $headerTemplate = Mage::helper('pdfinvoiceplus')->setTheTemplateLayout($fileName);
            $processedTemplate = $headerTemplate->getProcessedTemplate($templateVars);

            $cleanString = Mage::helper('core/string')->cleanString($processedTemplate);
            $cleanString = str_replace(array(' ', '.', ':'), '-', $processedTemplate);
            return $cleanString;
        }
        return 'invoice - ';
    }
    
    public function getBarcodeValueInvoice() {
        if ($barcode = $this->createTemplate()->getData('barcode_order'))
        {
            $templateVars = $this->getVars();
            $headerTemplate = Mage::helper('pdfinvoiceplus')->setTheTemplateLayout($barcode);
            $processedTemplate = $headerTemplate->getProcessedTemplate($templateVars);

            $cleanString = Mage::helper('core/string')->cleanString($processedTemplate);
            $cleanString = str_replace(array(' ', '.', ':'), '-', $processedTemplate);
            return $cleanString;
        }
        return 'barcode - ';
    }
     public function getBody()
    {

        if ($body = $this->createTemplate()->getData('invoice_html'))
        {
            return $body;
            
        }
        return false;
    }

    /**
     * Get the template body from used in the backend with the varables and add the item variables.
     * @return string
     */
    public function getTheTemplateBodyWithItems()
    {
        $templateToProcessForItems = $this->getBody();
        $items = Mage::getModel('pdfinvoiceplus/entity_items')
                        ->setSource($this->getTheInvoice())->setOrder($this->getTheInvoice()->getOrder());
        $itemsData = $items->processAllVars();
        
        $result = Mage::helper('pdfinvoiceplus/items')
                ->getTheItemsFromBetwin($templateToProcessForItems,self::THE_START, self::THE_END);
        $i = 1;
        foreach ($itemsData as $templateVars)
        {
            $itemPosition = array('items_position' => $i++);
            $templateVars = array_merge($itemPosition, $templateVars);

            $pdfProcessTemplate = Mage::getModel('core/email_template');
            $itemProcess = $pdfProcessTemplate->setTemplateText($result)->getProcessedTemplate($templateVars);
            if($i%2==0){
                $itemProcess = str_replace('<tr>','<tr class="items-tr">',$itemProcess);
            }
            $finalItems .= $itemProcess . '<br>';
        }
        $templateWithItemsProcessed = str_replace($result, $finalItems, $templateToProcessForItems);


        $tempmplateForHtmlProcess = '<html>' . $templateWithItemsProcessed . '</html>';

        //$htmlTemplateWithItems = Mage::helper('pdfinvoiceplus/items')->processHtml($tempmplateForHtmlProcess);
        return $tempmplateForHtmlProcess;
    }

    /**
     * Load the default information for the template processing
     * @return object Mail template object
     */
    public function mainVariableProcess()
    {
        $templateText = $this->getTheTemplateBodyWithItems();
        //auto insert totals
        $total_invoice = Mage::getModel('pdfinvoiceplus/entity_totalsRender_invoice');
        $total_invoice->setSource($this->getTheInvoice())
            ->setHtml($templateText)->setTemplateId($this->createTemplate()->getId());
        $templateText = $total_invoice->renderHtml();
        
        //Zend_Debug::dump($templateText);die;
        
        $theVariableProcessor = Mage::helper('pdfinvoiceplus')->setTheTemplateLayout($templateText);
        return $theVariableProcessor;
    }

    /**
     * The vars for the entity
     * @return type
     */
    public function getTheProcessedTemplate()
    {
        $templateVars = $this->getVars();
        $processedTemplate = $this->mainVariableProcess()->getProcessedTemplate($templateVars);
        return $processedTemplate;
    }

    /**
     * get system template in use
     * @return system template
     */
    public function getSystemTemplate() {
        $activeTemplate = Mage::getModel('pdfinvoiceplus/template')->getCollection()
            ->addFieldToFilter('is_active', 1)
            ->getFirstItem();
        $systemTemplate = Mage::getModel('pdfinvoiceplus/systemtemplate')
            ->load($activeTemplate->getSystemTemplateId());
        return $systemTemplate;
    }

    public function getOrientation() {
        $template = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
        $orientation = $template->getOrientation();
        if ($orientation == 1)
            return 'L';
        return 'P';
    }

    public function getPdf($html) {
        $mailPdf = new Varien_Object;
        $pdf = $this->loadPdf();
        $templateBody = $this->getTheProcessedTemplate();
        $pdf->WriteHTML($this->getCss(), 1);
        $pdf->WriteHTML($templateBody);

        $mailPdf->setData('htmltemplate', $templateBody);
        $output = $pdf->Output($this->getFileName(), 'S');
        $mailPdf->setData('pdfbody', $output);
        $mailPdf->setData('filename', $this->getFileName());
        return $mailPdf;
    }

    public function pdfPaperFormat() {
        $template = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
        $format = $template->getFormat();
        $format = $format ? $format : 'A4';
        return $format;
    }

    public function loadPdf() {
        $top = '5';
        $bottom = '5';
        $left = '0';
        $right = '0';
        $orientation = $this->getOrientation();
        $pdf = new Mpdf_Magestorepdf('', $this->pdfPaperFormat(), 8, '', $left, $right, $top, $bottom);
        $pdf->AddPage($orientation);
        return $pdf;
    }
}
