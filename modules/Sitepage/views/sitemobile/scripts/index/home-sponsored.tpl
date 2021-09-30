<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: homesponsored.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1);?>
<?php if ($this->direction == 1) { ?>
  <?php foreach ($this->sitepagesitepage as $sitepage): ?>
    <div class="SlideItMoo_element seaocore_sponsored_carousel_items">
      <div class="seaocore_sponsored_carousel_items_thumb">
        <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()), $this->itemPhoto($sitepage, 'thumb.icon', $sitepage->getTitle()), array('rel' => 'lightbox[galerie]', 'class' => "thumb_icon")) ?>
      </div>
      <div class="seaocore_sponsored_carousel_items_info">
        <div class="seaocore_sponsored_carousel_items_title">
          <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()), Engine_Api::_()->sitepage()->truncation($sitepage->getTitle(), $this->titletruncation), array('title' => $sitepage->getTitle())) ?>
        </div>
        <?php if($postedBy):?>
          <div class="seaocore_sponsored_carousel_items_stat seaocore_txt_light">
            <?php echo $this->translate('posted by'); ?>
            <?php echo $this->htmlLink($sitepage->getOwner()->getHref(), Engine_Api::_()->sitepage()->truncation($sitepage->getOwner()->getTitle(), 10), array('title' => $sitepage->getOwner()->getTitle())) ?>
          </div>
        <?php endif;?>
      </div>
    </div>	 
  <?php endforeach; ?>
<?php } else { ?>
  <?php $count = $this->totalpages; ?>
  <?php for ($i = $count; $i < $this->count; $i++): ?>
    <div class="SlideItMoo_element seaocore_sponsored_carousel_items">
      <div class="seaocore_sponsored_carousel_items_thumb">
        <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->sitepagesitepage[$i]->page_id, $this->sitepagesitepage[$i]->owner_id, $this->sitepagesitepage[$i]->getSlug()), $this->itemPhoto($this->sitepagesitepage[$i], 'thumb.icon', $this->sitepagesitepage[$i]->getTitle()), array('rel' => 'lightbox[galerie]', 'class' => "thumb_icon")) ?>
      </div>
      <div class="seaocore_sponsored_carousel_items_info">
        <div class="seaocore_sponsored_carousel_items_title">
          <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->sitepagesitepage[$i]->page_id, $this->sitepagesitepage[$i]->owner_id, $this->sitepagesitepage[$i]->getSlug()), Engine_Api::_()->sitepage()->truncation($this->sitepagesitepage[$i]->getTitle(), $this->titletruncation), array('title' => $this->sitepagesitepage[$i]->getTitle())) ?>
        </div>      
        <?php if($postedBy):?>
          <div class="seaocore_sponsored_carousel_items_stat seaocore_txt_light">
            <?php echo $this->translate('posted by'); ?>
            <?php echo $this->htmlLink($this->sitepagesitepage[$i]->getOwner()->getHref(), Engine_Api::_()->sitepage()->truncation($this->sitepagesitepage[$i]->getOwner()->getTitle(), 10), array('title' => $this->sitepagesitepage[$i]->getOwner()->getTitle())) ?>
          </div>
        <?php endif;?>
      </div>
    </div>	 
  <?php endfor; ?>
  <?php for ($i = 0; $i < $count; $i++): ?>
    <div class="SlideItMoo_element seaocore_sponsored_carousel_items">
      <div class="seaocore_sponsored_carousel_items_thumb">
        <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->sitepagesitepage[$i]->page_id, $this->sitepagesitepage[$i]->owner_id, $this->sitepagesitepage[$i]->getSlug()), $this->itemPhoto($this->sitepagesitepage[$i], 'thumb.icon', $this->sitepagesitepage[$i]->getTitle()), array('rel' => 'lightbox[galerie]', 'class' => "thumb_icon")) ?>
      </div>
      <div class="seaocore_sponsored_carousel_items_info">
        <div class="seaocore_sponsored_carousel_items_title">
          <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->sitepagesitepage[$i]->page_id, $this->sitepagesitepage[$i]->owner_id, $this->sitepagesitepage[$i]->getSlug()), Engine_Api::_()->sitepage()->truncation($this->sitepagesitepage[$i]->getTitle(), $this->titletruncation), array('title' => $this->sitepagesitepage[$i]->getTitle())) ?>
        </div> 
        <?php if($postedBy):?>
          <div class="seaocore_sponsored_carousel_items_stat seaocore_txt_light">
            <?php echo $this->translate('posted by'); ?>
            <?php echo $this->htmlLink($this->sitepagesitepage[$i]->getOwner()->getHref(), Engine_Api::_()->sitepage()->truncation($this->sitepagesitepage[$i]->getOwner()->getTitle(), 10), array('title' => $this->sitepagesitepage[$i]->getOwner()->getTitle())) ?>
          </div>
        <?php endif;?>
      </div>
    </div>
  <?php endfor; ?>
<?php } ?>