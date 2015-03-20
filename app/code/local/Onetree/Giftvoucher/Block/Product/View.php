<?php
/**
 * Created by PhpStorm.
 * User: Juan Carlos Conde
 * Date: 11/14/14
 * Time: 11:07 AM
 */ 
class Onetree_Giftvoucher_Block_Product_View extends Magestore_Giftvoucher_Block_Product_View
{
    public function getAvailableTemplate() {
        $product = $this->getProduct();
        $product_template = $product->getGiftTemplateIds();
        if($product_template){
            $product_template = explode(',', $product_template);
        }else $product_template = array();

        $templates = Mage::getModel('giftvoucher/gifttemplate')->getCollection()
            ->addFieldToFilter('status', '1')
            ->addFieldToFilter('giftcard_template_id', array('in'=>$product_template));

        $datas = $templates->getData();
        if (count($datas) > 0) {
            foreach($datas as &$data) {
                if (isset($data['notes']) && !empty($data['notes'])) {
                    $data['notes'] = $this->getPrintNotes($data['notes']);
                }
            }
        }
        return $datas;
    }

    private function getPrintNotes($notes) {
        $notes = str_replace(array(
            '{store_url}',
            '{store_name}',
            '{store_address}'
        ), array(
            Mage::app()->getStore($this->getStoreId())->getBaseUrl(),
            Mage::app()->getStore($this->getStoreId())->getFrontendName(),
            Mage::getStoreConfig('general/store_information/address', $this->getStoreId())
        ), $notes);

        return $notes;
    }

    public function getGiftAmount($product) {

        $gift_value = Mage::helper('giftvoucher/giftproduct')->getGiftValue($product);
        $store = Mage::app()->getStore();
        switch ($gift_value['type']) {
            case 'range':
//                $gift_value['from'] = $this->convertPrice($product, $gift_value['from']);
//                $gift_value['to'] = $this->convertPrice($product, $gift_value['to']);
                $gift_value['from_txt'] = $store->formatPrice($gift_value['from']);
                $gift_value['to_txt'] = $store->formatPrice($gift_value['to']);
                break;
            case 'dropdown':
                //$gift_value['options'] = $this->_convertPrices($product, $gift_value['options']);
                $gift_value['prices'] = $this->_convertPrices($product, $gift_value['prices']);
                $gift_value['prices'] = array_combine($gift_value['options'],$gift_value['prices']);
                $gift_value['options_txt'] = $this->_formatPrices($gift_value['options']);
                break;
            case 'static':
//                $gift_value['value'] = $this->convertPrice($product, $gift_value['value']);
                $gift_value['value_txt'] = $store->formatPrice($gift_value['value']);
                $gift_value['value_txt'] = str_replace('U','', $gift_value['value_txt']);
                $gift_value['price'] = $this->convertPrice($product, $gift_value['gift_price']);
                break;
            default:
                $gift_value['type'] = 'any';
        }
        return $gift_value;
    }
}