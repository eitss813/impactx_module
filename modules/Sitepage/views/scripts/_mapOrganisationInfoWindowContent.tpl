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

<?php
$sitepage = $this->sitepage;
$location = $this->location;
$page_type = $this->page_type;
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
    .days > b{
        font-weight: unset !important;
    }
</style>

<div id="content">
    <div id="siteNotice">
    </div>
    <div class="sitecrowdfunding_map_info_tip o_hidden">
        <div class="sitecrowdfunding_map_info_tip_top o_hidden">
            <div class="sitecrowdfunding_map_info_tip_title">
                <?php echo $this->htmlLink($sitepage->getHref(), $this->translate($page_type." - ".$sitepage->getTitle())) ?>
            </div>
        </div>
        <div class="sitecrowdfunding_map_info_tip_photo prelative" >
            <?php echo $this->htmlLink($sitepage->getHref(), $this->itemPhoto($sitepage, 'thumb.cover')) ?>
        </div>
    </div>
</div>