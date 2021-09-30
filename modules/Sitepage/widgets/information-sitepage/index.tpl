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
<script type="text/javascript">
  var tagAction =function(tag) 
  {
    $('tag').value = tag;
    $('filter_form').submit();
  }
</script>

<ul class="sitepage_sidebar_info">
  <form id='filter_form' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'index'), "sitepage_general", true) ?>' style='display: none;'>
    <input type="hidden" id="tag" name="tag" value=""/>
    <input type="hidden" id="category" name="category" value=""/>
    <input type="hidden" id="subcategory" name="subcategory" value=""/>
    <input type="hidden" id="subsubcategory" name="subsubcategory" value=""/>
    <input type="hidden" id="categoryname" name="categoryname" value=""/>	
    <input type="hidden" id="subcategoryname" name="subcategoryname" value=""/>
    <input type="hidden" id="subsubcategoryname" name="subsubcategoryname" value=""/>
    <input type="hidden" id="start_date" name="start_date" value="<?php if ($this->start_date)
  echo $this->start_date; ?>"/>
    <input type="hidden" id="end_date" name="end_date" value="<?php if ($this->end_date)
             echo $this->end_date; ?>"/>
  </form>

  <?php if(is_array($this->showContent) && (in_array('ownerPhoto', $this->showContent) || in_array('ownerName', $this->showContent))):?>
		<li>
			<?php if(in_array('ownerPhoto', $this->showContent)):?>
				<?php echo $this->htmlLink($this->sitepage->getParent(), $this->itemPhoto($this->sitepage->getParent(), 'thumb.icon', '' , array('align' => 'center')), array('class'=> 'fleft sitepage_sidebar_info_photo')) ?>
			<?php endif ;?>
			<?php if(in_array('ownerName', $this->showContent) && $this->sitepage->show_name) :?>
				<div class="o_hidden">
					<?php echo $this->htmlLink($this->sitepage->getParent(), $this->sitepage->getParent()->getTitle()) ?><br /><?php echo $this->translate("(Owner)"); ?>
				</div>
			<?php endif ;?>  
		</li>
  <?php endif ;?>

  <?php if(is_array($this->showContent) && in_array('categoryName', $this->showContent)):?>
		<li>
			<?php if ($this->category_name != '' && $this->subcategory_name == '') : ?>
				<?php echo $this->translate('Category:'); ?>
				<?php echo $this->htmlLink($this->url(array('category_id' => $this->sitepage->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->category_name)), 'sitepage_general_category'), $this->translate($this->category_name)) ?>
			<?php elseif ($this->category_name != '' && $this->subcategory_name != ''): ?> 
				<?php echo $this->translate('Category:'); ?>
				<?php echo $this->htmlLink($this->url(array('category_id' => $this->sitepage->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->category_name)), 'sitepage_general_category'), $this->translate($this->category_name)) ?>
				<?php if (!empty($this->category_name)): echo '&raquo;';endif;
          echo $this->htmlLink($this->url(array('category_id' => $this->sitepage->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->category_name), 'subcategory_id' => $this->sitepage->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->subcategory_name)), 'sitepage_general_subcategory'), $this->translate($this->subcategory_name)) ?>
				<?php if(!empty($this->subsubcategory_name)): echo '&raquo;';
					echo $this->htmlLink($this->url(array('category_id' => $this->sitepage->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->category_name), 'subcategory_id' => $this->sitepage->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->subcategory_name),'subsubcategory_id' => $this->sitepage->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->subsubcategory_name)), 'sitepage_general_subsubcategory'),$this->translate($this->subsubcategory_name)) ?>
				<?php endif; ?>
				<?php endif; ?>
		</li>
  <?php endif ;?>

  <?php if (is_array($this->showContent) &&  in_array('tags', $this->showContent) && count($this->sitepageTags) > 0): $tagCount = 0; ?>
    <li>
      <?php echo $this->translate('Tags:'); ?>
      <?php foreach ($this->sitepageTags as $tag): ?>
        <?php if (!empty($tag->getTag()->text)): ?>
          <?php if (empty($tagCount)): ?>
            <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text ?></a>
            <?php $tagCount++;
          else: ?>
            <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text ?></a>
          <?php endif; ?>
      <?php endif; ?>
    <?php endforeach; ?>
    </li>
  <?php endif; ?>

  <li>
    <ul>
    
      <?php if (is_array($this->showContent) && in_array('modifiedDate', $this->showContent)):?>
				<li>
					<?php echo $this->translate('Last updated %s', $this->timestamp($this->sitepage->modified_date)) ?>
				</li>       
      <?php endif;?>

      <?php 

        $statistics = '';

        if(is_array($this->showContent) &&  in_array('commentCount', $this->showContent)) {
          $statistics .= $this->translate(array('%s comment', '%s comments', $this->sitepage->comment_count), $this->locale()->toNumber($this->sitepage->comment_count)).', ';
        }

        if(is_array($this->showContent) && in_array('viewCount', $this->showContent)) {
          $statistics .= $this->translate(array('%s view', '%s views', $this->sitepage->view_count), $this->locale()->toNumber($this->sitepage->view_count)).', ';
        }

        if(is_array($this->showContent) &&  in_array('likeCount', $this->showContent)) {
          $statistics .= $this->translate(array('%s like', '%s likes', $this->sitepage->like_count), $this->locale()->toNumber($this->sitepage->like_count)).', ';
        }                 

        if(is_array($this->showContent) && in_array('followerCount', $this->showContent) &&  isset($this->sitepage->follow_count)) {
          $statistics .= $this->translate(array('%s follower', '%s followers', $this->sitepage->follow_count), $this->locale()->toNumber($this->sitepage->follow_count)).', ';
        }       

        if(is_array($this->showContent) && in_array('memberCount', $this->showContent) && isset($this->sitepage->member_count)) {
				 $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.member.title' , 1);
			   if ($this->sitepage->member_title && $memberTitle) : 
				 $statistics .=  $this->sitepage->member_count . ' ' .  $this->sitepage->member_title.', ';
				 else : 
					$statistics .= $this->translate(array('%s member', '%s members', $this->sitepage->member_count), $this->locale()->toNumber($this->sitepage->member_count)).', ';
				 endif;
        }       

        $statistics = trim($statistics);
        $statistics = rtrim($statistics, ',');

      ?>

      <li><?php echo $statistics; ?></li>
    </ul>
  </li>

  <?php if (is_array($this->showContent) && $this->sitepage->price > 0 && in_array('price', $this->showContent)): ?>
    <li>
    	<b>
      	<?php echo Engine_Api::_()->sitepage()->getPriceWithCurrency($this->sitepage->price); ?>
      </b>	
    </li>   
  <?php endif; ?>  
  
  <?php if (is_array($this->showContent) && in_array('location', $this->showContent) && !empty($this->sitepage->location)): ?>
    <li>
      <?php echo $this->translate($this->sitepage->location); ?>&nbsp;-
      <b>
        <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $this->sitepage->page_id, 'resouce_type' => 'sitepage_page'), $this->translate("Get Directions"), array('class' => 'smoothbox')); ?>
      </b>
    </li>
  <?php endif; ?> 
    
</ul>