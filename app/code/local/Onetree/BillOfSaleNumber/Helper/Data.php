<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @package    Onetree_Abitab
 * @copyright  Copyright (c) 2010 ITABS GbR - Rouven Alexander Rieker
 * @copyright  Copyright (c) 2010 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Onetree_BillOfSaleNumber_Helper_Data extends Mage_Payment_Helper_Data
{
    public function checkBillFormat($bill){
        return str_pad($bill,7,'0',STR_PAD_LEFT);
    }
}