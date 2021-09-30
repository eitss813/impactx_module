<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: post.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'); ?>

<?php
include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/Adintegration.tpl';
?>

<div class="sitecrowdfunding_view_top">
    <?php echo $this->htmlLink($this->project->getHref(), $this->itemPhoto($this->project, 'thumb.icon', '', array('align' => 'left'))) ?>
    <h2>	
        <?php echo $this->translate($this->project->__toString()) ?>	
        <?php echo $this->translate('&raquo; '); ?>
        <?php echo $this->htmlLink($this->project->getHref(array('tab' => $this->tab_selected_id)), $this->translate('Discussions')) ?>
        <?php echo $this->translate('&raquo; '); ?>
        <?php echo $this->translate($this->topic->__toString()) ?>
    </h2>
</div>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.addiscussionreply', 3) && $project_communityad_integration): ?>
    <div class="layout_right" id="communityad_post">
        <?php echo $this->content()->renderWidget("communityad.ads", array("itemCount" => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.addiscussionreply', 3), "loaded_by_ajax" => 0, 'widgetId' => 'project_addiscussionreply')); ?>
    </div>
<?php endif; ?>

<div class="layout_middle">
    <?php if ($this->message) echo $this->translate($this->message) ?>
    <?php if ($this->form) echo $this->form->render($this) ?>
</div>