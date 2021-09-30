<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: FundingProgressiveBar.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_View_Helper_FundingProgressiveBar extends Zend_View_Helper_Abstract {

    public function fundingProgressiveBar($fundedRatio) {

        $fundedRatio = $fundedRatio>100?100:$fundedRatio;
?>
<div class="sitecrowdfunding_funding_bar">
    <div class="funding_percent" style="width:<?php echo $fundedRatio;?>%";>
        <div class="funding_animation"></div>
    </div>
</div>
    <?php 
         
        
    }

}
