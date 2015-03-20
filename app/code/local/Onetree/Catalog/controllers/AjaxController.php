<?php

require_once(Mage::getBaseDir('lib') . '/recaptcha/recaptchalib.php');

class Onetree_Catalog_AjaxController extends Mage_Core_Controller_Front_Action
{
   public function indexAction ()
   {
		$this->loadLayout();
		$this->renderLayout();
   }
   
   public function categoryproductAction()
   { 
     $html='Hola dav.q';
     $this->getResponse()->setHeader('Content-type', 'application/html');
     $this->getResponse()->setBody($html);
   }

}
?>

































































