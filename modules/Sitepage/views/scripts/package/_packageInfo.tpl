<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _packageinfo.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$package_view = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.view', 1);
$packageInfoArray = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.information',array('price', 'billing_cycle', 'duration', 'featured', 'sponsored', 'tellafriend', 'print', 'overview', 'map', 'insights', 'contactdetails', 'sendanupdate', 'apps', 'description', 'twitterupdates', 'ads'));
?>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
<div class="sitepage_package_stat">
    <?php if (in_array('price', $packageInfoArray)): ?>
        <span>
            <b><?php echo $this->translate("Price") . ": "; ?> </b>
            <?php if ($item->price > 0):echo Engine_Api::_()->sitepage()->getPriceWithCurrency($item->price);
            else: echo $this->translate('FREE');
            endif; ?>
        </span>
<?php endif; ?>
        <?php if (in_array('billing_cycle', $packageInfoArray)): ?>
        <span>
            <b><?php echo $this->translate("Billing Cycle") . ": "; ?> </b>
        <?php echo $item->getBillingCycle() ?>
        </span>
<?php endif; ?>
        <?php if (in_array('duration', $packageInfoArray)): ?>
        <span  style="width: auto;">
            <b><?php echo ($item->price > 0 && $item->recurrence > 0 && $item->recurrence_type != 'forever' ) ? $this->translate("Billing Duration") . ": " : $this->translate("Duration") . ": "; ?> </b>
        <?php echo $item->getPackageQuantity(); ?>
        </span>
    <?php endif; ?>
    <br />
        <?php if (in_array('featured', $packageInfoArray)): ?>
        <span>
            <b><?php echo $this->translate("Featured") . ": "; ?> </b>
            <?php
            if ($item->featured == 1)
                echo $this->translate("Yes");
            else
                echo $this->translate("No");
            ?>
        </span>
<?php endif; ?>
        <?php if (in_array('sponsored', $packageInfoArray)): ?>
        <span>
            <b><?php echo $this->translate("Sponsored") . ": "; ?> </b>
            <?php
            if ($item->sponsored == 1)
                echo $this->translate("Yes");
            else
                echo $this->translate("No");
            ?>
        </span>
    <?php endif; ?>
<?php if (in_array('ads', $packageInfoArray)): ?>
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')): ?>
            <span>
                <b><?php echo $this->translate("Ads Display") . ": "; ?> </b>
                <?php
                if ($item->ads == 1 && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1))
                    echo $this->translate("Yes");
                else
                    echo $this->translate("No");
                ?>
            </span>
        <?php endif; ?>       
<?php endif; ?> 
        <?php if (in_array('tellafriend', $packageInfoArray)): ?>
        <span>
            <b><?php echo $this->translate("Tell a friend") . ": "; ?> </b>
            <?php
            if ($item->tellafriend == 1)
                echo $this->translate("Yes");
            else
                echo $this->translate("No");
            ?>
        </span>
<?php endif; ?> 
        <?php if (in_array('print', $packageInfoArray)): ?>
        <span>
            <b><?php echo $this->translate("Print") . ": "; ?> </b>
            <?php
            if ($item->print == 1)
                echo $this->translate("Yes");
            else
                echo $this->translate("No");
            ?>
        </span>
<?php endif; ?> 
        <?php if (in_array('overview', $packageInfoArray)): ?>
        <span>
            <b><?php echo $this->translate("Rich Overview") . ": "; ?> </b>
            <?php
            if ($item->overview == 1)
                echo $this->translate("Yes");
            else
                echo $this->translate("No");
            ?>
        </span>
<?php endif; ?> 
        <?php if (in_array('map', $packageInfoArray)): ?>
        <span>
            <b><?php echo $this->translate("Map") . ": "; ?> </b>
            <?php
            if ($item->map == 1)
                echo $this->translate("Yes");
            else
                echo $this->translate("No");
            ?>
        </span>
<?php endif; ?> 
        <?php if (in_array('insights', $packageInfoArray)): ?>
        <span>
            <b><?php echo $this->translate("Insights") . ": "; ?> </b>
            <?php
            if ($item->insights == 1)
                echo $this->translate("Yes");
            else
                echo $this->translate("No");
            ?>
        </span>
<?php endif; ?> 
        <?php if (in_array('contactdetails', $packageInfoArray)): ?>
        <span>
            <b><?php echo $this->translate("Contact Details") . ": "; ?> </b>
            <?php
            if ($item->contact_details == 1)
                echo $this->translate("Yes");
            else
                echo $this->translate("No");
            ?>
        </span>  
<?php endif; ?> 
        <?php if (in_array('sendanupdate', $packageInfoArray)): ?>
        <span>
            <b><?php echo $this->translate("Send an Update") . ": "; ?> </b>
            <?php
            if ($item->sendupdate == 1)
                echo $this->translate("Yes");
            else
                echo $this->translate("No");
            ?>
        </span>
    <?php endif; ?> 
    <?php if (false && in_array('foursquarebutton', $packageInfoArray)): ?>
       <span><b><?php echo $this->translate("Save To Foursquare Button") . ": "; ?> </b>
        <?php
        if ($item->foursquare == 1)
            echo $this->translate("Yes");
        else
            echo $this->translate("No");
        ?></span>
    <?php endif; ?> 
<?php if (in_array('twitterupdates', $packageInfoArray)): ?>
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter')) : ?>
            <span>
                <b><?php echo $this->translate("Display Twitter Updates") . ": "; ?> </b>
                <?php
                if ($item->twitter == 1)
                    echo $this->translate("Yes");
                else
                    echo $this->translate("No");
                ?>
            </span>
        <?php endif; ?> 
    <?php endif; ?> 
    <?php if (in_array('apps', $packageInfoArray)): ?>
        <?php
        $module = unserialize($item->modules);
        if (!empty($module)):
            $subModuleStr = $item->getSubModulesString();
            if (!empty($item->modules) && !empty($subModuleStr)):
                ?>
                <span class="sitepage_package_stat_apps">
                    <b><?php echo $this->translate("Apps available") . ": "; ?> </b>
                <?php echo $subModuleStr; ?>
                </span>
        <?php endif; ?>
    <?php endif; ?>
    <?php endif; ?> 
</div>
    <?php if (in_array('description', $packageInfoArray)): ?>
    <div class="sitepage_list_details">
        <?php if (empty($this->detailPackage)): ?>
            <?php echo $this->viewMore($this->translate($item->description), 425); ?>
    <?php else: ?>
        <?php echo $this->translate($item->description); ?>
    <?php endif; ?>
    </div>
<?php endif; ?> 
<div class="clr"></div>