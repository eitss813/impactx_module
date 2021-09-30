<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    _stripeChargeMethod.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $stripechargemethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripechargemethod');?>

<div class="form-wrapper" id="sitegateway_stripechargemethod-wrapper" style="display: block;">
    <div class="form-label" id="sitegateway_stripechargemethod-label">
        <label class="optional" for="sitegateway_stripechargemethod">Stripe's Fees</label>
    </div>
    <div class="form-element" id="sitegateway_stripechargemethod-element">
        <p class="description">Who will pay Stripe’s fees for payments processing?</p>

        <ul class="form-options-wrapper">
            <li>
                <input type="radio" <?php echo (($stripechargemethod == '0') ? 'checked=checked' : '') ?> value="0" id="sitegateway_stripechargemethod-0" name="sitegateway_stripechargemethod">
                <label for="sitegateway_stripechargemethod-0">Connected Stripe accounts (sellers) will pay Stripe’s fees. [Here, connected account of sellers will be responsible for Stripe’s fees, any refunds and chargebacks.]
                </label>
            </li>
            <li>
                <input type="radio" <?php echo ($stripechargemethod ? 'checked=checked' : '') ?> value="1" id="sitegateway_stripechargemethod-1" name="sitegateway_stripechargemethod">
                <label for="sitegateway_stripechargemethod-1">Platform's Stripe account (your website's Stripe account) will pay Stripe’s fees. [Here, platform account will be responsible for Stripe’s fees, any refunds and chargebacks.]
                </label>
            </li>
        </ul>
    </div>
</div>