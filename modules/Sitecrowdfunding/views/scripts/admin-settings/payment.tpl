<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: payment.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
    <?php echo 'Crowdfunding / Fundraising / Donations Plugin'; ?>
</h2>
<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>
  
    <div class='seaocore_settings_form'>
        <div class='settings'>
            <?php
            echo $this->form->render($this);
            ?>
        </div>
    </div>
    <script>
        function selectPaymentOption(value) {
            if (value == 'automatic') {
                $('sitecrowdfunding_automatic_payment_method-wrapper').show();

            } else {
                $('sitecrowdfunding_automatic_payment_method-wrapper').hide();

            }
        }

        function selectPaymentMethod(value) {
            if (value == 'escrow') {
                $('sitecrowdfunding_allowed_payment_split_gateway-wrapper').hide();
                $('sitecrowdfunding_allowed_payment_gateway-wrapper').hide();
                $('sitecrowdfunding_allowed_payment_escrow_gateway-wrapper').show();
                $('sitecrowdfunding_payment_setting-wrapper').show();
                hideNormalGatewayElem();
                if ($('sitecrowdfunding_payment_setting-automatic').checked) {
                    selectPaymentOption($('sitecrowdfunding_payment_setting-automatic').value);
                } else {
                    selectPaymentOption($('sitecrowdfunding_payment_setting-mannual').value);
                }
            } else if (value == 'split') {
                $('sitecrowdfunding_allowed_payment_escrow_gateway-wrapper').hide();
                $('sitecrowdfunding_allowed_payment_gateway-wrapper').hide();
                $('sitecrowdfunding_payment_setting-wrapper').hide();
                $('sitecrowdfunding_automatic_payment_method-wrapper').hide();
                $('sitecrowdfunding_allowed_payment_split_gateway-wrapper').show();
                hideNormalGatewayElem();
            } else {
                $('sitecrowdfunding_allowed_payment_gateway-wrapper').show();
                $('sitecrowdfunding_allowed_payment_escrow_gateway-wrapper').hide();
                $('sitecrowdfunding_allowed_payment_split_gateway-wrapper').hide();
                $('sitecrowdfunding_payment_setting-wrapper').hide();
                $('sitecrowdfunding_automatic_payment_method-wrapper').hide();
                showPaymentForOrders();
            }
        }
        // START FUNCTIONS TO MANAGE PAYMENT FLOW FOR ORDERS AND PAYMNET GATEWAYS
        function showPaymentForOrders() {
            if ($("sitecrowdfunding_payment_to_siteadmin-wrapper")) {
                $("sitecrowdfunding_payment_to_siteadmin-wrapper").style.display = 'block';
                showPaymentForOrdersGateway();
            }
        }
        function showPaymentForOrdersGateway() {
            if ($("sitecrowdfunding_allowed_payment_gateway-wrapper")) {
                if ($("sitecrowdfunding_payment_to_siteadmin-0").checked) {
                    $("sitecrowdfunding_allowed_payment_gateway-wrapper").style.display = 'block';
                    $("sitecrowdfunding_thresholdnotification-wrapper").style.display = 'block';
                }
                else {
                    $("sitecrowdfunding_allowed_payment_gateway-wrapper").style.display = 'none';
                    $("sitecrowdfunding_thresholdnotification-wrapper").style.display = 'none';
                }
            }

            if ($("sitecrowdfunding_admin_gateway-wrapper")) {
                if ($("sitecrowdfunding_payment_to_siteadmin-1").checked)
                    $("sitecrowdfunding_admin_gateway-wrapper").style.display = 'block';
                else
                    $("sitecrowdfunding_admin_gateway-wrapper").style.display = 'none';
            }

            billPaymentSettings();
            thresholdNotification();
        }
        function billPaymentSettings() {
            $("sitecrowdfunding_paymentmethod-wrapper").show();
            if ($("sitecrowdfunding_payment_to_siteadmin-0").checked && $("sitecrowdfunding_paymentmethod-wrapper")) {
                $("sitecrowdfunding_paymentmethod-label").innerHTML = "Payment for 'Commissions Bill'";
                $("sitecrowdfunding_paymentmethod-element").children[0].innerHTML = "Select the payment gateway to be available to project owners for admin ‘Commissions Bill’ payment, if ‘Direct Payment to Project Owners’ is selected.";
                if (<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0); ?> && $("sitecrowdfunding_paymentmethod-stripe")) {
                    $("sitecrowdfunding_paymentmethod-stripe").getParent().getElement('label').innerHTML = "Stripe [Here, normal Stripe account will be used by sellers to pay admin 'Commissions Bill' which are collected through other than Stripe Connect payment gateway.]";
                }
            }
            else if ($("sitecrowdfunding_paymentmethod-wrapper")) {
                $("sitecrowdfunding_paymentmethod-label").innerHTML = "Payment for Sellers 'Payment Requests'";
                $("sitecrowdfunding_paymentmethod-element").children[0].innerHTML = "Select the payment gateway to be available to site admin for making payments against the 'Payment Requests' made by sellers, if ‘Payment to Website / Site Admin’ is selected.";
                if (<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0); ?> && $("sitecrowdfunding_paymentmethod-stripe")) {
                    $("sitecrowdfunding_paymentmethod-stripe").getParent().getElement('label').innerHTML = "Stripe [Here, normal Stripe account will be used by admin to pay seller's payments which are collected through other than Stripe Connect payment gateway.]";
                }
            }
        }
        function thresholdNotification() {

            //THRESHOLD AMOUNT NOTIFICATION WORK
            if ($("sitecrowdfunding_payment_to_siteadmin-0").checked && $("sitecrowdfunding_thresholdnotification-1").checked) {
                $("sitecrowdfunding_thresholdnotificationamount-wrapper").style.display = 'block';
                $("sitecrowdfunding_thresholdnotify-wrapper").style.display = 'block';
            }
            else {
                $("sitecrowdfunding_thresholdnotificationamount-wrapper").style.display = 'none';
                $("sitecrowdfunding_thresholdnotify-wrapper").style.display = 'none';
            }

        }
        function hideNormalGatewayElem() {
            $('sitecrowdfunding_payment_to_siteadmin-wrapper').hide();
            $('sitecrowdfunding_paymentmethod-wrapper').hide();
            $('sitecrowdfunding_thresholdnotification-wrapper').hide();
            $('sitecrowdfunding_thresholdnotificationamount-wrapper').hide();
            $('sitecrowdfunding_thresholdnotify-wrapper').hide();
            $('sitecrowdfunding_admin_gateway-wrapper').hide();
        }

        if ($('sitecrowdfunding_payment_method-split').checked) {
            selectPaymentMethod($('sitecrowdfunding_payment_method-split').value);
        } else if ($('sitecrowdfunding_payment_method-escrow').checked) {
            selectPaymentMethod($('sitecrowdfunding_payment_method-escrow').value);
        } else {
            selectPaymentMethod($('sitecrowdfunding_payment_method-normal').value);
        }
    </script>
 