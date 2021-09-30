<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'); ?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/Adintegration.tpl';
?>
<div class="sitecrowdfunding_view_top">

    <?php echo $this->htmlLink($this->project->getHref(), $this->itemPhoto($this->project, 'thumb.icon', '', array('align' => 'left'))) ?>
    <h2>	
        <?php echo $this->translate($this->project->__toString()) ?>	
        <?php echo $this->translate('&raquo; '); ?>
        <?php echo $this->htmlLink($this->project->getHref(array('tab' => $this->tab_selected_id)), $this->translate('Discussions')) ?>
    </h2>
</div>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.addiscussioncreate', 3) && $project_communityad_integration): ?>
    <div class="layout_right" id="communityad_discussioncreate">
        <?php echo $this->content()->renderWidget("communityad.ads", array("itemCount" => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.addiscussioncreate', 3), "loaded_by_ajax" => 0, 'widgetId' => 'project_discussioncreate')); ?>
    </div>
<?php endif; ?>

<div class="layout_middle">
    <?php echo $this->form->render($this) ?>
</div>