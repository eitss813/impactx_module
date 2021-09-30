<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    index.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2>
    <?php echo 'Advanced Payment Gateways / Stripe Connect Plugin'; ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
<?php endif; ?>

<div class='seaocore_settings_form'>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>

<script type="text/javascript">

    window.addEvent('domready', function () {
        stripeConnectOptions();
        if ($('sitegateway_mangopay_fees_charge')) {
            spn = new Element('span', {
                text: " %",
                style: "font-weight:bolder;font-size:18px;"
            });
            spn.inject($('sitegateway_mangopay_fees_charge'), 'after');
        }
    });
    function changeText(method) {
        if (method == 'escrow') {
            label.innerHTML = "Escrow <br /><span ><b>Note:</b> Note: This payment method is integrated only with ‘Stores / Marketplace - Ecommerce Plugin’ and 'Crowdfunding / Fundraising / Donations Plugin'.</span>";
        } else {
            label.innerHTML = "Escrow";
        }
    }
    function stripeConnectOptions() {

        if (document.getElementById('sitegateway_stripeconnect-wrapper')) {
            if (document.getElementById('sitegateway_stripeconnect-1').checked) {
                document.getElementById('sitegateway_stripechargemethod-wrapper').style.display = 'block';
            }
            else {
                document.getElementById('sitegateway_stripechargemethod-wrapper').style.display = 'none';
            }
        }
    }

</script>
