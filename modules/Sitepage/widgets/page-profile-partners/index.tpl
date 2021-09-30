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
    <div id="dynamic_app_info_sitepage">
        <?php if(count($this->partners) > 0 ): ?>
            <ul id="page-profile-partners" class="grid_wrapper">
                <?php foreach ($this->partners as $partner): ?>
                    <!-- <?php $sitepage = Engine_Api::_()->getItem('sitepage_page', $partner->partner_page_id); ?> -->
                   <?php $sitepage = Engine_Api::_()->getItem('sitepage_page', $partner); ?>
                    <li style="box-shadow: 0 1px 3px rgb(0 0 0 / 12%), 0 1px 2px rgb(0 0 0 / 24%) ; border-radius: 3px;padding: 7px 0px;">
                      <div class="org_outer_content">
                          <div class="org_profile_image">
                              <?php echo $this->htmlLink($sitepage->getHref(), $this->itemBackgroundPhoto($sitepage, 'thumb.profile')); ?>
                          </div>
                          <div class="org_inner_content">
                              <div class='followers-name'>
                                  <?php echo $this->htmlLink($sitepage->getHref(), $sitepage->getTitle()); ?>
                              </div>
                              <div class='org_description'>
                                  <?php echo $sitepage->getDescription(); ?>
                              </div>
                          </div>
                      </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="tip">
                <span>
                    <?php echo $this->translate('No sister organization found.'); ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
</div>
<style>
    .org_profile_image{
        width: 100px;
        height: 100px;
    }
   #page-profile-partners li {
       width: 48%;
       margin-top: 15px;
       margin-bottom: 15px;
    }
    .org_inner_content {
        padding-left: 7px;
    }
    .org_outer_content {
        display: flex;
    }
    .followers-name {
        margin-bottom: 4px;
        font-weight: bold;
    }
    .org_profile_image .bg_item_photo {
        width: 100px;
        height: 100px;
    }
    @media (max-width: 767px)
    {
        #page-profile-partners li {
            width: 100% !important;
        }
    }
</style>