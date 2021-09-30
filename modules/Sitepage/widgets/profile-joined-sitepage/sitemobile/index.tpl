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

<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1);?>
<?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
  <div id='profile_joined_pages'>
    <ul class="p_list_grid">
      <?php foreach ($this->paginator as $item): ?>
        <li style="height:<?php echo $this->columnHeight ?>px;">
          <a href="<?php echo $item->getHref(); ?>" class="ui-link-inherit">
            <div class="p_list_grid_top_sec">
              <div class="p_list_grid_img">
                <?php $url = $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/nophoto_page_thumb_profile.png';
                $temp_url = $item->getPhotoUrl('thumb.profile');
                if (!empty($temp_url)): $url = $item->getPhotoUrl('thumb.profile');
                  endif; ?>
                <span style="background-image: url(<?php echo $url; ?>);"> </span>
              </div>
              <div class="p_list_grid_title">
                <span><?php echo $this->string()->chunk($this->string()->truncate($item->getTitle(), 45), 10); ?></span>
              </div>
              <div class="list-label-wrap">
                <?php if ($item->sponsored == 1): ?>
                  <span class="list-label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.color', '#fc0505'); ?>;'>
                    <?php echo $this->translate('Sponsored'); ?>     				
                  </span>
                <?php endif; ?>
                <?php if ($item->featured == 1): ?>
                  <span class="list-label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.featured.color', '#0cf523'); ?>;'><?php echo $this->translate('Featured')?></span>
                <?php endif; ?>
              </div>
            </div>
          </a>
          <div class="p_list_grid_info p_list_grid_icon_right">
            <span class="p_list_grid_stats">
              <span class="fleft">            
                <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
              </span>
              <?php if($postedBy):?>
                <span class="fright">
                   <?php echo $this->translate('by ') . '<b>' .$this->htmlLink($item->getOwner()->getHref(), $this->string()->chunk($this->string()->truncate($item->getOwner()->getTitle(), 16), 10)) . '</b>'; ?>
                </span>
              <?php endif; ?>
            </span>
            <?php if ($this->ratngShow): ?>
              <span class="p_list_grid_stats">
                <?php if (($item->rating > 0)): ?>
                  <?php
                  $currentRatingValue = $item->rating;
                  $difference = $currentRatingValue - (int) $currentRatingValue;
                  if ($difference < .5) {
                    $finalRatingValue = (int) $currentRatingValue;
                  } else {
                    $finalRatingValue = (int) $currentRatingValue + .5;
                  }
                  ?>
                  <span title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                    <?php for ($x = 1; $x <= $item->rating; $x++): ?>
                      <span class="rating_star_generic rating_star" ></span>
                    <?php endfor; ?>
                    <?php if ((round($item->rating) - $item->rating) > 0): ?>
                      <span class="rating_star_generic rating_star_half" ></span>
                    <?php endif; ?>
                  </span>
                <?php endif; ?>
              </span>
            <?php endif; ?>

            <span class="p_list_grid_stats">
              <?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?> - 
              <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) : ?>
                <?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.member.title' , 1);
                if ($item->member_title && $memberTitle) : ?>
                      <?php echo $item->member_count . ' ' . $item->member_title; ?> -
                <?php else : ?>
                    <?php echo $this->translate(array('%s member', '%s members', $item->member_count), $this->locale()->toNumber($item->member_count)) ?> -
                <?php endif; ?>
              <?php endif; ?>
              <?php $sitepagereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview'); ?>
              <?php if ($sitepagereviewEnabled): ?>
                <?php echo $this->translate(array('%s review', '%s reviews', $item->review_count), $this->locale()->toNumber($item->review_count)) ?> - 
              <?php endif; ?>
              <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?> - 
              <?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>
            </span>
            <?php if (0 && !empty($item->page_owner_id)) : ?>
              <span class="p_list_grid_stats">
              <?php if ($this->viewer_id == $item->owner_id) : ?>
                <?php echo $this->translate("PAGEMEMBER_OWNER"); ?>
              <?php else: ?>
                <?php if(!empty($this->showMemberText)) : ?>
                  <?php if (empty($item->member_title)) : ?>
                    <strong><?php echo $this->translate("PAGEMEMBER_MEMBER"); ?></strong>
                  <?php else: ?>
                    <strong><?php echo $item->member_title; ?></strong>
                  <?php endif ?>
                <?php endif; ?>
              <?php endif; ?>
              </span>
            <?php endif; ?>
            
            <?php if(0 && !empty($item->page_owner_id) && ($this->viewer_id != $item->owner_id) && ($this->subject_id == $this->viewer_id)):?>
<!--              <a href="#user_profile_page_<?php echo $item->getGuid()?>" data-rel="popup" data-transition="pop" class="righticon ui-icon-ellipsis-vertical"></a>-->
            <?php endif; ?>
          </div>
            <?php if(0 && !empty($item->page_owner_id) && ($this->viewer_id != $item->owner_id) && ($this->subject_id == $this->viewer_id)):?>
<!--          <div data-role="popup" id="user_profile_page_<?php echo $item->getGuid()?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window">
            <div data-inset="true" style="min-width:150px;" class="sm-options-popup">
              <?php echo $this->htmlLink(array('route' => 'sitepage_profilepagemember', 'action' => 'leave', 'page_id' => $item->page_id), $this->translate('Leave Page'), array('class' => 'ui-btn-default smoothbox')); ?>
            </div>
          </div>-->
          <?php endif; ?> 
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php else :?>
  <div class="sm-content-list" id="profile_joined_pages">
    <ul data-role="listview" data-icon="arrow-r">
      <?php foreach ($this->paginator as $item): ?>
        <li <?php if(!empty($item->page_owner_id) && ($this->viewer_id != $item->owner_id) && ($this->subject_id == $this->viewer_id)):?> data-icon="cog" data-inset="true" <?php endif;?>>
          <a href="<?php echo $item->getHref(); ?>">
            <?php echo $this->itemPhoto($item, 'thumb.icon'); ?>
            <h3><?php echo $item->getTitle() ?></h3>
            <p>
              <?php if ($this->ratngShow): ?>
                <?php if (($item->rating > 0)): ?>
                  <?php
                  $currentRatingValue = $item->rating;
                  $difference = $currentRatingValue - (int) $currentRatingValue;
                  if ($difference < .5) {
                    $finalRatingValue = (int) $currentRatingValue;
                  } else {
                    $finalRatingValue = (int) $currentRatingValue + .5;
                  }
                  ?>
                  <span title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                    <?php for ($x = 1; $x <= $item->rating; $x++): ?>
                      <span class="rating_star_generic rating_star" ></span>
                    <?php endfor; ?>
                    <?php if ((round($item->rating) - $item->rating) > 0): ?>
                      <span class="rating_star_generic rating_star_half" ></span>
                    <?php endif; ?>
                  </span>
                <?php endif; ?>
              <?php endif; ?>
            </p>
            <p>
              <?php if($postedBy && $item->show_name):?><?php echo $this->translate('posted by'); ?>
                  <strong><?php echo $item->getOwner()->getTitle() ?></strong> - 
              <?php endif; ?>
            <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
            </p>
            <p class="ui-li-aside">
              <?php if ($item->closed): ?>
                <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/close.png', '', array('class' => 'icon', 'title' => $this->translate('Closed'))) ?>
              <?php endif; ?>
              <?php if ($item->sponsored == 1): ?>
                <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
              <?php endif; ?>
              <?php if ($item->featured == 1): ?>
                <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
              <?php endif; ?>
            </p>
            <p>
              <?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?> - 
              <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) : ?>
                <?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.member.title' , 1);
                if ($item->member_title && $memberTitle) : ?>
                      <?php echo $item->member_count . ' ' . $item->member_title; ?> -
                <?php else : ?>
                    <?php echo $this->translate(array('%s member', '%s members', $item->member_count), $this->locale()->toNumber($item->member_count)) ?> -
                <?php endif; ?>
              <?php endif; ?>
              <?php $sitepagereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview'); ?>
              <?php if ($sitepagereviewEnabled): ?>
                <?php echo $this->translate(array('%s review', '%s reviews', $item->review_count), $this->locale()->toNumber($item->review_count)) ?> - 
              <?php endif; ?>
              <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?> - 
              <?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>
            </p>
            <p>
              <?php if (0 && !empty($item->page_owner_id)) : ?>
                <?php if ($this->viewer_id == $item->owner_id) : ?>
                  <i class="icon_sitepages_page-owner"><?php echo $this->translate("PAGEMEMBER_OWNER"); ?></i>
                <?php else: ?>
                  <?php if(!empty($this->showMemberText)) : ?>
                    <i class="icon_sitepage_page-member">
                      <?php if (empty($item->member_title)) : ?>
                        <strong><?php echo $this->translate("PAGEMEMBER_MEMBER"); ?></strong>
                      <?php else: ?>
                        <strong><?php echo $item->member_title; ?></strong>
                      <?php endif ?>
                    </i>
                  <?php endif; ?>
                <?php endif; ?>
              <?php endif; ?>
              <?php if (0 && !empty($this->textShow)) : ?>
                <?php if (!empty($item->page_owner_id)) : ?>
                  <?php if ($this->viewer_id != $item->owner_id && empty($item->member_approval)) : ?>
                      <i class="icon_sitepage_verified_page"><?php echo $this->translate($this->textShow); ?></i>
                  <?php endif; ?>
                <?php endif; ?>
              <?php endif; ?>
            </p>
          </a> 
          <?php if(!empty($item->page_owner_id) && ($this->viewer_id != $item->owner_id) && ($this->subject_id == $this->viewer_id)):?>
            <a href="#user_profile_page_<?php echo $item->getGuid()?>" data-rel="popup"></a>
            <div data-role="popup" id="user_profile_page_<?php echo $item->getGuid()?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme'=>"c")); ?> data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window">
              <div data-inset="true" style="min-width:150px;" class="sm-options-popup">
                <?php echo $this->htmlLink(array('route' => 'sitepage_profilepagemember', 'action' => 'leave', 'page_id' => $item->page_id), $this->translate('Leave Page'), array('class' => 'ui-btn-default smoothbox')); ?>
                <a href="#" data-rel="back" class="ui-btn-default ui-btn-main">
                  <?php echo $this->translate('Cancel'); ?>
                </a>
              </div>
            </div>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
<?php if ($this->paginator->count() > 1): ?>
  <?php
  echo $this->paginationAjaxControl(
          $this->paginator, $this->identity, 'profile_joined_pages', array('textShow' => $this->textShow, 'category_id' => $this->category_id, 'showMemberText' => $this->showMemberText, 'pageAdminJoined' => $this->pageAdminJoined));
  ?>
<?php endif; ?>