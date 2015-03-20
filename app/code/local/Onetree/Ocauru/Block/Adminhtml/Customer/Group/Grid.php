<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml customers groups grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Onetree_Ocauru_Block_Adminhtml_Customer_Group_Grid extends Mage_Adminhtml_Block_Customer_Group_Grid
{

    public function __construct() {
        parent::__construct();
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        
        $this->addColumn('end_user', array(
            'header' => Mage::helper('customer')->__('Is End User'),
            'index' => 'end_user',
            'width' => '50px',
            'index' => 'customer_group_is_end_user',
            'renderer'  => 'Onetree_Ocauru_Block_Adminhtml_Customer_Group_Renderer_Isenduser',
        ));

        return $this;
    }
}
