<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: reward-selection.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js');
?>

<div class="sitecrowdfunding_dashboard_content">
    <div class="layout_middle">
        <div class="sitecrowdfunding_dashboard_content">
            <div id="show_tab_content">
                <div class="global_form">

                    <!-- project information-->
                    <div class="generic_list_wrapper">
                        <ul class="generic_list_widget">
                            <li class="inner_container">
                                <div class="photo">
                                    <?php echo $this->htmlLink($this->project->getHref(), $this->itemPhoto($this->project, 'thumb.cover'), array('class' => 'thumb')) ?>
                                </div>
                                <div class="fright" id="fright">
                                    <?php echo $this->htmlLink($this->project->getHref(), $this->translate('View this Project'), array("class" =>'view_project_btn button' , 'target' => '_blank')) ?>
                                </div>
                                <div class="info">
                                    <div class="title">
                                        <?php echo $this->htmlLink($this->project->getHref(), $this->project->getTitle()) ?>
                                    </div>

                                    <div class="owner">
                                        <?php echo $this->translate('by %1$s', $this->htmlLink($this->owner->getHref(), $this->owner->getTitle())); ?>
                                    </div>

                                    <?php if(!empty($this->parentOrganization['title']) ): ?>
                                    <div class="owner">
                                        <a title="<?php echo $this->parentOrganization['title'];?>" href="<?php echo !empty($this->parentOrganization['link']) ? $this->parentOrganization['link'] : 'javascript:void(0);'  ?>" >
                                            <b>Organisation: </b><?php echo $this->parentOrganization['title']; ?>
                                        </a>
                                    </div>
                                    <?php endif; ?>

                                    <?php if( !empty($this->initiative['initiative_id']) && !empty($this->parentOrganization['page_id']) ):?>
                                    <div class="owner">
                                        <?php $initiativesURL = $this->url(array('action' => 'landing-page','page_id' => $this->parentOrganization['page_id'], 'initiative_id' => $this->initiative['initiative_id']), "sitepage_initiatives");?>
                                        <a title="<?php echo $initiative['title'];?>"  href="<?php echo !empty($initiativesURL) ? $initiativesURL : 'javascript:void(0);'  ?>" >
                                            <b>Initiative: </b><?php echo $this->initiative['title']; ?>
                                        </a>
                                    </div>
                                    <?php endif; ?>

                                </div>
                            </li>
                        </ul>
                    </div>

                    <br/>

                    <!-- form-->
                    <div class="donate_form_container">
                        <form class="donate_form" method="post" id="donate_form" >
                            <h3 class="donate_form_title">
                                <?php echo $this->payment_action_label;  ?>
                            </h3>
                            <br/>
                            <div id="div_message" class="seaocore_txt_red"></div>
                            <div class="form-elements">
                                <div id="div_error_message" class="seaocore_txt_red"></div>

                                <input type="hidden" name="project_id" id="project_id" value="<?php echo $this->project_id ?>"/>

                                <input type="hidden" name="sourceUrl" id="sourceUrl" value="<?php echo $this->sourceUrl ?>"/>

                                <input type="hidden" name="donationType" id="donationType" value="<?php echo $this->donationType ?>"/>

                                <div id="pledge_amount-wrapper" class="form-wrapper">
                                    <div id="pledge_amount-label" class="form-label">
                                        <label for="title" class="required">Amount in USD($)</label>
                                    </div>
                                    <div id="pledge_amount-element" class="form-element">
                                        <input type="text" placeholder="Enter amount in USD($)" name="pledge_amount" id="pledge_amount" value="">
                                    </div>
                                </div>

                                <div id="pledge_message-wrapper" class="form-wrapper">
                                    <div id="pledge_message-label" class="form-label">
                                        <label for="title" class="required">Post Message</label>
                                    </div>
                                    <div id="pledge_message-element" class="form-element">
                                        <textarea name="pledge_message" id="pledge_message">Hi Everyone, Please join me in funding this project!</textarea>
                                    </div>
                                </div>

                                <?php $temp_online_gateway = false;?>

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
                                                        echo '<div class="sitecrowdfunding_payment_method"><input id="paypal" type="radio" name="payment_method" value="' . $paymentGatewayId . '" ' . $selected . ' onchange="paymentMethod(this.value)"><label ' . $handTip . ' for="paypal" class="mbot5"><img src="' . $base_url . 'application/modules/Sitecrowdfunding/externals/images/paypal.png" title="PayPal" /> </label>';
                                                        echo '<input type="hidden" id="payment_gateway_name_' . $paymentGatewayId . '" value="PayPal" >
                                                        <span id="payment_method_message_' . $paymentGatewayId . '" class="mbot5 pleft10">&nbsp;<br/>' . $this->translate("You will be redirected to securely make the payment. ") . '</span></div>';
                                                }

                                                if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
                                                    if (($payment_method['plugin'] === 'Sitegateway_Plugin_Gateway_Stripe' || $payment_method['plugin'] === 'Sitegateway_Plugin_Gateway_MangoPay')) {
                                                        $paymentGatewayTitle = strtolower($paymentGateway->title);
                                                        $paymentGatewayTitleUC = ucfirst($paymentGateway->title);

                                                        echo '<div class="sitecrowdfunding_payment_method"><input id="' . $paymentGatewayTitle . '" type="radio" name="payment_method" value="' . $paymentGatewayId . '" ' . $selected . ' onchange="paymentMethod(this.value)"><label ' . $handTip . ' for="' . $paymentGatewayTitle . '" class="mbot5"><img src="' . $base_url . 'application/modules/Sitegateway/externals/images/' . $paymentGatewayTitle . '.png" title="' . $paymentGatewayTitleUC . '" /></label> (Debit and Credit cards)';
                                                        echo '<input type="hidden" id="payment_gateway_name_' . $paymentGatewayId . '" value="' . $paymentGatewayTitleUC . '"><span id="payment_method_message_' . $paymentGatewayId . '" class="mbot5"><br/>' . $this->translate("You will be redirected to securely make the payment.") . '</span></div>';
                                                    }
                                                }
                                            endforeach;
                                        endif;
                                    ?>
                                </div>

                                <input type="checkbox" id="isPrivateBacking" name="isPrivateBacking"><label for="isPrivateBacking"><?php echo $this->translate("Make my funding private.") ?></label>
                                <br/><br/>

                                <?php if (!empty($this->sitecrowdfunding_checkout_no_payment_gateway_enable)): ?>
                                    <div class="tip">
                                            <span>
                                                <?php echo $this->translate("Site admin has not configured or enabled the payment gateways yet. Please, contact site admin to configure and enable payment gateways."); ?>
                                            </span>
                                    </div>
                                <?php else: ?>
                                    <div id="loading_image_5"></div>
                                    <div id="buttons-wrapper" class="form-wrapper">
                                        <div id="buttons-element" class="form-element">
                                            <button name="execute" id="execute" type="button" onclick="submitDonate()"><?php echo $this->payment_action_label; ?></button>

                                            <button name="execute" id="execute" type="button" onclick="cancelDonate()">Cancel</button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    var otherPaymentGateways = '<?php echo (!empty($otherPaymentGateways) ? json_encode($otherPaymentGateways) : '[]'); ?>';
    var index;
    for (index = 0; index < otherPaymentGateways.length; index++) {
        if ($('payment_method_message_' + otherPaymentGateways[index])){
            new Fx.Slide('payment_method_message_' + otherPaymentGateways[index]).hide();
        }
    }
    if ($$('input[name=payment_method]:checked').get('value')){
        paymentMethod($$('input[name=payment_method]:checked').get('value'));
    }

    // submit function
    function submitDonate() {
        var isValidatedYn = true;
        $('div_message').innerHTML = '';
        var rewardId = 0;
        var shipping_amt = 0;
        var country = "";
        var pledge_amount = Number($('pledge_amount').value);
        var pledge_message = $('pledge_message').value;
        var project_id = $('project_id').value;
        var sourceUrl = $('sourceUrl').value;
        var donationType = $('donationType').value;
        if (isNaN(Number(pledge_amount))) {
            $('div_message').innerHTML = 'Please enter the valid amount '+ pledge_amount;
            isValidatedYn =  false;
        }
        if (isNaN(pledge_amount) || pledge_amount <= 0) {
            $('div_message').innerHTML = 'Please enter the valid amount '+ pledge_amount;
            isValidatedYn =  false;
        }

        if(isValidatedYn === true){
            var data = {
                project_id:project_id,
                reward_id:rewardId,
                country:country,
                message: pledge_message,
                pledge_amount: pledge_amount,
                shipping_amt:shipping_amt,
                sourceUrl:sourceUrl,
                donationType:donationType
            };
            paymentInformation(data);
        }
    }

    // cancel donate
    function cancelDonate() {
        var project_url = '<?php echo $this->project->getHref(); ?>';
        window.location = project_url;
    }

    function paymentMethod(payment_method_value){
        $('div_error_message').innerHTML = '';
        var index;
        for (index = 0; index < otherPaymentGateways.length; index++) {
            if (payment_method_value == otherPaymentGateways[index])
                new Fx.Slide('payment_method_message_' + otherPaymentGateways[index], {mode: 'vertical', resetHeight: true}).slideIn().toggle();
            else if (document.getElementById('payment_method_message_' + otherPaymentGateways[index]))
                new Fx.Slide('payment_method_message_' + otherPaymentGateways[index]).slideOut().toggle();
        }
    }

    function paymentInformation(data){
        var payment_method = $$('input[name=payment_method]:checked').get('value');
        //If viewer not select any payment method then show error message.
        if (payment_method.length == 0){
            $('div_error_message').innerHTML = '<?php echo $this->translate("Please choose a payment method.") ?>';
        }else{
            storeDataInSession(data,String(payment_method));
        }
    }

    function storeDataInSession(data,payment_method){
        var save_session_url = '<?php echo $this->url(array('action' => 'save-cart-data-as-session', 'project_id' => $this->project_id), 'sitecrowdfunding_backer', true) ?>';
        new Request.JSON({
            format: 'json',
            method: 'post',
            url: save_session_url,
            data: data,
            onRequest: function () {
                $('loading_image_5').innerHTML = '<img src=' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif height=15 width=15>';
            },
            onSuccess: function (responseJSON) {
                $('loading_image_5').innerHTML = '';
                if (responseJSON.return == 1) {
                    placeOrder(String(payment_method), '');
                }
            }
        }).send();
    }

    function placeOrder(param, addressDetail){
        var isPrivateBacking = 0;
        if ($('isPrivateBacking') && $('isPrivateBacking').checked){
            isPrivateBacking = 1;
        }
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
                onSuccess: function (responseJSON){
                    $('loading_image_5').innerHTML = '';
                    <?php $payment_url = $this->url(array('action' => 'payment', 'project_id' => $this->project_id), 'sitecrowdfunding_backer', true) ?>
                    window.location = '<?php echo $payment_url ?>/gateway_id/' + responseJSON.gateway_id + '/backer_id/' + responseJSON.backer_id;
                }
            })
        );
    }

</script>

<style>
    ul.generic_list_widget a.thumb > img{
        width: 100px !important;
        height: 100px !important;
    }
    .title > a{
        color: #44AEC1;
        font-size: 18px !important;
        margin-bottom: 5px;
        font-weight: normal;
    }
    .owner {
        font-size: 14px !important;
        margin: 8px 0px;
    }
    .donate_form{
        margin: 0 25%;
    }
    .donate_form_title{
        font-size: 25px;
        text-align: center;
    }
    #buttons-element,#loading_image_5{
        text-align: center;
    }
    .view_project_btn {
        margin-top: 5px;
        margin-bottom: 5px;
        padding: 7px !important;
        border-radius: 3px !important;
        font-size: 14px !important;
    }
    a.button{
        font-weight: unset !important;
    }
    #fright {
        margin-top: 6px;
    }
    @media screen and (max-width:767px) {
        .inner_container {
            display: flex;
            flex-wrap: wrap;
        }

        .info .title {
            margin-top: 12px;
        }
        button#execute {
            margin-top: 7px;
        }
        .fright {
            /* float: right !important; */
            /* margin-left: 7px; */
            position: relative;
            left: 3px;
        }
    }
</style>