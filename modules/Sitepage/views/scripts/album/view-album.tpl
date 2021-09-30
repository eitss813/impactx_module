<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: viewalbum.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>
<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Sitepagealbum/externals/styles/style_sitepagealbum.css');
?>
<div class="sitepage_viewpages_head">
  <?php echo $this->htmlLink($this->sitepage->getHref(), $this->itemPhoto($this->sitepage, 'thumb.icon', '', array('align' => 'left'))) ?>
  <h2><?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->sitepage->page_id, $this->sitepage->owner_id, $this->sitepage->getSlug()), $this->sitepage->getTitle()) ?>  
    <?php echo $this->translate('&raquo; '); ?>	
    <?php echo $this->htmlLink(array('route' => 'sitepage_entry_view', 'page_url' => Engine_Api::_()->sitepage()->getPageUrl($this->sitepage->page_id), 'tab' => $this->tab_selected_id), $this->translate('Albums')) ?>
  </h2>			
</div>
<!--RIGHT AD START HERE-->
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adalbumview', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)): ?>
  <div class="layout_right" id="communityad_viewalbum">
    <?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adalbumview', 3),"loaded_by_ajax"=>1,'widgetId'=>'page_viewalbum'))?>
  </div>
<?php endif; ?>
<!--RIGHT AD END HERE-->  

<div class="layout_middle">
  <?php if (count($this->album) > 0) : ?>
    <div class="sitepage_album_box">
      <ul class="thumbs">
        <?php foreach ($this->album as $albums): ?>
          <li style="height:200px;"> 
            <?php if ($albums->photo_id != 0): ?>
              <a href="<?php echo $this->url(array('action' => 'view', 'page_id' => $this->sitepage->page_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug()), 'sitepage_albumphoto_general') ?>" class="thumbs_photo" title="<?php echo $albums->title; ?>">
                <?php echo $this->itemBackgroundPhoto($albums, 'thumb.normal', $albums->getTitle(), array(
                                'tag' => 'span',
                                )); 
                ?>
              </a>
            <?php else: ?>
              <a href="<?php echo $this->url(array('action' => 'view', 'page_id' => $this->sitepage->page_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug()), 'sitepage_albumphoto_general') ?>" class="thumbs_photo"  title="<?php echo $albums->title; ?>">
                <span><?php echo $this->itemPhoto($albums, 'thumb.normal') ?></span>
              </a>
            <?php endif; ?>
            <div class="sitepage_profile_album_title">
              <a href="<?php echo $this->url(array('action' => 'view', 'page_id' => $this->sitepage->page_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug()), 'sitepage_albumphoto_general') ?>" title="<?php echo $albums->title; ?>"><?php echo $albums->title; ?></a>
            </div>
            <div class="sitepage_profile_album_stat">
              <?php echo $this->translate(array('%s photo', '%s photos', $albums->count()), $this->locale()->toNumber($albums->count())) ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>	
  <?php endif; ?>
</div>