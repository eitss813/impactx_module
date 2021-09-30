<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'); ?>
<?php if ($this->create_button): ?>
    <?php if (Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()): ?>
        <?php echo $this->htmlLink(array('route' => "sitecrowdfunding_package", 'action' => 'index'), $this->translate($this->create_button_title), array('class' => 'create_project_link common_btn header_btn seaocore_icon_add')) ?>
    <?php else : ?>
        <?php echo $this->htmlLink(array('route' => "sitecrowdfunding_project_general", 'action' => 'create'), $this->translate($this->create_button_title), array('class' => 'create_project_link common_btn header_btn seaocore_icon_add')) ?>
    <?php endif ?>
<?php else : ?>
    <?php if (Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()): ?>
        <?php echo $this->htmlLink(array('route' => "sitecrowdfunding_package", 'action' => 'index'), $this->translate($this->create_button_title), array('class' => 'create_project_link header_start_link seaocore_icon_add')) ?>
    <?php else : ?>
        <?php echo $this->htmlLink(array('route' => "sitecrowdfunding_project_general", 'action' => 'create'), $this->translate($this->create_button_title), array('class' => 'create_project_link header_start_link seaocore_icon_add')) ?>
    <?php endif ?>

<?php endif; ?>
