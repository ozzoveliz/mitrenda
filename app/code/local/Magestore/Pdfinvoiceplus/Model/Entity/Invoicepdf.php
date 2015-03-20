<?php

class Magestore_Pdfinvoiceplus_Model_Entity_Invoicepdf extends Magestore_Pdfinvoiceplus_Model_Entity_Pdfgenerator {

    public $invoiceId;
    public $templateId;

    public function getTheInvoice() {
        $invoice = Mage::getModel('sales/order_invoice')->load($this->invoiceId);
        return $invoice;
    }

    public function getThePdf($invoiceId, $templateId) {
        $this->templateId = $templateId;
        $this->invoiceId = $invoiceId;
        $this->setVars(Mage::helper('pdfinvoiceplus')->processAllVars($this->collectVars()));
        return $this->getPdf();
    }

    public function collectVars() {
        $vars = Mage::getModel('pdfinvoiceplus/entity_additional_info')
            ->setSource($this->getTheInvoice())
            ->setOrder($this->getTheInvoice()->getOrder())
            ->getTheInfoMergedVariables();
        return $vars;
    }

}

