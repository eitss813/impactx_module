<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>
<?php 
	$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl
  	              . 'application/modules/Seaocore/externals/styles/styles.css');
  	              
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<script type="text/javascript">

	function showPagePhoto(ImagePath, category_id, page_id) {
    var elem = document.getElementById('page_elements_'+category_id).getElementsByTagName('a'); 
    for(var i = 0; i < elem.length; i++)
    { 
			var cat_pageid = elem[i].id;
			$(cat_pageid).erase('class');
		}
    $('page_link_class_'+page_id).set('class', 'active');
    
		$('pageImage_'+category_id).src = ImagePath;
	}

</script>

<ul class="seaocore_categories_box">
  <li> 
    <?php $ceil_count = 0; $k = 0; ?>
    <?php for ($i = 0; $i <= count($this->categories); $i++) { ?>
			<?php if($ceil_count == 0) :?>      
				<div>      
			<?php endif;?>  
          <div class="seaocore_categories_list_row" >
				<?php $ceil_count++;?>				
				<?php $category = "";
					if (isset($this->categories[$k]) && !empty($this->categories[$k])): 
						$category = $this->categories[$k];
					endif;
					$k++;

					if (empty($category)) {
						break;
					}
				?>

				<div class="seaocore_categories_list">
					<?php $total_subcat = Count($category['category_pages']); ?>
					<h6>
						<?php echo $this->htmlLink($this->url(array('category_id' => $category['category_id'], 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($category['category_name'])), 'sitepage_general_category'), $this->translate($category['category_name'])) ?>
					</h6>	
					<div class="sub_cat" id="subcat_<?php echo $category['category_id'] ?>">

						<?php $total_count = 1; ?>
		
						<?php foreach ($category['category_pages'] as $categoryPages) : ?>

							<?php 
								$imageSrc = $categoryPages['imageSrc']; 
								if(empty($imageSrc)) {
									$imageSrc = $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/nophoto_sitepage_thumb_icon.png';
								}

								$category_id = $category['category_id'];
								$page_id = $categoryPages['page_id'];
							?>
							<?php if($total_count == 1): ?>
								<div class="seaocore_categories_img" >
									<img src="<?php echo $imageSrc; ?>" id="pageImage_<?php echo $category['category_id'] ?>" alt="" class="thumb_icon" />
								</div>
								<div id='page_elements_<?php echo $category_id;?>'>
								<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($categoryPages['page_id'], $categoryPages['owner_id'], $categoryPages['slug']), Engine_Api::_()->sitepage()->truncation($categoryPages['page_title'], 25)." (".$categoryPages['populirityCount'].")", array('onmouseover' => "javascript:showPagePhoto('$imageSrc', '$category_id', '$page_id');",'title' => $categoryPages['page_title'], 'class'=>'active', 'id'=>"page_link_class_$page_id"));?>
							<?php else: ?>
								<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($categoryPages['page_id'], $categoryPages['owner_id'], $categoryPages['slug']), Engine_Api::_()->sitepage()->truncation($categoryPages['page_title'], 25)." (".$categoryPages['populirityCount'].")", array('onmouseover' => "javascript:showPagePhoto('$imageSrc', '$category_id', '$page_id');",'title' => $categoryPages['page_title'], 'id'=>"page_link_class_$page_id"));?>
							<?php endif; ?>

							<?php $total_count++; ?>
            <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
     <?php if($ceil_count % $this->columnCount == 0) :?>      
     </div>
     <?php $ceil_count=0; ?>
     <?php endif;?>
    <?php } ?> 
  </li>	
</ul>