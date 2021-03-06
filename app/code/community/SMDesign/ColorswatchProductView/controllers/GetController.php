<?php
class SMDesign_ColorswatchProductView_GetController extends Mage_Core_Controller_Front_Action {

    /**
     * funcion original de la extension colorswatch, nombre de funcion mainImageAction cambiado
     * a mainImageOriginalAction
     */
    function mainImageOriginalAction() {

		$selection = Mage::helper('core')->jsonDecode($this->getRequest()->getParam('selection', '[]'));
		$attributeId = $this->getRequest()->getParam('attribute_id');
		$optionId = $this->getRequest()->getParam('option_id');
		$productId = $this->getRequest()->getParam('product_id');
    	$imageSelector = $this->getRequest()->getParam('image_selector', '.product-img-box img#image');
    
		$_product = Mage::getModel('catalog/product')->load($productId);
		if (!$_product->getId()) {
			$this->_forward('noRoute');
			return;
		}

		$selectedAttributeCode = $_product->getTypeInstance(true)->getAttributeById($attributeId, $_product)->getAttributeCode();

		$colorswatch = Mage::getModel('colorswatch/product_swatch')->setProduct($_product);
		$allProducts = $colorswatch->getAllowProducts();

		foreach ($allProducts as $product) {
		    if ($product->isSaleable() && $product->getIsInStock()) {
		    	if (Mage::getModel('colorswatch/attribute_settings')->getConfig($attributeId, 'allow_attribute_to_change_main_image') == 1 ) {
		    		if ($product->getData($selectedAttributeCode) == $optionId) {
		    			 $products[] = $product;
		    		}
		    	} else {
		    		 $products[] = $product;
		    	}
		    }
		}

		$selected = array();
		foreach ($selection as $key=>$val) {
			if ($val && Mage::getModel('colorswatch/attribute_settings')->getConfig($key, 'allow_attribute_to_change_main_image') == 1) {
				$selected[$key] = $val;
			}
		}

		$allAvialableAttributeCode = $colorswatch->getAllAttributeCodes();
		foreach ($colorswatch->getAllAttributeIds() as $aKey=>$aId) {
			
			if (!isset($selected[$aId]) && Mage::getModel('colorswatch/attribute_settings')->getConfig($aId, 'allow_attribute_to_change_main_image') == 1) {
				$options = $colorswatch->getAttributeById($aId)->getColorswatchOptions()->getData();
				$optionCount = count($options);
				$optionIndex = 0;
				
				while ($optionIndex < $optionCount) {
					$option = $options[$optionIndex];
					
					if ($this->productExsist($products, $allAvialableAttributeCode[$aKey], $option['option_id'])) {
						$selected[$aId] = $option['option_id'];
						$optionIndex = count($options);
					}
					$optionIndex++;
				}
			}

			if (isset($selected[$aId])) {
				foreach ($products as $key=>$simpleProduct) {
						if ($simpleProduct->getData($allAvialableAttributeCode[$aKey]) != $selected[$aId]) {
							unset($products[$key]);
						}
			  }
			}
		}
		
		if (count($selected) == 0) { // not have attribut who is allowed to change image
		  echo "SMDesignColorswatchPreloader.removePerload($$('.product-image img')[0]);";
		  echo "  //not have allowed attribute to change image.\n";
		  exit();
		}
		
		/*  calculate image sizes */
		if (Mage::getStoreConfig('smdesign_smdzoom/zoom/enabled') && $_product->getData('enable_zoom_plugin') == 1) {
			/* smdzoom plugin installed*/
			$zoomConfig = array();
	    	$zoomConfig['image_width'] 		= Mage::getStoreConfig('smdesign_smdzoom/zoom/image_width');
	    	$zoomConfig['image_height'] 	= Mage::getStoreConfig('smdesign_smdzoom/zoom/image_height');
	    	$zoomConfig['thumbnail_width'] 	= Mage::getStoreConfig('smdesign_smdzoom/zoom/thumbnail_width');
	    	$zoomConfig['thumbnail_height'] = Mage::getStoreConfig('smdesign_smdzoom/zoom/thumbnail_height');
	    	$zoomConfig['wrapper_width'] 	= Mage::getStoreConfig('smdesign_smdzoom/zoom/wrapper_width');
	    	$zoomConfig['wrapper_height'] 	= Mage::getStoreConfig('smdesign_smdzoom/zoom/wrapper_height');
	    	$zoomConfig['wrapper_offset_left'] 	= Mage::getStoreConfig('smdesign_smdzoom/zoom/wrapper_offset_left');
	    	$zoomConfig['wrapper_offset_top'] 	= Mage::getStoreConfig('smdesign_smdzoom/zoom/wrapper_offset_top');
	    	$zoomConfig['zoom_type'] 	= Mage::getStoreConfig('smdesign_smdzoom/zoom/zoom_type');
	    	$zoomConfig['zoom_ratio'] 	= intval(Mage::getStoreConfig('smdesign_smdzoom/zoom/zoom_ratio'));
	    	$zoomConfig['show_zoom_effect'] = Mage::getStoreConfig('smdesign_smdzoom/zoom/show_zoom_effect');
	    	$zoomConfig['hide_zoom_effect'] = Mage::getStoreConfig('smdesign_smdzoom/zoom/hide_zoom_effect');
	    	$zoomConfig['show_info_error'] 	= Mage::getStoreConfig('smdesign_smdzoom/zoom/show_info_error');
	    	$zoomConfig['more_view'] 	= Mage::getStoreConfig('smdesign_smdzoom/zoom/more_view_change_main_image');
	    	$zoomConfig['show_preloader'] 	= Mage::getStoreConfig('smdesign_smdzoom/zoom/show_preloader');
	    	
	    	if ($zoomConfig['zoom_ratio'] == "" || $zoomConfig['zoom_ratio'] == 0 || $zoomConfig['zoom_ratio'] == 1) {
	    		$zoomConfig['zoom_ratio'] = 2;
	    	}
	    	
	    	switch ($zoomConfig['zoom_type']){
	    		default:
		    	case 0:
		    		/* outside */
		    		$ratioModifierWidth = 0;
		    		$ratioModifierHeight = 0;
		    		if ($zoomConfig['image_width'] * $zoomConfig['zoom_ratio'] <= $zoomConfig['wrapper_width']) {
		    			$ratioModifierWidth = intval($zoomConfig['wrapper_width'] / ($zoomConfig['image_width'] * $zoomConfig['zoom_ratio']) );
		    		}
		    		if ($zoomConfig['image_height'] * $zoomConfig['zoom_ratio'] <= $zoomConfig['wrapper_height']) {
		    			$ratioModifierHeight = intval($zoomConfig['wrapper_height'] / ($zoomConfig['image_height'] * $zoomConfig['zoom_ratio']) );
		    		}
		    		$zoomConfig['zoom_ratio'] = $zoomConfig['zoom_ratio'] + max($ratioModifierWidth,$ratioModifierHeight);
		    	break;
		    	case 1:
	    			/* inside */
	    			$zoomConfig['show_zoom_effect'] = "none";
	    			$zoomConfig['hide_zoom_effect'] = "none";
		    		$ratioModifierWidth = 0;
		    		$ratioModifierHeight = 0;
		    		
		    		if ($zoomConfig['image_width'] * $zoomConfig['zoom_ratio'] <= $zoomConfig['wrapper_width']) {
		    			$ratioModifierWidth = intval($zoomConfig['wrapper_width'] / ($zoomConfig['image_width'] * $zoomConfig['zoom_ratio']) );
		    		}
		    		if ($zoomConfig['image_height'] * $zoomConfig['zoom_ratio'] <= $zoomConfig['wrapper_height']) {
		    			$ratioModifierHeight = intval($zoomConfig['wrapper_height'] / ($zoomConfig['image_height'] * $zoomConfig['zoom_ratio']) );
		    		}
		    		$zoomConfig['zoom_ratio'] = $zoomConfig['zoom_ratio'] + max($ratioModifierWidth,$ratioModifierHeight);
		    	break;
		    	case 2:
		    		/* full */
		    		$zoomConfig['show_zoom_effect'] = "none";
	    			$zoomConfig['hide_zoom_effect'] = "none";
	    			$zoomConfig['wrapper_offset_left'] 	= 0;
	    			$zoomConfig['wrapper_offset_top'] 	= 0;
	    			$zoomConfig['wrapper_width'] 	= $zoomConfig['image_width'];
	    			$zoomConfig['wrapper_height'] 	= $zoomConfig['image_height'];
		    	break;
	    	}
		    
	    	$zoomConfig['upscale_image_width'] = $zoomConfig['zoom_ratio'] * $zoomConfig['image_width'];
	    	$zoomConfig['upscale_image_height'] = $zoomConfig['zoom_ratio'] * $zoomConfig['image_height'];
	    	$bigImageWidth 	= $zoomConfig['upscale_image_width'];
	 		$bigImageHeight = $zoomConfig['upscale_image_height'];
	 		$mainImageWidth = $zoomConfig['image_width'];
	 		$mainImageHeight = $zoomConfig['image_height'];
	 		$thumbImageWidth = $zoomConfig['thumbnail_width'];
	 		$thumbImageHeight= $zoomConfig['thumbnail_height'];

		}else{
			/* smdzoom not being used */
			$bigImageWidth 	= null;
	 		$bigImageHeight = null;
	 		$mainImageWidth = null;//$this->getRequest()->getParam('img_width', null);
	 		$mainImageHeight= null;//$this->getRequest()->getParam('img_height', null);
	 		$thumbImageWidth = 56;
	 		$thumbImageHeight= 56;
		}
		
		$images = array();
		if (count($products) > 0) {
			foreach ($products as $simpleProduct) {
				if (count($images) == 0) {
					$simpleProduct->load();
					$simpleProductImages = $simpleProduct->getMediaGalleryImages();
					
					if (count($simpleProductImages)) {
						foreach ($simpleProductImages as $_image) {
							if ($simpleProduct->getImage() == $_image->getData('file') ) { // Is base image if exsist go on top of array
								array_unshift($images, array(
								'id'=> $_image->getId(),
								'product_id'=> $simpleProduct->getId(),
								'product'=> $simpleProduct,
								'label'=> $_image->getLabel(),
								'big_image'=> sprintf(Mage::helper('catalog/image')->init($simpleProduct, 'image', $_image->getFile())->resize($bigImageWidth, $bigImageHeight)),
								'image'=> sprintf(Mage::helper('catalog/image')->init($simpleProduct, 'image', $_image->getFile())->resize($mainImageWidth, $mainImageHeight)),
								'thumb'=> sprintf(Mage::helper('catalog/image')->init($simpleProduct, 'image', $_image->getFile())->resize($thumbImageWidth,$thumbImageHeight))
								));
							} else {
								array_push($images, array(
								'id'=> $_image->getId(),
								'product_id'=> $simpleProduct->getId(),
								'product'=> $simpleProduct,
								'label'=> $_image->getLabel(),
								'big_image'=> sprintf(Mage::helper('catalog/image')->init($simpleProduct, 'image', $_image->getFile())->resize($bigImageWidth, $bigImageHeight)),
								'image'=> sprintf(Mage::helper('catalog/image')->init($simpleProduct, 'image', $_image->getFile())->resize($mainImageWidth, $mainImageHeight)),
								'thumb'=> sprintf(Mage::helper('catalog/image')->init($simpleProduct, 'image', $_image->getFile())->resize($thumbImageWidth,$thumbImageHeight))
								));
							}
						
						}
					}
					
				}
			}
		}
		
		if (count($images) == 0) {
			foreach ($_product->getMediaGalleryImages() as $_image) {
				$images[] = array(
					'big_image'=> sprintf(Mage::helper('catalog/image')->init($_product, 'thumbnail', $_image->getFile())->resize($bigImageWidth, $bigImageHeight)),
					'image'=> sprintf(Mage::helper('catalog/image')->init($_product, 'thumbnail', $_image->getFile())->resize($mainImageWidth, $mainImageHeight)),
					'thumb'=> sprintf(Mage::helper('catalog/image')->init($_product, 'thumbnail', $_image->getFile())->resize($thumbImageWidth,$thumbImageHeight)),
					'label'=> $_image->getLabel(),
					'id'=> $_image->getId(),
					'product_id'=> $productId,
					'product'=> $_product
				);
			}
		}

		if (count($images) == 0) {
			echo "SMDesignColorswatchPreloader.removePerload($$('.product-image img')[0]);\n";
			if (Mage::getStoreConfig('smdesign_colorswatch/general/update_more_view')) {
				echo "$$('.more-views ul')[0].update('');";
			}
			$image = Mage::helper('catalog/image')->init($_product, 'image')->resize($mainImageWidth, $mainImageHeight);
			echo "$$('.product-image img')[0].src = '{$image}?rand=' + Math.random();";
			exit();
		}
?>
<?php if (Mage::getStoreConfig('smdesign_smdzoom/zoom/enabled') && $_product->getData('enable_zoom_plugin') == 1) :?>
<?php /*)
$('image-zoom').href = '<?php echo $images[0]['big_image']?>?rand=' + Math.random();
<!--SMDesignsmdzoomPreloader = new SMDesignsmdzoomPreload({showPreloader:<?php echo $zoomConfig['show_preloader']; ?>});
SMDesignsmdzoomPreloader.setImage('/skin/frontend/default/default/images/smdzoom/smdzoom_loading.gif');
SMDesignsmdzoomPreloader.showPerload($('image-zoom'));-->
*/ ?>
tempLink = new Object();
tempLink.rel = '<?php echo $images[0]['image']?>?rand=' + Math.random();
tempLink.href = '<?php echo $images[0]['big_image']?>?rand=' + Math.random();
SMDUpdateMainImage(tempLink);

<?php else : ?>
$$('<?php echo $imageSelector ?>').first().src = '<?php echo $images[0]['image']?>?rand=' + Math.random();
<?php endif; ?>
<?php if (Mage::getStoreConfig('smdesign_colorswatch/general/update_more_view')) : ?>
galleryView = $$('.more-views ul');
if (galleryView.length == 0) {
	galleryViewContainer = document.createElement('div');
	galleryViewContainer.className = 'more-views';
	galleryView = document.createElement('ul');
	galleryViewContainer.appendChild(galleryView);
	if ($$('.product-img-box').length > 0) {
		$$('.product-img-box').first().appendChild(galleryViewContainer);
	}
} else {
	galleryView = galleryView[0];
}
galleryView.update('');

<?php foreach ($images as $key=>$image) : ?>
	li = document.createElement('LI');
	<?php if (Mage::getStoreConfig('smdesign_smdzoom/zoom/enabled') && Mage::getStoreConfig('smdesign_smdzoom/zoom/more_view_change_main_image') && $_product->getData('enable_zoom_plugin') == 1 && ( 1 || $_product->getImage() != 'no_selection' && $_product->getImage() ) ) : ?>
		$(li).update("<a href=\"<?php echo  $image['big_image']; ?>\"  rel=\"<?php echo  $image['image']; ?>\" onclick=\"SMDUpdateMainImage(this);return false;\"><img src=\"<?php echo  $image['thumb'] ?>\" width=\"<?php echo $thumbImageWidth; ?>\" height=\"<?php echo $thumbImageHeight; ?>\" alt=\"<?php echo  $image['label'] ?>\" /></a>");
	<?php else : ?>
		$(li).update("<a href=\"#\" onclick=\"popWin('<?php echo Mage::getUrl('catalog/product/gallery', array('id'=>$image['product_id'], 'image'=>$image['id'], 'pid'=>$productId)) ?>', 'gallery', 'width=300,height=300,left=0,top=0,location=no,status=yes,scrollbars=yes,resizable=yes'); return false;\" title=\"<?php echo  $image['label'] ?>\"><img src=\"<?php echo  $image['thumb'] ?>\" width=\"56\" height=\"56\" alt=\"<?php echo  $image['label'] ?>\" /></a>");
		
	<?php endif; ?>
	galleryView.appendChild(li);
<?php endforeach; ?>

<?php endif; ?>

/*
<?php if (!(Mage::getStoreConfig('smdesign_smdzoom/zoom/enabled') && Mage::getStoreConfig('smdesign_smdzoom/zoom/more_view_change_main_image') && $_product->getData('enable_zoom_plugin') == 1 && ( $_product->getImage() != 'no_selection' && $_product->getImage() ) ) ): ?>
SMDesignColorswatchPreloader.removePerload($('image'));
<?php endif; ?>
*/

<?php }













    /**
     * funcion modificada para que trabaje con el theme de mitrenda
     */
    function mainImageAction() {
        $_helper = Mage::helper('catalog/output');
        $helpZoom = Mage::helper('infortis_cloudzoom');
        $helpImg = Mage::helper('infortis/image');

        //Get image sizes. If height is not specified, aspect ratio will be kept.
        $imgWidth		= intval($helpZoom->getCfg('images/main_width'));
        $imgHeight		= intval($helpZoom->getCfg('images/main_height'));
        $bigImageWidth  = intval($helpZoom->getCfg('general/big_image_width'));
        $bigImageHeight = intval($helpZoom->getCfg('general/big_image_height'));

        //If main image width is not specified, use default values
        if ($imgWidth <= 0)
        {
            $imgWidth = 364;
            $imgHeight = 364;
        }
        //$imgBorder = 1;
        //$imgPadd = 5;
        //$imgTotalPadd = ($imgBorder + $imgPadd) * 2; //12
        //$imgWidth -= $imgTotalPadd;
        //$imgHeight -= $imgTotalPadd;

        //If main image width is not specified, use default values
        if ($bigImageWidth <= 0)
        {
            $bigImageWidth = 650;
            $bigImageHeight= 650;
        }

        //Thumbnail images size
        $thumbImgWidth = 65;
        $thumbImgHeight = 65;
        $thumbTotalPadd = 14; //Additional padding, border and margin (horizontal axis) around the image
        $thumbTotalWidth = $thumbImgWidth + $thumbTotalPadd;
        //	$thumbsShowCount = 3;
        //	$thumbTotalWidth = floor(($imgWidth * 0.8) / $thumbsShowCount);
        //	$thumbImgWidth = $thumbTotalWidth - $thumbTotalPadd;
        //	$thumbImgHeight = $thumbTotalWidth - $thumbTotalPadd;

        //Aspect ratio settings
        if ($helpZoom->getCfg('images/aspect_ratio'))
        {
            //Height will be computed automatically (based on width) to keep the aspect ratio of the image
            $imgHeight = 0;
            $bigImageHeight= 0;
            $thumbImgHeight = 0;
        }
        /* END helper config */

        $selection = Mage::helper('core')->jsonDecode($this->getRequest()->getParam('selection', '[]'));
        $attributeId = $this->getRequest()->getParam('attribute_id');
        $optionId = $this->getRequest()->getParam('option_id');
        $productId = $this->getRequest()->getParam('product_id');
        $imageSelector = $this->getRequest()->getParam('image_selector', '.product-image img');

        $_product = Mage::getModel('catalog/product')->load($productId);
        if (!$_product->getId()) {
            $this->_forward('noRoute');
            return;
        }

        $selectedAttributeCode = $_product->getTypeInstance(true)->getAttributeById($attributeId, $_product)->getAttributeCode();

        $colorswatch = Mage::getModel('colorswatch/product_swatch')->setProduct($_product);
        $allProducts = $colorswatch->getAllowProducts();

        foreach ($allProducts as $product) {
            if ($product->isSaleable() && $product->getIsInStock()) {
                if (Mage::getModel('colorswatch/attribute_settings')->getConfig($attributeId, 'allow_attribute_to_change_main_image') == 1 ) {
                    if ($product->getData($selectedAttributeCode) == $optionId) {
                        $products[] = $product;
                    }
                } else {
                    $products[] = $product;
                }
            }
        }

        $selected = array();
        foreach ($selection as $key=>$val) {
            if ($val && Mage::getModel('colorswatch/attribute_settings')->getConfig($key, 'allow_attribute_to_change_main_image') == 1) {
                $selected[$key] = $val;
            }
        }

        $allAvialableAttributeCode = $colorswatch->getAllAttributeCodes();
        foreach ($colorswatch->getAllAttributeIds() as $aKey=>$aId) {

            if (!isset($selected[$aId]) && Mage::getModel('colorswatch/attribute_settings')->getConfig($aId, 'allow_attribute_to_change_main_image') == 1) {
                $options = $colorswatch->getAttributeById($aId)->getColorswatchOptions()->getData();
                $optionCount = count($options);
                $optionIndex = 0;

                while ($optionIndex < $optionCount) {
                    $option = $options[$optionIndex];

                    if ($this->productExsist($products, $allAvialableAttributeCode[$aKey], $option['option_id'])) {
                        $selected[$aId] = $option['option_id'];
                        $optionIndex = count($options);
                    }
                    $optionIndex++;
                }
            }

            if (isset($selected[$aId])) {
                foreach ($products as $key=>$simpleProduct) {
                    if ($simpleProduct->getData($allAvialableAttributeCode[$aKey]) != $selected[$aId]) {
                        unset($products[$key]);
                    }
                }
            }
        }

        if (count($selected) == 0) { // not have attribut who is allowed to change image
            echo "SMDesignColorswatchPreloader.removePerload($$('.product-image img')[0]);";
            echo "  //not have allowed attribute to change image.\n";
            exit();
        }

        /* smdzoom not being used */
        $bigImageWidth 	= null;
        $bigImageHeight = null;
        $mainImageWidth = null;//$this->getRequest()->getParam('img_width', null);
        $mainImageHeight= null;//$this->getRequest()->getParam('img_height', null);
        $thumbImageWidth = 56;
        $thumbImageHeight= 56;

        $images = array();
        if (count($products) > 0) {
            foreach ($products as $simpleProduct) {
                if (count($images) == 0) {
                    $simpleProduct->load();
                    $simpleProductImages = $simpleProduct->getMediaGalleryImages();
                    if (count($simpleProductImages)) {
                        foreach ($simpleProductImages as $_image) {
                            if ($simpleProduct->getImage() == $_image->getData('file') ) { // Is base image if exsist go on top of array
                                array_unshift($images, array(
                                    'id'=> $_image->getId(),
                                    'value_id'=> $_image->getValueId(),
                                    'product_id'=> $simpleProduct->getId(),
                                    'product'=> $simpleProduct,
                                    'label'=> $_image->getLabel(),
                                    'big_image'=> sprintf(Mage::helper('catalog/image')->init($simpleProduct, 'image', $_image->getFile())->resize($bigImageWidth, $bigImageHeight)),
                                    'image'=> sprintf(Mage::helper('catalog/image')->init($simpleProduct, 'image', $_image->getFile())->resize($mainImageWidth, $mainImageHeight)),
                                    'thumb'=> sprintf(Mage::helper('catalog/image')->init($simpleProduct, 'image', $_image->getFile())->resize($thumbImageWidth,$thumbImageHeight))
                                ));
                            } else {
                                array_push($images, array(
                                    'id'=> $_image->getId(),
                                    'value_id'=> $_image->getValueId(),
                                    'product_id'=> $simpleProduct->getId(),
                                    'product'=> $simpleProduct,
                                    'label'=> $_image->getLabel(),
                                    'big_image'=> sprintf(Mage::helper('catalog/image')->init($simpleProduct, 'image', $_image->getFile())->resize($bigImageWidth, $bigImageHeight)),
                                    'image'=> sprintf(Mage::helper('catalog/image')->init($simpleProduct, 'image', $_image->getFile())->resize($mainImageWidth, $mainImageHeight)),
                                    'thumb'=> sprintf(Mage::helper('catalog/image')->init($simpleProduct, 'image', $_image->getFile())->resize($thumbImageWidth,$thumbImageHeight))
                                ));
                            }

                        }
                    }

                }
            }
        }

        if (count($images) == 0) {
            foreach ($_product->getMediaGalleryImages() as $_image) {
                $images[] = array(
                    'big_image'=> sprintf(Mage::helper('catalog/image')->init($_product, 'thumbnail', $_image->getFile())->resize($bigImageWidth, $bigImageHeight)),
                    'image'=> sprintf(Mage::helper('catalog/image')->init($_product, 'thumbnail', $_image->getFile())->resize($mainImageWidth, $mainImageHeight)),
                    'thumb'=> sprintf(Mage::helper('catalog/image')->init($_product, 'thumbnail', $_image->getFile())->resize($thumbImageWidth,$thumbImageHeight)),
                    'label'=> $_image->getLabel(),
                    'id'=> $_image->getId(),
                    'value_id'=> $_image->getValueId(),
                    'product_id'=> $productId,
                    'product'=> $_product
                );
            }
        }

        if (count($images) == 0) {
            echo "SMDesignColorswatchPreloader.removePerload($$('.product-image img')[0]);\n";
            if (Mage::getStoreConfig('smdesign_colorswatch/general/update_more_view')) {
                echo "$$('.more-views ul')[0].update('');";
            }
            $image = Mage::helper('catalog/image')->init($_product, 'image')->resize($mainImageWidth, $mainImageHeight);
            echo "$$('.product-image img')[0].src = '{$image}?rand=' + Math.random();";
            exit();
        }
        ?>
        <?php
            $imageCount = count($images);

            //Modify thumbnail slider depending on number of thumbnails
            $sliderClasses = '';
            if ($imageCount > 0)
            {
                if ($imageCount <= 5)
                    $sliderClasses .= " count-$imageCount";
                else
                    $sliderClasses .= " count-multi";
            }
            if ($imageCount < 4)
                $sliderClasses .= ' hide-direction-nav';
        ?>

            jQuery(function($) {
                var t; $(window).resize(function() { clearTimeout(t); t = setTimeout(function() { $(".more-images .cloud-zoom-gallery").first().click(); }, 200); });
            });

            jQuery(function($) {
                $('<?php echo $imageSelector ?>').first().attr('src', '<?php echo $images[0]['image']?>?rand=' + Math.random());
                $('.product-image #zoom1').attr('href', '<?php echo $images[0]['image']?>?rand=' + Math.random());
                $('.product-image #zoom-btn').attr('href', '<?php echo $images[0]['image']?>?rand=' + Math.random());

                <?php if (Mage::getStoreConfig('smdesign_colorswatch/general/update_more_view')) : ?>
                    $('.itemslider-thumbnails').removeData("flexslider");
                    $('.itemslider-thumbnails').html('<ul class="thumbnails slides"></ul>');
                    galleryView = $('.more-images ul');
                    galleryView.parent().attr('class', 'more-images itemslider itemslider-thumbnails gen-slider-arrows3 <?php if($sliderClasses) echo $sliderClasses; ?>');

                    <?php foreach ($images as $key=>$_image) : ?>
                        li = $('<li></li>');
                        var imgtxt = "<img src=\"<?php echo $_image['thumb']; ?>\" alt=\"<?php echo $_image['label'] ?>\">";
                        var atxt = "<a href=\"<?php echo $_image['big_image']; ?>\" class=\"cloud-zoom-gallery lightbox-group\" title=\"<?php echo Mage::helper('core')->escapeHtml($_image['label']) ?>\" rel=\"useZoom:'zoom1', smallImage: '<?php echo $_image['big_image']; ?>' \">"+imgtxt+"</a>";
                        li.append(atxt);
                        galleryView.append(li);
                    <?php endforeach; ?>
                    jQuery('.cloud-zoom').CloudZoom();
                <?php endif; ?>
                $('.itemslider-thumbnails').flexslider({
                    namespace: "",
                    animation: "slide",
                    easing: "easeInQuart",
                    animationSpeed: 300,
                    animationLoop: false,
                    slideshow: false,

                    pauseOnHover: true,
                    controlNav: false,

                    itemWidth: <?php echo $thumbTotalWidth; ?>,
                    move: 1
                });
            });

        <?php if ($helpZoom->useLightbox()): ?>
            <?php
            $maxWidth	= $helpZoom->getCfg('lightbox/max_width');
            $maxHeight	= $helpZoom->getCfg('lightbox/max_height');
            $cfg = '';
            if ($maxWidth)
                $cfg .= ", maxWidth:'{$maxWidth}'";
            if ($maxHeight)
                $cfg .= ", maxHeight:'{$maxHeight}'";
            ?>

            jQuery(function($) {
                $(".lightbox-group").colorbox({
                    <?php if ($helpZoom->getCfg('lightbox/group')): ?>
                        rel:		'lightbox-group',
                    <?php endif; ?>
                    opacity:	0.5,
                    speed:		300,
                    current:	'<?php echo $this->__('image {current} of {total}') ?>'
                    <?php if ($cfg) echo $cfg; ?>
                });

                //Product thumbnails
                <?php if ($helpZoom->getCfg('lightbox/group')): ?>
                    $(".cloud-zoom-gallery").first().removeClass("cboxElement");
                <?php endif; ?>

                $(".cloud-zoom-gallery").click(function() {
                    $("#zoom-btn").attr('href', $(this).attr('href'));
                    $("#zoom-btn").attr('title', $(this).attr('title'));

                    <?php if ($helpZoom->getCfg('lightbox/group')): //Erase class from clicked thumbnail ?>
                        $(".cloud-zoom-gallery").each(function() {
                            $(this).addClass("cboxElement");
                        });
                        $(this).removeClass("cboxElement");
                    <?php endif; ?>
                });

                jQuery('#zoom01, .cloud-zoom-gallery').CloudZoom();
            });
        <?php endif; ?>

    <?php }

    private function productExsist($products, $aCode, $oId) {
        foreach ($products as $key=>$product) {
            if ($product->getData($aCode) == $oId) {
                return true;
            }
        }
        return false;
    }

    public function getGalleryUrl($image = null, $_product)
    {
        $params = array('id' => $this->getProduct($_product)->getId());
        if ($image) {
            $params['image'] = $image['value_id'];
        }
        return Mage::getUrl('catalog/product/gallery', $params);
    }

    /**
     * Retrive product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct($product)
    {
        if (is_null($product->getTypeInstance(true)->getStoreFilter($product))) {
            $product->getTypeInstance(true)->setStoreFilter(Mage::app()->getStore(), $product);
        }

        return $product;
    }
}