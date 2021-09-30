<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    _stripeConnectButton.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h3><?php echo $this->translate("Stripe Connect"); ?></h3>
<div id="show_stripe_form_massges"></div>
<?php if ($this->stripeConnected): ?>
    <?php if ($this->show_stripe_form_massges): ?>
        <div id="show_stripe_form_massges"></div>
    <?php endif; ?>

    <?php
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();
    ?>
    <?php $showTipMessage = false;?>    
    <?php if (isset($params['stripe-connect'])): ?>
        <?php if (!empty($params['stripe-connect'])): ?>
            <ul class="form-notices">
                <li><?php echo $this->translate("You have connected your Stripe account successfully."); ?></li>
            </ul>
             <?php $showTipMessage = true;?>
        <?php else: ?>
            <ul class="form-errors">
                <li><?php echo $this->translate("Unable to connect."); ?></li>
            </ul>
        <?php endif; ?>
    <?php endif; ?>    

    <div class="tip">
        <span>
            <?php if(!$showTipMessage): ?>
                <?php echo $this->translate("You are already connected with your Stripe account."); ?>
                <br/>
            <?php endif; ?>
            <?php echo $this->translate("You can reconnect with different Stripe account or re-configure your current Stripe account by clicking %s.", "<a href='javascript:void(0)' onclick='javascript:openStripeConnect()'>here</a>"); ?>
        </span>
    </div>
<?php else: ?>        
    <div class="tip">
        <span>
            <?php echo $this->translate("You are not yet connected with Stripe. To get started, click the 'Connect with Stripe' button and sign up."); ?>
        </span>
    </div>        
        
    <div class="mbot10">
        <a class="stripe_connect_button" href="javascript:void(0);" onclick="javascript:callStripeConnect();"></a>
    </div>        
<?php endif; ?>

<script type="text/javascript">
    function callStripeConnect() {
        <?php $redirectURL = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sitegateway', 'controller' => 'payment', 'action' => 'stripe-connect'), 'default', true); ?>
        var request = new Request.JSON({
            url: '<?php echo $this->url(array('module' => 'sitegateway', 'controller' => 'payment', 'action' => 'o-auth-process'), "default"); ?>',
            method: 'post',
            data: {
                format: 'json',
                resource_type: '<?php echo $this->resource_type; ?>',
                resource_id: '<?php echo $this->resource_id; ?>'
            },
            //responseTree, responseElements, responseHTML, responseJavaScript
            onSuccess: function (responseJSON) {
                var client_id = responseJSON.client_id;
                window.location.href = "https://connect.stripe.com/oauth/authorize?response_type=code&client_id=" + client_id + "&scope=read_write" + '&redirect_uri=<?php echo $redirectURL;?>';
            }
        });
        request.send();
    }
    
    function openStripeConnect() {
        Smoothbox.open('<div class="global_form_popup"><h3 class="mbot15">'+'<?php echo $this->translate('Stripe Connect');?>'+'</h3><div class="mbot10"><a class="stripe_connect_button" href="javascript:void(0);" onclick="javascript:callStripeConnect();"></a></div><button onclick="parent.Smoothbox.close();">Close</button></div>');
    }
</script>