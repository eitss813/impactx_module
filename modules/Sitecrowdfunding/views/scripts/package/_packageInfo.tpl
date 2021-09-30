<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _packageInfo.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$request = Zend_Controller_Front::getInstance()->getRequest();
$controller = $request->getControllerName();
$action = $request->getActionName();
?>

<?php if (!empty($this->viewer->level_id)): ?>
    <?php $level_id = $this->viewer->level_id; ?>
<?php else: ?>
    <?php $level_id = 0; ?>
<?php endif; ?>
<?php if (!empty($this->packageInfoArray)): ?>
    <div class="sitecrowdfunding_package_stats">

        <?php if (in_array('price', $this->packageInfoArray)): ?>
            <span>
                <strong><?php echo $this->translate("Price") . " : "; ?> </strong>
                <?php
                if ($item->price > 0):echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($item->price);
                else: echo $this->translate('Free');
                endif;
                ?>
            </span>
        <?php endif; ?>
        <?php if (in_array('billing_cycle', $this->packageInfoArray)): ?>
            <span>
                <strong><?php echo $this->translate("Billing Cycle") . " : "; ?> </strong>
                <?php echo $item->getBillingCycle() ?>
            </span>
        <?php endif; ?>
        <?php if (in_array('duration', $this->packageInfoArray)): ?>
            <span>
                <strong><?php echo ($item->price > 0 && $item->recurrence > 0 && $item->recurrence_type != 'forever' ) ? $this->translate("Billing Duration") . ": " : $this->translate("Duration") . " : "; ?> </strong>
                <?php echo $item->getPackageQuantity(); ?>
            </span>
        <?php endif; ?>
        <!--<br/>-->
        <?php if (in_array('featured', $this->packageInfoArray)): ?>
            <span>
                <strong><?php echo $this->translate("Featured") . " : "; ?> </strong>
                <?php
                if ($item->featured == 1)
                    echo $this->translate("Yes");
                else
                    echo $this->translate("No");
                ?>
            </span>
        <?php endif; ?>
        <?php if (in_array('sponsored', $this->packageInfoArray)): ?>
            <span>
                <strong><?php echo $this->translate("Sponsored") . " : "; ?> </strong>
                <?php
                if ($item->sponsored == 1)
                    echo $this->translate("Yes");
                else
                    echo $this->translate("No");
                ?>
            </span>
        <?php endif; ?>

        <?php if (in_array('rich_overview', $this->packageInfoArray) && ($this->overview && (!empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'sitecrowdfunding_project', "overview")))): ?>
            <span>
                <strong><?php echo $this->translate("Rich Overview") . " : "; ?> </strong>
                <?php
                if ($item->overview == 1)
                    echo $this->translate("Yes");
                else
                    echo $this->translate("No");
                ?>
            </span>
            <!--<br/>-->
        <?php endif; ?> 

        <?php if (in_array('videos', $this->packageInfoArray) && (!empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'sitecrowdfunding_project', "video"))): ?>
            <span>
                <strong><?php echo $this->translate("Videos") . " : "; ?> </strong>
                <?php
                if ($item->video == 1)
                    if ($item->video_count)
                        echo $item->video_count;
                    else
                        echo $this->translate("Unlimited");
                else
                    echo $this->translate("No");
                ?>
            </span>
        <?php endif; ?>

        <?php if (in_array('photos', $this->packageInfoArray) && (!empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'sitecrowdfunding_project', "photo"))): ?>
            <span>
                <strong><?php echo $this->translate("Photos") . " : "; ?> </strong>
                <?php
                if ($item->photo == 1)
                    if ($item->photo_count)
                        echo $item->photo_count;
                    else
                        echo $this->translate("Unlimited");
                else
                    echo $this->translate("No");
                ?>
            </span>
        <?php endif; ?>

        <?php if (in_array('commission', $this->packageInfoArray)): ?>
            <?php
            if (!empty($item->commission_settings)):
                $commissionInfo = @unserialize($item->commission_settings);
                $commissionType = $commissionInfo['commission_handling'];
                $commissionFee = $commissionInfo['commission_fee'];
                $commissionRate = $commissionInfo['commission_rate'];

            endif;
            ?>  
            <span>
                <strong><?php echo $this->translate("Commission") . " : "; ?> </strong>
                <?php if (!empty($item->commission_settings) && isset($commissionType)): ?>
                    <?php
                    if (empty($commissionType)):
                        echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency((int) $commissionFee);
                    else:
                        echo $commissionRate . '%';
                    endif;
                    ?>
                    <img class="mleft5" style="margin-bottom: -3px;" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/help.png" title="<?php echo $this->translate("Commission charged on the amount backed for a Project of this package."); ?>" > 
                <?php else: ?>
                    <?php echo $this->translate("N/A"); ?>
                <?php endif; ?>
            </span>
        <?php endif; ?>

        <?php if (in_array('description', $this->packageInfoArray) || ($controller == 'package' && $action != 'index')): ?>
            <div class="sitecrowdfunding_stats_detail icon">
                <?php if (empty($this->detailPackage)): ?>
                    <?php echo $this->viewMore($this->translate($item->description), 425); ?>
                <?php else: ?>
                    <?php echo $this->translate($item->description); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
<?php endif; ?>
<div class="clr"></div>


