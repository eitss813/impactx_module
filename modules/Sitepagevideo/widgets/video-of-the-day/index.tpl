<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitepagevideo/externals/styles/style_sitepagevideo.css')
?>
<?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
						$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagevideo.profile-sitepagevideos', $this->videoOfDay->page_id, $layout);?>
<ul class="generic_list_widget generic_list_widget_large_photo">
	<li>
		<div>
			<a href="<?php echo $this->url(array('user_id' => $this->videoOfDay->owner_id, 'video_id' =>  $this->videoOfDay->video_id,'tab' => $tab_id,'slug' => $this->videoOfDay->getSlug()),'sitepagevideo_view', true)?>">
				<div class="sitepagevideo_thumb_wrapper">
					<?php if ($this->videoOfDay->duration): ?>
						<span class="sitepagevideo_length">
							<?php
							if ($this->videoOfDay->duration > 360)
							$duration = gmdate("H:i:s", $this->videoOfDay->duration); else
							$duration = gmdate("i:s", $this->videoOfDay->duration);
							if ($duration[0] == '0')
							$duration = substr($duration, 1); echo $duration;
							?>
						</span>
					<?php endif; ?>
					<?php  if ($this->videoOfDay->photo_id): ?>
						<?php echo   $this->itemPhoto($this->videoOfDay, 'thumb.normal'); ?>
					<?php else: ?>
						<img src= "<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagevideo/externals/images/video.png" class="thumb_normal item_photo_video  thumb_normal" />
					<?php endif;?>
				</div>
		  </a>
		</div>
		<div class="info clr">
			<div class="title">
        <?php echo $this->htmlLink($this->videoOfDay->getHref(array('tab' => $tab_id)), $this->string()->chunk($this->string()->truncate($this->videoOfDay->getTitle(), 45), 10),array('title' => $this->videoOfDay->getTitle())) ?>
			</div>
	    <div class="owner">
				<?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $this->videoOfDay->page_id);?>
				<?php
				$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
				$tmpBody = strip_tags($sitepage_object->title);
				$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
				?>	
			<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->videoOfDay->page_id, $this->videoOfDay->owner_id, $this->videoOfDay->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?>      
			</div>	
		</div>
	</li>
</ul>		