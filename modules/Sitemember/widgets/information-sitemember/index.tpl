<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>

<ul>
  <li class="siteevent_profile_event_info_btns txt_center">
<?php if (!empty($this->showContent) && in_array('likebutton', $this->showContent) && $this->viewer->getIdentity()): ?>
      <?php echo $this->content()->renderWidget("seaocore.like-button") ?>
    <?php endif; ?>

    <?php if (!empty($this->showContent)) : ?>
      <?php
      $this->addHelperPath(APPLICATION_PATH . '/application/modules/Sitemember/View/Helper', 'Sitemember_View_Helper');
      echo $this->memberInfo($this->user, $this->showContent, array('customParams' => $this->customParams, 'custom_field_title' => $this->custom_field_title, 'custom_field_heading' => $this->custom_field_heading));
      ?>
    <?php endif; ?>
  </li>
</ul>