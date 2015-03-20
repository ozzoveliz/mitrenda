<?php

    class Dotsquares_Maintenancemode_Helper_Data extends Mage_Core_Helper_Abstract
    {
		public function isNeedEnable(){
		
				$storeId = Mage::app()->getStore()->getStoreId();
				$total_hour = Mage::getStoreConfig('maintenancemode/timer/total_hour',$storeId);
				$total_min = Mage::getStoreConfig('maintenancemode/timer/total_min',$storeId);
				
				$enabledDateTime = Mage::getStoreConfig('maintenancemode/general/enable_time',$storeId);
				$givenDate = strtotime($enabledDateTime);		 		
				$endDateTime = date('Y/m/d H:i:s', strtotime("+$total_hour hours +$total_min minutes", $givenDate)); 
				
				$currentDatetime = Mage::getModel('core/date')->date('Y/m/d H:i:s');
					
				if(strtotime($currentDatetime) < strtotime($endDateTime)){
					return true;
				}
				return false;
		}       
	   
	}