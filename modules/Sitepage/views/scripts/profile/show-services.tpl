<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: contact.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()
      ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/style_sitepage.css');
      $this->headLink()
      ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/style_sitepage_profile.css');
?>
<?php if (empty($this->is_ajax)) : ?>
  <div class="generic_layout_container layout_middle">
    <?php if (count($this->services) > 0) : ?>
      <?php foreach ($this->services as $item): ?>
        <div id='<?php echo $item->service_id ?>_page_main'  class='sitepage_manage_announcements_list'>
          <div id='<?php echo $item->service_id ?>_page'>
            <?php
            $url = $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/nophoto_page_thumb_icon.png';
            $temp_url = $item->getPhotoUrl();
            if (!empty($temp_url)):
              $url = $item->getPhotoUrl('thumb.icon');
            endif;
            ?>
            <img src="<?php echo $url;?>">
          </div>
          <div class="sitepage_manage_announcements_info_popup">
            <div class="sitepage_manage_announcements_title">
              <span><?php echo $item->title; ?></span>
            </div>
            <div class="sitepage_manage_announcements_dates seaocore_txt_light">
              <b><?php echo $this->translate("Duration: ")?></b> <?php echo $this->translate($item->duration); ?>&nbsp;&nbsp;&nbsp;
              <b><?php echo $this->translate("Duration type: ") ?></b><?php echo $this->translate($item->duration_type); ?>
            </div>
            <div class="sitepage_manage_announcements_body show_content_body">
              <b><?php echo $this->translate("Description: ") ?></b>f<?php echo $item->body ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif;?>
  </div>
<?php endif;?>
