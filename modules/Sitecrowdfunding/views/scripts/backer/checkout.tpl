<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: checkout.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl(); ?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
?>
<div class="payment_information">
    <div class="Sitecrowdfunding_title_div_wrapper">
        <h3>
            <?php echo $this->translate($this->project->getTitle()); ?>
        </h3> 
    </div>
    <br />
    <div class="reward_choose">
        <?php echo $this->translate("Payment Information "); ?>
    </div>
    <?php
    $countries = array();
    if (!empty($this->reward)) {
        $countries = $this->reward->getAllCountries();
    }
    if (count($countries) > 0):
        ?>
        <div class="sitecrowdfunding_billing_address"><?= $this->translate('Shipping Address') ?> </div>
        <form name="form_address" id="form_address">
            <span id="address_required_error" class="seaocore_txt_red f_small"></span>
            <div class="form-label">
                <label class="required"><?= $this->translate('Country') ?></label>
                <select name="country" disabled>
                    <?php foreach ($countries as $country): ?>
                        <?php
                        $name = empty($country->country) ? $this->translate('Rest of the World') : $country->country_name;
                        $regionId = empty($country->region_id) ? 0 : $country->region_id;
                        ?>
                        <option value="<?= $regionId ?>" <?= ($regionId == $this->session->country) ? 'selected' : '' ?> ><?= $name ?> </option>
                    <?php endforeach; ?>
                </select><br />
                <input type="hidden" name="regionId" value="<?php echo $this->session->country; ?>">
                <label class="required">Address1</label>
                <input class="address_field" type="text" name="address1"  /><br />
                <label>Address2</label>
                <input class="address_field" type="text" name="address2"  /><br />
                <label class="required">City</label>
                <input type="text" name="city"  /><br />
                <label class="required">Postal Code</label>
                <input class="address_field" type="text" name="postal_code"  />
            </div>
        </form>
    <?php endif; ?>

    <?php
// IF NO PAYMENT GATEWAY ENABLE BY THE SITEADMIN
    if (!empty($this->sitecrowdfunding_checkout_no_payment_gateway_enable)):
        ?>
        <div class="tip">
            <span>
                <?php echo $this->translate("Site admin has not configured or enabled the payment gateways yet. Please, contact site admin to configure and enable payment gateways."); ?>
            </span>
        </div> 
        <?php
        return;
    endif;
    ?>
</div>
<section class="sitecrowdfunding_checkout_process_form mtop10 o_hidden">
    <div>
        <h3 class="sitecrowdfunding_checkout_process_normal mbot10">
            <?php echo $this->translate('Payment Method'); ?>
        </h3>
    </div>

    <?php
    $temp_online_gateway = false;
    ?>
    <?php $base_url = $this->layout()->staticBaseUrl; ?>
    <div class="sitecrowdfunding_payment_methods_wrap">
        <?php
        $paymentMethodCount = COUNT($this->payment_gateway);
        $handTip = '';
        if ($paymentMethodCount == 1) :
            $handTip = 'style="cursor:default;"';
        endif;
        ?>
        <?php
        $otherPaymentGateways = array();
        if (!isset($this->payment_gateway)):
            return;
        else:
            foreach ($this->payment_gateway as $payment_method) :

                if (count($this->payment_gateway) == 1):
                    $selected = "checked = checked style='display:none'";
                else:
                    $selected = "";
                endif;
                if (!isset($payment_method['plugin'])) {
                    continue;
                }
                $pluginName = $payment_method['plugin'];
                $paymentGateway = Engine_Api::_()->sitecrowdfunding()->getPaymentGateway($pluginName);
                if (!$paymentGateway) {
                    continue;
                }
                $otherPaymentGateways[] = $paymentGatewayId = $paymentGateway->gateway_id;
                if ((isset($payment_method['plugin']) && $payment_method['plugin'] === 'Payment_Plugin_Gateway_PayPal') || ($payment_method == 'paypal')) {
        echo '<div class="sitecrowdfunding_payment_method"><input id="paypal" type="radio" name="payment_method" value="' . $paymentGatewayId . '" ' . $selected . ' onchange="paymentMethod(this.value)"><label ' . $handTip . ' for="paypal" class="mbot5"><img src="' . $base_url . 'application/modules/Sitecrowdfunding/externals/images/paypal.png" title="PayPal" /> / <span style="font-weight: bold">Credit Card</span> / <span style="font-weight: bold">Debit Card</span></label>';
                    echo '<input type="hidden" id="payment_gateway_name_' . $paymentGatewayId . '" value="PayPal" >
                    <span id="payment_method_message_' . $paymentGatewayId . '" class="mbot5 pleft10">&nbsp;&nbsp;' . $this->translate("You will be redirected to securely make payment to donate this project using the selected payment gateway.") . '</span></div>';
                }
                if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
                    if (($payment_method['plugin'] === 'Sitegateway_Plugin_Gateway_Stripe' || $payment_method['plugin'] === 'Sitegateway_Plugin_Gateway_MangoPay')) {
                        $paymentGatewayTitle = strtolower($paymentGateway->title);
                        $paymentGatewayTitleUC = ucfirst($paymentGateway->title);

                        echo '<div class="sitecrowdfunding_payment_method"><input id="' . $paymentGatewayTitle . '" type="radio" name="payment_method" value="' . $paymentGatewayId . '" ' . $selected . ' onchange="paymentMethod(this.value)"><label ' . $handTip . ' for="' . $paymentGatewayTitle . '" class="mbot5"><img src="' . $base_url . 'application/modules/Sitegateway/externals/images/' . $paymentGatewayTitle . '.png" title="' . $paymentGatewayTitleUC . '" /></label>';
                        echo '<input type="hidden" id="payment_gateway_name_' . $paymentGatewayId . '" value="' . $paymentGatewayTitleUC . '"><span id="payment_method_message_' . $paymentGatewayId . '" class="mbot5">' . $this->translate("You will be redirected to securely make payment to donate this project using the selected payment gateway.") . '</span></div>';
                    }
                }
            endforeach;
        endif;
        ?>
        <div> <span id="payment_method_missing" class="seaocore_txt_red f_small"></span> </div>
    </div>
    <div class="private_backing">
        <input type="checkbox" id="isPrivateBacking" name="isPrivateBacking"><label for="isPrivateBacking"><?php echo $this->translate("Make my donation private.") ?></label>
    </div>
    <div class="clr">
        <div id="checkout_place_order_error"></div>
        <div class='buttons'>

            <div class="mtop10 fleft">
                <button type="button" name="place_order" onclick="window.location.href = '<?php echo $this->url(array("action" => "reward-selection", 'project_id' => $this->project_id), "sitecrowdfunding_backer", true) ?>';" class="fright seaocore_back_icon"><?php echo $this->translate("Back") ?></button>
            </div>    
            <div class="fright m10">  
                <button type="button" name="place_order" onclick="paymentInformation()" class="fright"><?php echo $this->translate("Donate") ?></button>
                <div id="loading_image_5" class="fright m10" style="display: inline-block;"></div>
            </div>
            <div id="loading_image_4" class="fright mtop10 ptop10" style="display: inline-block;"></div>
        </div>
    </div>
</section>
<script>
    var otherPaymentGateways = '<?php echo (!empty($otherPaymentGateways) ? json_encode($otherPaymentGateways) : '[]'); ?>';
    var index;
    for (index = 0; index < otherPaymentGateways.length; index++) {
        if ($('payment_method_message_' + otherPaymentGateways[index]))
            new Fx.Slide('payment_method_message_' + otherPaymentGateways[index]).hide();
    }
    if ($$('input[name=payment_method]:checked').get('value'))
        paymentMethod($$('input[name=payment_method]:checked').get('value'));

    $$('.address_field').addEvent('click', function(){ $('address_required_error').innerHTML = '';});

    function paymentMethod(payment_method_value)
    {
        $('payment_method_missing').innerHTML = '';
        var index;
        for (index = 0; index < otherPaymentGateways.length; index++) {
            if (payment_method_value == otherPaymentGateways[index])
                new Fx.Slide('payment_method_message_' + otherPaymentGateways[index], {mode: 'vertical', resetHeight: true}).slideIn().toggle();
            else if (document.getElementById('payment_method_message_' + otherPaymentGateways[index]))
                new Fx.Slide('payment_method_message_' + otherPaymentGateways[index]).slideOut().toggle();
        }
    }

    function paymentInformation()
    {
        var form = $('form_address');
        if(form && (form.address1.value == "" || form.city.value == "" || form.postal_code.value == "")) { 
            $('address_required_error').innerHTML = '<?php echo $this->translate("Please fill required fields") ?>';
            return;
        }
        var payment_method = $$('input[name=payment_method]:checked').get('value');

        //If viewer not select any payment method then show error message.
        if (payment_method.length == 0)
        {
            $('payment_method_missing').innerHTML = '<?php echo $this->translate("Please choose a payment method.") ?>';
            return;
        }
        var checkout_process_payment_gateway = '<?php echo $this->translate("Gateway: ") ?>' + $('payment_gateway_name_' + payment_method).value + '<br />';
        sitecrowdfunding_checkout_process_payment_information = checkout_process_payment_gateway;
        addressDetail = '';
        if (form) {
            addressDetail = form.toQueryString()
        }
        placeOrder(String(payment_method), addressDetail);
    }

    function placeOrder(param, addressDetail)
    {
        var isPrivateBacking = 0;
        if ($('isPrivateBacking') && $('isPrivateBacking').checked)
          isPrivateBacking = 1;
        var placeOrderUrl;
        placeOrderUrl = "sitecrowdfunding/backer/place-order/project_id/<?php echo $this->project_id ?>";
        en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + placeOrderUrl,
            method: 'POST',
            onRequest: function () {
                $('loading_image_5').innerHTML = '<img src=' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif height=15 width=15>';
            },
            data: {
                format: 'json',
                param: param,
                formValues: addressDetail,
                isPrivateBacking: isPrivateBacking,
            },
            onSuccess: function (responseJSON)
            {
                $('loading_image_5').innerHTML = '';
<?php $payment_url = $this->url(array('action' => 'payment', 'project_id' => $this->project_id), 'sitecrowdfunding_backer', true) ?>
                window.location = '<?php echo $payment_url ?>/gateway_id/' + responseJSON.gateway_id + '/backer_id/' + responseJSON.backer_id;
            }
        })
                );
    }

</script>