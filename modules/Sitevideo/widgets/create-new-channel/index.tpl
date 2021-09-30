<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css'); ?>
<?php echo $this->htmlLink(array('route' => 'sitevideo_general', 'action' => 'create'), $this->translate('Create New Channel'), array('class' => 'button seaocore_icon_add')) ?>
