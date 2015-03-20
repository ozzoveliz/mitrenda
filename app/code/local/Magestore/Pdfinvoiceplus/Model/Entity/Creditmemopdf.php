<?php

class Magestore_Pdfinvoiceplus_Model_Entity_Creditmemopdf extends Magestore_Pdfinvoiceplus_Model_Entity_Creditmemogenerator
{
    public $creditmemoId;
    
    public $templateId;

    public function getTheCreditmemo()
    {
        $invoice = Mage::getModel('sales/order_creditmemo')->load($this->creditmemoId);
        return $invoice;
    }

    public function getThePdf($creditmemoId, $templateId)
    {
        $this->templateId = $templateId;
        $this->creditmemoId = $creditmemoId;
        $this->setVars(Mage::helper('pdfinvoiceplus')->processAllVars($this->collectVars()));
        return $this->getPdf();
    }
        public function collectVars()
    {
         $vars = Mage::getModel('pdfinvoiceplus/entity_additional_info')
                ->setSource($this->getTheCreditmemo())
                ->setOrder($this->getTheCreditmemo()->getOrder())
                ->getTheInfoMergedVariables();   
         
         return $vars;
    }
}
