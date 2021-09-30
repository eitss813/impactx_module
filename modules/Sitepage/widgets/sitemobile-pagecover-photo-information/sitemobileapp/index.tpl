<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
	var seaocore_content_type = '<?php echo $this->resource_type; ?>';
</script>

<div class="content_cover_wrapper">
  <?php if($this->can_edit):?>
		<a href="#icons_options_cover_photo" data-rel="popup" data-transition="pop">
  <?php endif;?>
    <div class='content_cover_photo_wrapper' id="siteuser_cover_photo" style='min-height:60px;'>
      <?php if ($this->photo) : ?>
        <div class="content_cover_photo">
          <?php if (empty($this->can_edit)): ?>
            <a href="<?php echo $this->photo->getHref(); ?>" class="thumbs_photo">
          <?php endif; ?>
            <?php echo $this->itemPhoto($this->photo, 'thumb.cover', '', array('align' => 'left', 'class' => 'cover_photo thumbs_photo')); ?>
          <?php if (empty($this->can_edit)) : ?>
            </a>
          <?php endif; ?>
          </div>
      <?php else: ?>
        <div class="content_cover_photo_empty ui-bar-c"></div>
      <?php endif; ?>
      <?php if($this->can_edit):?>  
        <div class="seaocore_profile_cover_upload_op"><span></span></div>
      <?php endif;?>
      <span class="content_cover_photo_cover"></span>
      
      <?php if(!empty($this->sitepage->sponsored) || !empty($this->sitepage->featured)):?>
        <div class="list-label-wrap">
          <?php if (in_array('sponsored', $this->showContent) && !empty($this->sitepage->sponsored)): ?>
            <span class="list-label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.color', '#fc0505'); ?>;'>
              <?php echo $this->translate('SPONSORED'); ?>
            </span>
          <?php endif; ?>
          
          <?php if (in_array('featured', $this->showContent) && !empty($this->sitepage->featured)): ?>
            <span class="list-label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.featured.color', '#0cf523'); ?>;'>
              <?php echo $this->translate('FEATURED'); ?>
            </span>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  <?php if($this->can_edit):?>
		</a>
  <?php endif;?>
  
  <?php if (in_array('mainPhoto', $this->showContent) || in_array('title', $this->showContent) || in_array('category', $this->showContent)): ?>
    <div class="content_cover_head">
      <?php if (in_array('mainPhoto', $this->showContent)): ?>
        <div class="content_cover_main_photo_wrapper">
          <div class='content_cover_main_photo'>
            <div class="item_photo <?php if($this->strachPhoto):?> show_photo_box <?php endif; ?>">
              <table border="0" cellpadding="0" cellspacing="0">
                <tr valign="middle">
                  <td>
                    <?php echo $this->itemPhoto($this->sitepage, 'thumb.profile', '', array('align' => 'left', 'id' => 'user_profile_photo')); ?>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      <?php endif;?>
        
      <?php if(in_array('badge', $this->showContent) && isset($this->sitepagebadges_value) && isset($this->sitepagebadge)):?>
        <?php if (empty($this->sitepagebadges_value) || $this->sitepagebadges_value == 2): ?>
          <?php //$badgeTitle = $this->sitepagebadge->title; ?>
        <?php endif; ?>

        <?php if ($this->sitepagebadges_value == 1 || $this->sitepagebadges_value == 2): ?>
          <?php if (!empty($this->sitepagebadge->badge_main_id)) :?>
            <?php $main_path = Engine_Api::_()->storage()->get($this->sitepagebadge->badge_main_id, '')->getPhotoUrl(); ?>
            <?php if(!empty($main_path)) :?>
            <a data-transition="pop" href="#profile_badges_<?php echo $this->sitepagebadge->badge_main_id?>" data-rel="popup"><img src="<?php echo $main_path ?>" class="badge-img" /></a>
            <?php endif;?>
          <?php endif; ?>
        <?php endif; ?>
      <?php endif; ?>
        
      <?php if (in_array('title', $this->showContent) || in_array('category', $this->showContent) || in_array('subcategory', $this->showContent) || in_array('subsubcategory', $this->showContent) || in_array('badge', $this->showContent)): ?>
        <div class="content_cover_title <?php if (!in_array('mainPhoto', $this->showContent)): ?>seaocore_profile_photo_none<?php endif; ?>">
          <?php if(in_array('title', $this->showContent)):?>
            <a href="<?php echo $this->sitepage->getHref(); ?>"><h2><?php echo $this->sitepage->getTitle(); ?></h2></a>
          <?php endif;?>
          
          <div class="f_small">
						<?php if(in_array('category', $this->showContent) && $this->sitepage->category_id):?>
							<?php echo $this->htmlLink($this->url(array('category_id' => $this->sitepage->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->category_name)), 'sitepage_general_category'), $this->translate($this->category_name)) ?>
						<?php endif;?>
						<?php if(in_array('subcategory', $this->showContent) && $this->sitepage->subcategory_id && isset(Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($this->sitepage->subcategory_id)->category_name)):?>
              <?php echo '&raquo;';?>  
							<?php echo $this->htmlLink($this->url(array('category_id' => $this->sitepage->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->category_name), 'subcategory_id' => $this->sitepage->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->subcategory_name)), 'sitepage_general_subcategory'), $this->translate($this->subcategory_name)) ?>
						<?php endif;?>
						<?php if(in_array('subsubcategory', $this->showContent) && $this->sitepage->subsubcategory_id  && isset(Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($this->sitepage->subsubcategory_id)->category_name)):?>
              <?php echo '&raquo;';?> 
							<?php echo $this->htmlLink($this->url(array('category_id' => $this->sitepage->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->category_name), 'subcategory_id' => $this->sitepage->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->subcategory_name),'subsubcategory_id' => $this->sitepage->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($this->subsubcategory_name)), 'sitepage_general_subsubcategory'),$this->translate($this->subsubcategory_name)) ?>
						<?php endif;?>
					</div>
        </div>
      <?php endif;?>
    </div>
  <?php endif;?>
  <div class="content_cover_info o_hidden <?php if (!in_array('mainPhoto', $this->showContent)): ?>seaocore_profile_photo_none<?php endif; ?>">
    
    <?php if ($this->sitepage->price > 0 && in_array('price', $this->showContent)): ?>
      <p class="fright f_small">
        <?php //echo $this->translate('Price'); ?>
        <b>
          <?php echo Engine_Api::_()->sitepage()->getPriceWithCurrency($this->sitepage->price);?>
        </b>  
      </p>  
    <?php endif; ?> 
      
    <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')):?>
      <?php if (($this->sitepage->rating > 0)): ?>
        <?php
        $currentRatingValue = $this->sitepage->rating;
        $difference = $currentRatingValue - (int) $currentRatingValue;
        if ($difference < .5) {
          $finalRatingValue = (int) $currentRatingValue;
        } else {
          $finalRatingValue = (int) $currentRatingValue + .5;
        }
        ?>
        <p>  
          <span class="list_rating_star" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
            <?php for ($x = 1; $x <= $this->sitepage->rating; $x++): ?>
              <span class="rating_star_generic rating_star" ></span>
            <?php endfor; ?>
            <?php if ((round($this->sitepage->rating) - $this->sitepage->rating) > 0): ?>
              <span class="rating_star_generic rating_star_half" ></span>
            <?php endif; ?>
          </span>
        </p>
      <?php endif; ?>
    <?php endif; ?>
    
    
    <?php if (!empty($this->sitepage->location) && in_array('location', $this->showContent)):?>
      <p class="t_light f_small">
        <i class="ui-icon-map-marker"></i>
        <?php //echo $this->translate("Location")?>
        <?php echo $this->htmlLink('https://maps.google.com/?q='.urlencode($this->sitepage->location), $this->sitepage->location, array('target' => 'blank')) ?> 
      </p>
    <?php endif;?>
  </div>
 
    <div class="seaocore_cover_cont" id="siteuser_main_photo">
  
    <?php if (in_array('likeButton', $this->showContent) || in_array('followButton', $this->showContent) || in_array('joinButton', $this->showContent) || in_array('addButton', $this->showContent) || in_array('leaveButton', $this->showContent)): ?>
			<div class="seaocore_profile_cover_buttons">
				<table cellpadding="1" cellspacing="0">
					<tr>
            <?php if (in_array('likeButton', $this->showContent)):?>
							<td id="seaocore_like">
								<?php if(!empty($this->viewer_id)): ?>
									<?php $hasLike = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($this->resource_type, $this->resource_id); ?>
									<a href ="javascript://" onclick = "seaocore_content_type_likes_sitemobile('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="<?php echo $this->resource_type; ?>_unlikes_<?php echo $this->resource_id;?>" style ='display:<?php echo $hasLike ?"block":"none"?>'>
										<i class="ui-icon ui-icon-thumbs-up-alt feed-unlike-btn"></i>
										<span><?php echo $this->translate('Like') ?></span>
									</a>
									<a href = "javascript://" onclick = "seaocore_content_type_likes_sitemobile('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="<?php echo $this->resource_type; ?>_most_likes_<?php echo $this->resource_id;?>" style ='display:<?php echo empty($hasLike) ?"block":"none"?>'>
										<i class="ui-icon ui-icon-thumbs-up-alt feed-like-btn"></i>
										<span><?php echo $this->translate('Like') ?></span>
									</a>
									<input type ="hidden" id = "<?php echo $this->resource_type; ?>_like_<?php echo $this->resource_id;?>" value = '<?php echo $hasLike ? $hasLike[0]['like_id'] :0; ?>' />
								<?php endif; ?>
							</td>
            <?php endif;?>
            <?php if (in_array('followButton', $this->showContent)):?>
							<?php if($this->viewer_id != $this->subject()->getOwner()->getIdentity()):?>
								<td id="seaocore_follow">
									<?php if ($this->viewer_id): ?>
										<?php $isFollow = $this->sitepage->follows()->isFollow($this->viewer); ?>
											<a href="javascript://" onclick = "seaocore_resource_type_follows_sitemobile('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="<?php echo $this->resource_type ?>_unfollows_<?php echo $this->resource_id;?>" style =' display:<?php echo $isFollow ?"block":"none"?>'>
                        <i class="ui-icon-delete"></i>
												<span><?php echo $this->translate('Unfollow') ?></span>
											</a>
											<a href="javascript://" onclick = "seaocore_resource_type_follows_sitemobile('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' id="<?php echo $this->resource_type ?>_most_follows_<?php echo $this->resource_id;?>" style ='display:<?php echo empty($isFollow) ?"block":"none"?>'>
                        <i class="ui-icon-plus"></i>
												<span><?php echo $this->translate('Follow') ?></span>
											</a>
											<input type ="hidden" id = "<?php echo $this->resource_type; ?>_follow_<?php echo $this->resource_id;?>" value = '<?php echo $isFollow ? $isFollow :0; ?>' />
									<?php endif; ?>
								</td>
							<?php endif; ?>
            <?php endif;?>
                
             <!--JOIN /LEAVE BUTTON WILL BE SHOWN ONLY IF SITEPAGE MEMBER EXIST HERE OTHERWISE DISPLAY MESSAGE & SHARE BUTTON-->   
             <?php $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');?>
             <?php if(!empty($moduleEnabled)):?>
              <?php if (in_array('joinButton', $this->showContent)):?>
								<?php $joinMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($this->viewer_id, $this->sitepage->page_id);
								if (empty($joinMembers) && $this->viewer_id != $this->sitepage->owner_id && !empty($this->allowPage)): ?>
									<?php if (!empty($this->viewer_id)) : ?>
										<?php if (!empty($this->sitepage->member_approval)): ?>
											<td>
												<a href="<?php echo $this->escape($this->url(array( 'action' => 'join', 'page_id' => $this->sitepage->page_id), 'sitepage_profilepagemember', true)); ?>" class="smoothbox" data-role='button' data-icon='false' data-inset='false' data-mini='true' data-corners='false' data-shadow='true'>
                        <i class="ui-icon-ok"></i>
												<span><?php echo $this->translate("Join Page"); ?></span>
												</a>
											</td>
										<?php else: ?>
											<td>
												<a href='<?php echo $this->escape($this->url(array( 'action' => 'request', 'page_id' => $this->sitepage->page_id), 'sitepage_profilepagemember', true)); ?>' class="smoothbox" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true'>
                          <i class="ui-icon-plus"></i>
                          <span><?php echo $this->translate("Join Page"); ?></span>
                        </a>
											</td>
										<?php endif; ?>
									<?php endif; ?>
								<?php endif; ?>
								<?php $joinMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($this->viewer_id, $this->sitepage->page_id, 'Cancel');
								if (!empty($joinMembers) && $this->viewer_id != $this->sitepage->owner_id && !empty($this->allowPage)): ?>
									<td>
										<a href="<?php echo $this->escape($this->url(array( 'action' => 'cancel', 'page_id' => $this->sitepage->page_id), 'sitepage_profilepagemember', true)); ?>" class="smoothbox" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true'>
                      <i class="ui-icon-delete"></i>
                      <span><?php echo $this->translate("Cancel Membership Request"); ?></span>
										</a>
									</td>
								<?php endif;?>
              <?php endif; ?>
							<?php $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($this->viewer_id, $this->sitepage->page_id, $params = "Leave");
								if (!empty($hasMembers) && in_array('leaveButton', $this->showContent) && $this->viewer_id != $this->sitepage->owner_id && Engine_Api::_()->sitepage()->allowInThisPage($this->sitepage, "sitepagemember", 'smecreate')): ?>
									<td>
										<?php if ($this->viewer_id) : ?>
											<a href="<?php echo $this->escape($this->url(array( 'action' => 'leave', 'page_id' => $this->sitepage->page_id), 'sitepage_profilepagemember', true)); ?>" class="smoothbox" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true'>
                        <i class="ui-icon-minus"></i>
                        <span><?php echo $this->translate("Leave Page"); ?></span>
                      </a>
										<?php endif; ?>
									</td>
							<?php endif; ?>

                  <?php if (in_array('addButton', $this->showContent)):?>
								<?php $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($this->viewer_id, $this->sitepage->page_id, $params = 'Invite'); ?>
								<?php if (!empty($hasMembers) && !empty($this->can_edit)) : ?>
									<td>
										<a data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' class="smoothbox" href="<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'page_id' => $this->sitepage->page_id), 'sitepage_profilepagemember', true)); ?>">
                       <i class="ui-icon-plus"></i>
                      <span><?php echo $this->translate("Add People"); ?></span>
                    </a>	
									</td>
									<?php elseif (!empty($hasMembers) && empty($this->sitepage->member_invite)): ?>
									<td>
										<a data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' class="smoothbox" href='<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'page_id' => $this->sitepage->page_id), 'sitepage_profilepagemember', true)); ?>'>
                       <i class="ui-icon-plus"></i>
                       <span><?php echo $this->translate("Add People"); ?></span>
                    </a>
									</td>
								<?php endif; ?>
              <?php endif; ?>
                  <?php else:?>
                  <?php if($this->viewer->getIdentity() && $this->can_share):?>
                  <td>                    
										<a data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' class="smoothbox" href="<?php echo $this->escape($this->url( array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $this->resource_type, 'id' => $this->resource_id, 'format' => 'smoothbox'), 'default', true)); ?>">
                       <i class="ui-icon-share-alt"></i>
                      <span><?php echo $this->translate("share"); ?></span>
                    </a>	
									</td>
                  <?php endif; ?>
                  <?php if ($this->sitepage->owner_id != $this->viewer_id && $this->viewer_id && $this->showMessageOwner != 'none'):?>
                  <td>
                  <a data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' class="smoothbox" href="<?php echo $this->escape($this->url( array('action' => 'message-owner', 'page_id' => $this->resource_id, 'format' => 'smoothbox'), 'sitepage_profilepage', true)); ?>">
                       <i class="ui-icon-envelope-alt"></i>
                      <span><?php echo $this->translate("Message"); ?></span>
                    </a>
                    </td>
                  <?php endif; ?>
                  <?php endif; ?>                
					</tr>
				</table>  
			</div>
    <?php endif;?>
 
    <?php if(in_array('description', $this->showContent) || in_array('phone', $this->showContent) || in_array('website', $this->showContent) || in_array('email', $this->showContent)):?>
      <div class="ui-page-content sm-widget-block content_cover_profile_fields">
        <h4><?php echo $this->translate('Details'); ?></h4>
        <ul>
          <?php if(in_array('description', $this->showContent)):?>
            <?php if(isset(Engine_Api::_()->getDbtable('writes', 'sitepage')->writeContent($this->sitepage->page_id)->text)):?>
              <li>
                <?php echo $this->viewMore(htmlspecialchars_decode(nl2br(Engine_Api::_()->getDbtable('writes', 'sitepage')->writeContent($this->sitepage->page_id)->text), ENT_QUOTES), 200) ?>
              </li>
            <?php else:?>
              <li>
                <?php echo $this->viewMore($this->sitepage->body, 200) ?>
              </li>
            <?php endif;?>
          <?php endif;?>  
          <?php if (in_array('tags', $this->showContent) && count($this->sitepageTags) > 0): $tagCount = 0; ?>
            <li>
              <span class="t_light"><?php echo $this->translate('Tags'); ?>:</span>
              <span><?php foreach ($this->sitepageTags as $tag): ?>
									<?php if (!empty($tag->getTag()->text)): ?>
										<?php if (empty($tagCount)): ?>
											<a href='<?php echo $this->url(array('action' => 'index', 'tag' => $tag->getTag()->tag_id, 'tag_name' => Engine_Api::_()->seaocore()->getSlug($tag->getTag()->text, 225)), "sitepage_general"); ?>'>#<?php echo $tag->getTag()->text ?></a>
											<?php $tagCount++;
										else: ?>
											<a href='<?php echo $this->url(array('action' => 'index', 'tag' => $tag->getTag()->tag_id, 'tag_name' => Engine_Api::_()->seaocore()->getSlug($tag->getTag()->text, 225)), "sitepage_general"); ?>'>#<?php echo $tag->getTag()->text ?></a>
										<?php endif; ?>
									<?php endif; ?>
								<?php endforeach; ?></span>
            </li>
          <?php endif; ?>
        </ul>
        <?php if(($this->sitepage->phone || $this->sitepage->email || $this->sitepage->website) && in_array('phone', $this->showContent) || in_array('website', $this->showContent) || in_array('email', $this->showContent)):?>
          <?php if(($this->sitepage->phone || $this->sitepage->email || $this->sitepage->website)):?>
            <h4>
              <span><?php echo $this->translate("Contact Information");?></span>
            </h4>
            <ul>
              <?php if(in_array('phone', $this->showContent) &&  $this->sitepage->phone):?>
                <li>
                  <span class="t_light"><?php echo $this->translate("Phone")?>:</span>
                  <span> <a href="tel:<?php echo $this->sitepage->phone?>"> <?php echo $this->sitepage->phone?> </a></span>
                </li>
              <?php endif;?>
              <?php if(in_array('email', $this->showContent) &&  $this->sitepage->email):?>
                <li>
                  <span class="t_light"><?php echo $this->translate("Email")?>:</span>
                  <span> <a href='mailto:<?php echo $this->sitepage->email ?>'><?php echo $this->translate('Email Me') ?></a> </span>
                </li>
              <?php endif;?>
              <?php if(in_array('website', $this->showContent) &&  $this->sitepage->email):?>
                <li>
                  <span class="t_light"><?php echo $this->translate("Website")?>:</span>
                  <?php if (strstr($this->sitepage->website, 'http://') || strstr($this->sitepage->website, 'https://')): ?>
                  <span> <a href='<?php echo $this->sitepage->website ?>' target="_blank" title='<?php echo $this->sitepage->website ?>' ><?php echo $this->translate(''); ?> <?php echo $this->translate('Visit Website') ?></a> </span>
                  <?php else: ?>
                  <span> 	<a href='http://<?php echo $this->sitepage->website ?>' target="_blank" title='<?php echo $this->sitepage->website ?>' ><?php echo $this->translate(''); ?> <?php echo $this->translate('Visit Website') ?></a> </span>
                  <?php endif; ?>
                </li>
              <?php endif;?>
            </ul>	
          <?php endif;?>
        <?php endif;?>
      </div>
    <?php endif;?>	
  </div>
</div>

<div data-role="popup" id="icons_options_cover_photo" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15" data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window" >
	<div data-inset="true" style="min-width:150px;" class="sm-options-popup">
    <?php if ($this->photo): ?>
      <a href="<?php echo $this->photo->getHref();?>" data-linktype='photo-gallery' class="ui-btn-default"><?php echo $this->translate("View Photo");?></a>
    <?php endif;?>
		<form id="upload_cover_photo_mobile" enctype="multipart/form-data" method="post">
      <label for="Filedata" data-mini="true" class="ui-btn-default"><?php echo $this->translate("Upload Cover Photo");?></label>
			<input id="MAX_FILE_SIZE" type="hidden" value="1073741824" name="MAX_FILE_SIZE">
			<input id="Filedata" type="file" onchange="uploadCoverPhoto();" name="Filedata" class="ui-btn-default">
		</form>
		<?php echo $this->htmlLink($this->url(array('action' => 'get-albums-photos', 'page_id' => $this->sitepage->page_id, 'recent' => 1), 'sitepage_profilepagemobile', true), $this->translate('Choose from Album Photos'), array(' class' => 'ui-btn-default')); ?>
		<?php if (!empty($this->sitepage->page_cover) && $this->photo) : ?>
			<?php echo $this->htmlLink(array('route' => 'sitepage_profilepagemobile', 'action' => 'remove-cover-photo', 'page_id' => $this->sitepage->page_id), $this->translate('Remove Cover Photo'), array(' class' => 'smoothbox ui-btn-default ui-btn-danger')); ?>
		<?php endif; ?>
		<a href="#" data-rel="back" class="ui-btn-default">
			<?php echo $this->translate('Cancel'); ?>
		</a>
	</div>
</div>

<?php if(in_array('badge', $this->showContent) && isset($this->sitepagebadges_value) && isset($this->sitepagebadge)):?>
	<div data-role="popup" id="profile_badges_<?php echo $this->sitepagebadge->badge_main_id;?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window">
		<div data-inset="true" style="min-width:150px;max-width:400px" class="sm-options-popup">
			<?php if (empty($this->sitepagebadges_value) || $this->sitepagebadges_value == 2): ?>
					<h3><?php echo $this->sitepagebadge->title; ?></h3>
				<?php endif; ?>
				<?php if ($this->sitepagebadges_value == 1 || $this->sitepagebadges_value == 2): ?>
					<?php
					if (!empty($this->sitepagebadge->badge_main_id)) {
						$main_path = Engine_Api::_()->storage()->get($this->sitepagebadge->badge_main_id, '')->getPhotoUrl();
						if (!empty($main_path)) {
            echo '<img src="' . $main_path . '" style="max-width:90%;" />';
						}
					}
					?>
				<?php endif; ?>
				<?php if (!empty($this->sitepagebadge->description)): ?>
        <div class="t_l"><?php echo $this->sitepagebadge->description ?></div>
				<?php endif; ?>
				<a href="#" data-rel="back" class="ui-btn-default" data-role="button">
					<?php echo $this->translate('Close'); ?>
				</a>
		</div>
	</div>
<?php endif;?>

<script type="text/javascript">
  
  function uploadCoverPhoto() {
    $('#upload_cover_photo_mobile').attr("action", "<?php echo $this->url(array('action' => 'upload-cover-photo', 'page_id' => $this->sitepage->page_id, 'special' => 'cover'), 'sitepage_profilepagemobile', true); ?>");
    $('#upload_cover_photo_mobile').submit();
  }

</script>

<script type="text/javascript">

 	sm4.core.runonce.add(function() {
    setTimeout(function(){
			var imageMaxHeight =$('body').width();
			if(imageMaxHeight > 500){
				imageMaxHeight = 500;
			}
			var imageHeight= $.mobile.activePage.find('.seaocore_profile_cover_photo_wrapper').find('.seaocore_profile_cover_photo').find('.cover_photo').height();
			if(imageHeight > imageMaxHeight){
				$.mobile.activePage.find('.seaocore_profile_cover_photo_wrapper').find('.seaocore_profile_cover_photo').css('max-height', imageMaxHeight);$.mobile.activePage.find('.seaocore_profile_cover_photo_wrapper').find('.seaocore_profile_cover_photo').find('.cover_photo').css('top',-(imageHeight -imageMaxHeight)/2);
			}
    },100);
 	});

  sm4.core.runonce.add(function() { 
    if (DetectAllWindowsMobile()) {
      $.mobile.activePage.find('#upload_cover_photo_mobile').css('display', 'none');
    } else {
      $.mobile.activePage.find('#upload_cover_photo_mobile').css('display', 'block');
    } 
  });

</script>