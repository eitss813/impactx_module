<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemusic
 * @copyright  Copyright 2017-2022 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _albumGridView.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if( 0 != count($this->listings) ): ?>
  <?php if( $this->isCarousel ): ?>
    <div class="owl-carousel owl-theme <?php echo $this->carouselClass ?>">
    <?php endif; ?>
    <?php foreach( $this->listings as $page ): ?>
      <div style="height: <?php echo ($this->blockHeight) ?>px;width : <?php echo ($this->blockWidth) ?>px;" class="sitepage_owl">
        <?php
        echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($page->page_id, $page->owner_id, $page->getSlug()), $this->itemPhoto($page, 'thumb.profile'));
        ?>
        <div class="sitepage_owl_decs">
          <div class="sitepage_carousel_title">
            <h5><?php echo $this->htmlLink($page->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($page->getTitle(), $this->title_truncation), array('title' => $page->getTitle())) ?></h5>
          </div>
          <?php if ($this->statistics): ?>
            <?php if(in_array('likeCount', $this->statistics) || in_array('followCount', $this->statistics)) : ?>
          <div class="sitepage_txt_light">
            <?php if(in_array('likeCount', $this->statistics)): ?>
              <?php echo $this->translate(array('%s like', '%s likes', $page->like_count), $this->locale()->toNumber($page->like_count)) ?>
            <?php endif; ?>
            <?php if(in_array('likeCount', $this->statistics) && in_array('followCount', $this->statistics)) : ?> , <?php endif; ?>
            <?php if(in_array('followCount', $this->statistics)): ?>
              <?php echo $this->translate(array('%s follower', '%s followers', $page->follow_count), $this->locale()->toNumber($page->follow_count)) ?>
            <?php endif; ?>
          </div>
          <?php endif; ?>
          <?php if(in_array('viewCount', $this->statistics) || in_array('memberCount', $this->statistics)) : ?>
          <div class="sitepage_txt_light">
            <?php if(in_array('viewCount', $this->statistics)): ?>
              <?php echo $this->translate(array('%s view', '%s views', $page->view_count), $this->locale()->toNumber($page->view_count)) ?>
            <?php endif; ?>
            <?php if(in_array('viewCount', $this->statistics) && in_array('memberCount', $this->statistics)) : ?>  , <?php endif; ?>
            <?php if(in_array('memberCount', $this->statistics)): ?>
              <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')): ?>
              <?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.member.title' , 1);
              if ($page->member_title && $memberTitle) : ?>
                <?php echo $page->member_count . ' ' .  $page->member_title; ?>
                <?php else : ?>
                  <?php echo $this->translate(array('%s member', '%s members', $page->member_count), $this->locale()->toNumber($page->member_count)) ?>
                <?php endif; ?>
              <?php endif; ?>
            <?php endif; ?>
          </div>
          <?php endif; ?>
          <?php if(in_array('commentCount', $this->statistics) || in_array('reviewCount', $this->statistics)) : ?>
          <div class="sitepage_txt_light">
            <?php if(in_array('commentCount', $this->statistics)): ?>
              <?php echo $this->translate(array('%s comment', '%s comments', $page->comment_count), $this->locale()->toNumber($page->comment_count)) ?>
            <?php endif; ?>
            <?php if(in_array('commentCount', $this->statistics) && in_array('reviewCount', $this->statistics)) : ?> , <?php endif; ?>
            <?php if(in_array('reviewCount', $this->statistics)): ?>
              <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')): ?>
              <?php echo $this->translate(array('%s review', '%s reviews', $page->review_count), $this->locale()->toNumber($page->review_count)) ?>
            <?php endif; ?>
          <?php endif; ?>

          <span class="fright">
            <?php if ($page->sponsored == 1 && $this->sponsoredIcon): ?>
              <i title="<?php echo $this->translate('Sponsored');?>" class="sitepage_icon sitepage_icon_sponsored"></i>
            <?php endif; ?>
            <?php if ($page->featured == 1 && $this->featuredIcon): ?>
              <i title="<?php echo $this->translate('Featured');?>" class="sitepage_icon sitepage_icon_featured"></i>
            <?php endif; ?>
          </span>
        </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>

