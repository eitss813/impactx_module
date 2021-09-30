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
      <div class="sitepage_manage_announcements_info">
        <div class="sitepage_manage_announcements_title">
          <span><?php echo $item->title; ?></span>
        </div> 
        <div class="sitepage_manage_announcements_dates seaocore_txt_light">
          <b><?php echo $this->translate("Duration: ")?></b> <?php echo $this->translate($item->duration); ?>&nbsp;<?php echo $this->translate($item->duration_type); ?>
        </div>
        </div>
        <div class="sitepage_manage_announcements_body show_content_body"> 
          <?php echo $item->body;?>
        </div>
    </div>
  <?php endforeach; ?>
  <?php else: ?>
    <div class="tip">
      <span><?php echo $this->translate('No services have been posted for this page yet.'); ?></span>
    </div>
  <?php endif;?>
  <?php
  if($this->more == true) {
    echo $this->htmlLink($this->url(array('controller' => 'profile',"action" => "show-services", "page_id" => $this->sitepage->page_id), "sitepage_extended",true), $this->translate("View More"), array('class' => 'smoothbox'));
  }
  ?>