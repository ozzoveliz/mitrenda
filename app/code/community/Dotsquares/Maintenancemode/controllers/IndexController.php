<?php
    class Dotsquares_Maintenancemode_IndexController extends Mage_Core_Controller_Front_Action
    {
        const XML_PATH_EMAIL_RECIPIENT  = 'maintenancemode/contactus/from_mail';
        const XML_PATH_EMAIL_SENDER     = 'contacts/email/sender_email_identity';
        const XML_PATH_EMAIL_CONTACTS     = 'contacts/email/recipient_email';

		
        public function postDisableAction(){
				$result = array();
				$post = $this->getRequest()->getPost();
                $result["result"]="failed";
				if ($post) {
					if (!($formKey = $this->getRequest()->getParam('form_key', null))) { // TODO: use of form kay validation, it's bypassed for now
							$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
							return ;
					}
						
					$isAjax = $this->getRequest()->getParam('isAjax', false);
					if($isAjax == false){
							$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
							return ;
					}
					
					if(!Mage::helper('maintenancemode')->isNeedEnable()){
						$result["result"]="success";
						$updateCofig = new Mage_Core_Model_Config();
						$updateCofig->saveConfig('maintenancemode/general/enable_time',NULL);
						$updateCofig->saveConfig('maintenancemode/general/enabled',0);
						Mage::app()->cleanCache();
					}
  				}
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
				return ;
				
		}
		
      }
