<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _mapInfoWindowContent.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<style>
    .sitecrowdfunding_map_info_tip{
        width: auto !important;
        min-height: auto !important;
    }
    .sitecrowdfunding_map_info_tip_photo{
        /*float: none !important;*/
        /*text-align: center !important;*/
    }
    .sitecrowdfunding_map_info_tip_photo > a > img{
        max-width: 100px !important;
        max-height: 150px !important;
    }
    .sitecrowdfunding_map_info_tip_title a{
        font-size: 16px !important;
        font-weight: bold !important;
    }
</style>
<div id="content">
    <div id="siteNotice">
    </div>
    <div id="map_users">
        <?php $user = Engine_Api::_()->getItem('user', $this->user_id); ?>
        <div class="sitecrowdfunding_map_info_tip o_hidden">
            <div class="sitecrowdfunding_map_info_tip_top o_hidden">
                <div class="sitecrowdfunding_map_info_tip_title">
                    <?php echo $this->htmlLink($user->getHref(), $this->translate($this->user_type.' - '.$user->getTitle())) ?>
                </div>
            </div>
            <div class="sitecrowdfunding_map_info_tip_photo prelative" >
                <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.normal')) ?>
            </div>
        </div>
    </div>
</div>