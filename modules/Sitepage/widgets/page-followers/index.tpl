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

<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
?>
<div class="layout_core_container_tabs">
    <div id="dynamic_app_info_sitecrowdfunding">

        <?php $count = 0; ?>

        <ul id="page-followers" class="grid_wrapper">
            <?php foreach ($this->paginator as $user): ?>
                <?php $count++; ?>
                <li>
                    <?php echo $this->htmlLink($user->getHref(), $this->itemBackgroundPhoto($user, 'thumb.profile')); ?>
                    <div class='followers-name'>
                        <?php echo $this->htmlLink($user->getHref(), $user->getTitle()); ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if ($count==0): ?>
            <div class="tip">
                <span>
                    <?php echo $this->translate('No Followers'); ?>
                </span>
            </div>
        <?php endif; ?>

    </div>
</div>