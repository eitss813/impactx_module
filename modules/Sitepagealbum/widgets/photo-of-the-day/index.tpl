<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitepage/externals/styles/style_sitepage.css')
?>
<?php
$photoSettings = array();
$photoSettings['class'] = 'thumb';
$photoSettings['title'] =  $this->photoOfDay->getTitle();
if ($this->showLightBox):
$photoSettings["onclick"]="openSeaocoreLightBox('".$this->photoOfDay->getHref()."');return false;";
 // include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/_lightboxPhoto.tpl';
endif;
?>
<?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
						$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.photos-sitepage', $this->photoOfDay->page_id, $layout);?>
<ul class="generic_list_widget generic_list_widget_large_photo">
	<li>
		<div class="photo">
		  <?php echo $this->htmlLink($this->photoOfDay->getHref(), $this->itemPhoto($this->photoOfDay), $photoSettings); ?>
		</div>
		<div class="info">
			<div class="owner">
			  <?php
			  $owner = $this->photoOfDay->getOwner();
			  $parent = $parent = $sitepagealbum_object = Engine_Api::_()->getItem('sitepage_album', $this->photoOfDay->album_id);
			  echo $this->translate('in ').
			          $this->htmlLink($parent->getHref(array('tab' => $tab_id)), $this->string()->chunk($this->string()->truncate($parent->getTitle(), 45), 10),array('title' => $parent->getTitle()));
			  ?>
				<?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $this->photoOfDay->page_id);?>
				<?php
				$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
				$tmpBody = strip_tags($sitepage_object->title);
				$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
				?>
				<?php echo $this->translate("of ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->photoOfDay->page_id, $this->photoOfDay->user_id, $this->photoOfDay->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?>  
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1)):?> 
					<?php echo $this->translate('by ').
									$this->htmlLink($owner->getHref(), $owner->getTitle(),array('title' => $owner->getTitle()));?>
        <?php endif;?>
			</div>
		</div>	
	</li>  
</ul>