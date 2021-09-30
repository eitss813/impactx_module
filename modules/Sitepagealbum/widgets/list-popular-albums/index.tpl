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
    . 'application/modules/Sitepagealbum/externals/styles/style_sitepagealbum.css')
?>
<ul class="generic_list_widget generic_list_widget_large_photo">
  <?php foreach( $this->paginator as $item ): ?>
    <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
						$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.photos-sitepage', $item->page_id, $layout);?>
    <li>
      <div class="photo">
        <?php echo $this->htmlLink($item->getHref(array('tab'=> $tab_id)), $this->itemPhoto($item, 'thumb.normal'), array('class' => 'thumb')) ?>
      </div>
      <div class="info">
        <div class="title">
          <?php echo $this->htmlLink($item->getHref(array('tab' => $tab_id)), $this->string()->chunk($this->string()->truncate($item->getTitle(), 45), 10),array('title' => $item->getTitle())) ?>
        </div>
        <div class="stats">
          <?php if( $this->popularType == 'view' ): ?>
            <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>
          <?php elseif( $this->popularType == 'comment' ): ?>
            <?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?>
          <?php elseif( $this->popularType == 'like' ): ?>
            <?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>
          <?php endif; ?>
        </div>
        <div class="owner">
					<?php
					$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
					$tmpBody = strip_tags($item->page_title);
					$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
					?>
					<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($item->page_id, $item->owner_id, $item->getSlug()),  $page_title,array('title' => $item->page_title)) ?>      
					&bull; <?php echo $this->translate(array('%s photo', '%s photos', $item->count()),$this->locale()->toNumber($item->count())); ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>