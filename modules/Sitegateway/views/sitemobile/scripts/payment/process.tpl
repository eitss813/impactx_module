<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    process.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headScriptSM()->appendFile("https://checkout.stripe.com/checkout.js"); ?>
<?php $product = $this->product; ?>
<?php $allParams = $this->allParams; ?>

<div id="processingPaymentStripe">
    <div>
        <center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/loading.gif" /></center>
    </div>
    <div id="LoadingImage" style="text-align:center;margin-top:15px;font-size:17px;">  
        <?php echo $this->translate("Processing Request. Please wait .....") ?>
    </div>
</div>

<script>

    var handler = StripeCheckout.configure({
        key: '<?php echo $this->publishable; ?>',
        isTokenGenerated: false,
        //image: '/img/documentation/checkout/marketplace.png',
        token: function (token) {
            handler.isTokenGenerated = true;
            $('#processingPaymentStripe').css("display", "block");
            sm4.core.request.send({
                url: '<?php echo $this->url(array('module' => 'sitegateway', 'controller' => 'payment', 'action' => 'payment'), "default"); ?>',
                method: 'post',
                data: {
                    format: 'json',
                    stripeToken: token.id,
                    product_id: '<?php echo $product->getIdentity(); ?>',
                    product_type: '<?php echo $product->getType(); ?>',
                    product_price: <?php echo $this->productPrice; ?>,
                    product_desc: '<?php echo json_encode($this->productDesc); ?>',
                    product_qty: '<?php echo $this->productQty; ?>',
                    productParentId: '<?php echo $this->productParentId; ?>',
                    allParams: <?php echo json_encode($allParams); ?>
                },
                success: function (responseJSON)
                {
                    window.location.href = '<?php echo $allParams['RETURNURL']; ?>' + '&customer_id=' + responseJSON.customer_id + '&subscription_id=' + responseJSON.subscription_id + '&charge_id=' + responseJSON.charge_id;
                }
            });
        }
    });

    $( document ).ready(function() {

        handler.open({
            name: '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', ''); ?>',
            description: '<?php echo json_encode($this->productDesc); ?>',
            amount: '<?php echo Engine_Api::_()->sitegateway()->getPrice($this->productPrice); ?>',
            currency: '<?php echo Engine_Api::_()->sitegateway()->getCurrency(); ?>',
            opened: function () {
                $('#processingPaymentStripe').css("display", "none");
            },
            closed: function () {
                if (!handler.isTokenGenerated) {
                    window.location = '<?php echo $this->url(array(), "default", true); ?>';
                }
            },
        });

    });
</script>
