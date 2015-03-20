<?php
    class Dotsquares_Maintenancemode_Model_Observer
    {
        const XML_PATH_EMAIL_SENDER     = 'contacts/email/sender_email_identity';
		
		
		public function adminSystemConfigChangedSectionMaintenancemode(Varien_Event_Observer $observer)
		{
			$enabled = Mage::getStoreConfig('maintenancemode/general/enabled');
			if($enabled == '0' || is_null($enabled)){
				$updateCofig = new Mage_Core_Model_Config();
				$updateCofig->saveConfig('maintenancemode/general/enable_time',NULL);
			}else{
				$currentDatetime = Mage::getModel('core/date')->date('Y/m/d H:i:s');
				$updateCofig = new Mage_Core_Model_Config();
				$updateCofig->saveConfig('maintenancemode/general/enable_time',$currentDatetime);
			}

 			return $this;
		}
	
	
        public function initControllerRouters($request) 
        {
            $frontName = (string)Mage::getConfig()->getNode('admin/routers/adminhtml/args/frontName'); 

          	$area = Mage::app()->getRequest()->getOriginalPathInfo();
			$areaArray = explode('/', $area); 
			 
			 
			//if((!preg_match('/'.$frontName.'/',$area)) && (!preg_match('/postDisable/',$area)))
            if (!in_array($frontName, $areaArray) && (!in_array('postDisable', $areaArray))) 
            {
			 					
                $storeId = Mage::app()->getStore()->getStoreId();
                $isEnabled = Mage::getStoreConfig('maintenancemode/general/enabled',$storeId);

                if ($isEnabled == 1) 
                {
                    $allowedIPs = Mage::getStoreConfig('maintenancemode/general/allowedIPs',$storeId);
                    $allowedIPs = preg_replace('/ /', '', $allowedIPs);
                    $IPs = array();
                    if ('' !== trim($allowedIPs)) 
                    {
                        $IPs = explode(',', $allowedIPs);
                    }
                    $currentIP = $_SERVER['REMOTE_ADDR'];
                    $allowForAdmin = Mage::getStoreConfig('maintenancemode/general/allowforadmin',$storeId);
                    $adminIpAddress = NULL;


                   if ($allowForAdmin == 1) 
                    {
						Mage::getSingleton('core/session', array('name' => 'adminhtml'));
						$adminSession = Mage::getSingleton('admin/session');
						$isAdminLoggedIn = $adminSession->isLoggedIn();
						if($isAdminLoggedIn){
                     		$adminIpAddress = $adminSession['_session_validator_data']['remote_addr'];
						}
                    }

                    if ($currentIP === $adminIpAddress) 
                    {
                        $this->generateLog('Access granted for admin with IP: ' . $currentIP . ' and store ' . $storeId, $storeId);

                    } else {
                        if (!in_array($currentIP, $IPs))
                        {
                            $this->generateLog('Access denied  for IP: ' . $currentIP . ' and store ' . $storeId,  $storeId);
							
	
                            $html = trim(Mage::getSingleton('core/layout')->createBlock('core/template')->setTemplate('maintenancemode/maintenancemode.phtml')->toHtml());

                            if ($html !== '') 
                            { 
                                Mage::getSingleton('core/session', array('name' => 'front'));
                                $response = $request->getEvent()->getFront()->getResponse();
                                $response->setHeader('HTTP/1.1', '503 Service Temporarily Unavailable');
                                $response->setHeader('Status', '503 Service Temporarily Unavailable');
                                $response->setHeader('Retry-After', '5000');
                                $response->setBody($html);
                                $response->sendHeaders();
                                $response->outputBody();
                            }
                            exit();
                        } else {
                            $this->generateLog('Access granted for IP: ' . $currentIP . ' and store ' . $storeId,  $storeId);
                        }
                    }
                }

            }
        }


        private function generateLog($text, $storeId = null, $zendLevel = Zend_Log::DEBUG) 
        {
             Mage::log($text, $zendLevel, 'maintenancemode.log');
        }

    }
