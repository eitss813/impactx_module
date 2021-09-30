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

<div id="content">
    <div id="siteNotice"></div>
    <?php print_r($this->user); ?>
    <div class="sitecrowdfunding_map_info_tip o_hidden">
        <div class="sitecrowdfunding_map_info_tip_top o_hidden">
            <div class="sitecrowdfunding_map_info_tip_title">
                <?php echo $this->htmlLink($this->user[1], $this->user[0]) ?>
            </div>
        </div>
        <div class="sitecrowdfunding_map_info_tip_photo prelative" > 

        </div>
        <div class="sitecrowdfunding_map_info_tip_info">
            <div class="mbto5">
                hhh
            </div>
            <div class="mbto5">
                jjjj
            </div>
        </div>
    </div>
</div>