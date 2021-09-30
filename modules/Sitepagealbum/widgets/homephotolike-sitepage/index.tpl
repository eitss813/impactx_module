<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<?php 
  echo $this->partial('application/modules/Sitepage/views/scripts/partialPhotoWidget.tpl', array('paginator' => $this->paginator, 'showLightBox' => $this->showLightBox, 'show_detail' =>  1,'show_info' => 'like', 'includeCss' => 1, 'displayPageName' => $this->displayPageName, 'displayUserName' => $this->displayUserName, 'showFullPhoto' => $this->showFullPhoto, 'type' => 'like_count', 'count' => $this->count, 'urlaction' => 'like'));
?>
