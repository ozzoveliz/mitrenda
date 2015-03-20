<?php
/**
 * Created by PhpStorm.
 * User: TOSHIBA
 * Date: 5/30/14
 * Time: 7:00 PM
 */ 
class Onetree_Core_Model_Core_Layout extends Mage_Core_Model_Layout
{
    protected function _generateBlock($node, $parent)
    {
        if (!empty($node['class'])) {
            $className = (string)$node['class'];
        } else {
            $className = (string)$node['type'];
        }

        $blockName = (string)$node['name'];
        $_profilerKey = 'BLOCK: '.$blockName;
        Varien_Profiler::start($_profilerKey);

        $block = $this->addBlock($className, $blockName);
        if (!$block) {
            return $this;
        }

        if (!empty($node['parent'])) {
            $parentBlock = $this->getBlock((string)$node['parent']);
        } else {
            $parentName = $parent->getBlockName();
            if (!empty($parentName)) {
                $parentBlock = $this->getBlock($parentName);
            }
        }
        if (!empty($parentBlock)) {
            $alias = isset($node['as']) ? (string)$node['as'] : '';
            if (isset($node['before'])) {
                $sibling = (string)$node['before'];
                if ('-'===$sibling) {
                    $sibling = '';
                }
                $parentBlock->insert($block, $sibling, false, $alias);
            } elseif (isset($node['after'])) {
                $sibling = (string)$node['after'];
                if ('-'===$sibling) {
                    $sibling = '';
                }
                $parentBlock->insert($block, $sibling, true, $alias);
            } else {
                $parentBlock->append($block, $alias);
            }
        }
        if (!empty($node['template'])) {
            if ($block instanceof Mage_Catalog_Block_Product_List) {
                $category = Mage::registry('current_category');
                if (isset($category)) {
                    if ($category->getData('is_lookbook') && !empty($node['template_lookbook'])) {
                        $block->setTemplate((string)$node['template_lookbook']);
                        $block->setData('template_lookbook',(string)$node['template_lookbook']);
                    } else {
                        $block->setTemplate((string)$node['template']);
                    }
                } else {
                    $block->setTemplate((string)$node['template']);
                }
            } else {
                $block->setTemplate((string)$node['template']);
            }
        }

        if (!empty($node['output'])) {
            $method = (string)$node['output'];
            $this->addOutputBlock($blockName, $method);
        }
        Varien_Profiler::stop($_profilerKey);

        return $this;
    }
}