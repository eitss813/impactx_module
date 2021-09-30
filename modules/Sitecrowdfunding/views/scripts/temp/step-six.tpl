<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding_dashboard.css'); ?>

<div class="sitecrowdfunding_project_new_steps">


    <div class="sitecrowdfunding_dashboard_content">

        <?php if (empty($this->enablePaymentGateway)) : ?>
        <div>
            <?php if ($this->paymentMethod == 'paypal'): ?>
            <?php echo $this->paypalForm->render($this) ?>


            <?php elseif (Engine_Api::_()->hasModuleBootstrap('sitegateway') &&
            Engine_Api::_()->sitegateway()->isValidGateway($this->paymentMethod)): ?>

            <?php if ($this->paymentMethod == 'stripe' && Engine_Api::_()->getApi('settings',
            'core')->getSetting('sitegateway.stripeconnect', 0)): ?>
            <?php echo $this->partial('_stripeConnectButton.tpl', 'sitegateway', array('stripeConnected' =>
            $this->stripeConnected, 'show_stripe_form_massges' => false, 'resource_type' => 'sitecrowdfunding_project',
            'resource_id' => $this->project_id)); ?>
            <?php else: ?>
            <?php $formName = "form" . ucfirst($this->paymentMethod); ?>
            <?php echo $this->$formName->render($this) ?>
            <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <?php if (is_array($this->enablePaymentGateway) && count($this->enablePaymentGateway) >= 1) : ?>
        <div class="global_form">
            <!-- <h3><?php echo $this->translate('Payment Methods') ?></h3> -->
            <!--<p class="mtop5"><?php echo $this->translate("Below, you can choose the payment methods that you want to be available to backers during their backing process to Back this project."); ?></p>-->
        </div>
        <div id="project_payment_gateway" style="margin-top: 10px;"
             class="mbot10 sitecrowdfunding_seller_payment_options">
            <div id="project_payment_gateway_success_message"></div>
            <?php foreach ($this->enablePaymentGateway as $paymentGateway) : ?>
            <?php if ($paymentGateway == 'paypal') : ?>
            <?php $isPaypalEnable = true; ?>
            <div class="global_form_desc">
                <input type="checkbox" class="payment_method" id="sitecrowdfunding_gateway_paypal"
                       onchange="selectProjectPaymentGateway(this.id, this.checked);" <?php
                            if (!empty($this->paypalEnable)) : echo $this->translate("checked");
                endif;
                ?>>
                <label for="sitecrowdfunding_gateway_paypal"><?php echo $this->translate("PayPal") ?></label>
            </div>

            <div id="sitecrowdfunding_gateway_paypal_form" class="sitecrowdfunding_dashboard_payment_method b_medium"
            <?php
                        if (empty($this->paypalEnable)) : echo 'style="display:none"';
            endif;
            ?>>
            <?php echo $this->paypalForm->render($this) ?>
        </div>
        <?php endif; ?>
        <?php if (Engine_Api::_()->hasModuleBootstrap('sitegateway') &&
        Engine_Api::_()->sitegateway()->isValidGateway($paymentGateway)): ?>
        <?php
                        $gatewayName = strtolower($paymentGateway);
                        $gatewayNameUC = ucfirst($paymentGateway);
                        $gatewayVariableName = "is" . $gatewayNameUC . "Enable";
                        $formName = "form$gatewayNameUC";
                        $gatewyEnabled = $gatewayName . 'Enabled';
                        ?>
        <?php if ($paymentGateway == 'stripe' && Engine_Api::_()->sitegateway()->isValidGateway($paymentGateway) &&
        Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0)) : ?>
        <div class="global_form_desc">
            <input type="checkbox" class="payment_method" id="sitecrowdfunding_gateway_stripe"
                   onchange="selectProjectPaymentGateway(this.id, this.checked);" <?php
                                if (!empty($this->stripeEnabled)) : echo $this->translate("checked");
            endif;
            ?>>
            <label for="sitecrowdfunding_gateway_stripe"><?php echo $this->translate("Stripe") ?></label>
        </div>

        <div id="sitecrowdfunding_gateway_stripe_form" class="sitecrowdfunding_dashboard_payment_method b_medium"
        <?php
                            if (empty($this->stripeEnabled)) : echo 'style="display:none"';
        endif;
        ?> >
        <?php echo $this->partial('_stripeConnectButton.tpl', 'sitegateway', array('stripeConnected' =>
        $this->stripeConnected, 'show_stripe_form_massges' => true, 'resource_type' => 'sitecrowdfunding_project',
        'resource_id' => $this->project_id)); ?>
    </div>

    <?php elseif ($paymentGateway == 'mangopay' && Engine_Api::_()->sitegateway()->isValidGateway($paymentGateway)) : ?>
    <div class="global_form_desc">
        <input type="checkbox" class="payment_method" id="sitecrowdfunding_gateway_mangopay"
               onchange="selectProjectPaymentGateway(this.id, this.checked);" <?php
                                if (!empty($this->mangopayEnable)) : echo $this->translate("checked");
        endif;
        ?>>
        <label for="sitecrowdfunding_gateway_mangopay"><?php echo $this->translate("MangoPay") ?></label>
    </div>
    <div id="sitecrowdfunding_gateway_mangopay_form" class="sitecrowdfunding_dashboard_payment_method b_medium"
    <?php
                            if (empty($this->mangopayEnable)) : echo 'style="display:none"';
    endif;
    ?>>
    <?php echo $this->mangopayForm->render($this); ?>
    <?php echo $this->mangopayBankDetailForm->render($this); ?>
</div>
<?php else : ?>
<div class="global_form_desc">
    <input type="checkbox" class="payment_method" id="sitecrowdfunding_gateway_<?php echo $gatewayName; ?>"
           onchange="selectProjectPaymentGateway(this.id, this.checked);" <?php
                                if (!empty($this->$gatewyEnabled)) : echo $this->translate("checked");
    endif;
    ?>>
    <label for="sitecrowdfunding_gateway_<?php echo $gatewayName; ?>"><?php echo $this->translate($gatewayNameUC)
        ?></label>
</div>
<div id="sitecrowdfunding_gateway_<?php echo $gatewayName; ?>_form"
     class="sitecrowdfunding_dashboard_payment_method b_medium" <?php
                            if (empty($this->$gatewyEnabled)) : echo 'style="display:none"';
endif;
?>>
<?php echo $this->$formName->render($this) ?>
</div>
<?php endif; ?>
<?php endif; ?>
<?php endforeach; ?>
</div>
<div class='buttons' id="project_gateway_submit" style="display: block;">
    <button type='button' name="save_gateway" onclick="saveProjectGateway();"><?php echo $this->translate("Save") ?>
    </button>
    <span id="project_gateway_submit_spinner"></span>
</div>
<?php else: ?>
<div class="global_form">
    <div class='tip'>
        <h3><?php echo $this->translate('Payment Methods') ?></h3>
        <span>
                        <?php echo $this->translate("Site admin has not configured or enabled the payment gateways yet. Please, contact site admin to configure and enable payment gateways."); ?>
                    </span>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>

</div>
</div>
</div>

</div>


<style>
    .sitecrowdfunding_project_new_steps {
        min-width: 430px;
        max-width: 750px;
        padding: 20px;
        margin-left: auto;
        margin-right: auto;
        position: relative;
        border-radius: 3px;
        margin-bottom: 30px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.23);
        background: rgba(255, 255, 255, .9)
    }
</style>


<script lang="javascript/text">
    function changeMangoPayAccountType(accountType, onload) {
        $$('.optionalBankDetails').getParent().getParent().hide();
        if (!onload) {
            Object.each($$('.optionalBankDetails'), function (obj, index) {
                obj.value = "";
            });
        }
        switch (accountType) {

            case 'IBAN' :
                $('iban-wrapper').show();
                $('bic-wrapper').show();
                break;
            case 'GB' :
                $('sort_code-wrapper').show();
                $('account_number-wrapper').show();
                break;
            case 'US' :
                $('deposit_account_type-wrapper').show();
                $('aba-wrapper').show();
                $('us_account_number-wrapper').show();
                break;
            case 'CA' :
                $('branch_code-wrapper').show();
                $('bank_name-wrapper').show();
                $('institution_number-wrapper').show();
                $('ca_account_number-wrapper').show();
                break;
            case 'OTHER' :
                $('other_bic-wrapper').show();
                $('other_account_number-wrapper').show();
                break;
        }
    }
    function initateForm() {
        if ($('account_type')) {
            changeMangoPayAccountType($('account_type').value, true);
        }
        else {
            setTimeout(initateForm, 500);
        }
    }
    window.addEvent('domready', function () {
        initateForm();
        isPaymentMethodChecked();
    });
    function selectProjectPaymentGateway(elementId, elementValue)
    {
        if (elementValue)
            $(elementId + "_form").style.display = 'block';
        else
            $(elementId + "_form").style.display = 'none';

        isPaymentMethodChecked();
    }

    function isPaymentMethodChecked() {
        if (!$("project_gateway_submit")) {
            return;
        }
        if ($$(".payment_method:checked").length > 0) {
            $("project_gateway_submit").style.display = 'block';
        }
        else {
            $("project_gateway_submit").style.display = 'none';
        }
    }
    function saveProjectGateway() {
        selectedPaymentGateways = $$(".payment_method:checked");
        if (selectedPaymentGateways.length <= 0) {
            return false;
        }
        data = {};
        additionalGatewayDetailArray = {};
        Array.each(selectedPaymentGateways, function (item, index) {
            switch (item.id) {
                case 'sitecrowdfunding_gateway_paypal' :
                    data.paypal = $("sitecrowdfunding_payment_info").toQueryString();
                    break;
                case 'sitecrowdfunding_gateway_mangopay' :
                    data.mangopay = $("sitecrowdfunding_mangopay_payment_info").toQueryString();
                    data.mangopayBankDetail = $("sitecrowdfunding_mangopay_bank_detail").toQueryString();
                    break;
                default :
                    if ($(item.id)) {
                        gt = (item.id).toString().split("_");
                        gatewayName = gt[gt.length - 1];
                        formId = "sitecrowdfunding_payment_info_" + gatewayName;
                        if ($(formId)) {
                            additionalGatewayDetailArray[gatewayName] = $(formId).toQueryString();
                        }
                    }
            }
        });

        request = new Request.JSON({
            url: en4.core.baseUrl + 'sitecrowdfunding/project/set-project-gateway-info',
            method: 'POST',
            data: {
                format: 'json',
                data: JSON.encode(data),
                additionalGatewayDetailArray: additionalGatewayDetailArray,
                project_id: '<?php echo $this->project_id ?>'
            },
            onRequest: function () {
                $('project_gateway_submit_spinner').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
                document.getElementById("project_payment_gateway_success_message").innerHTML = '';
                paypalMsg = document.getElementById('show_paypal_form_massges');
                if (paypalMsg) {
                    paypalMsg.innerHTML = '';
                }
                mangopayMsg = document.getElementById('show_mangopay_form_massges');
                if (mangopayMsg) {
                    mangopayMsg.innerHTML = '';
                }
                mangopayMsg = document.getElementById('show_mangopay_bank_detail_form_massges');
                if (mangopayMsg) {
                    mangopayMsg.innerHTML = '';
                }
            },
            onSuccess: function (responseJSON) {
                document.getElementById('project_gateway_submit_spinner').innerHTML = '';

                if (responseJSON.success_message) {
                    document.getElementById("project_payment_gateway_success_message").innerHTML = '<ul class="form-notices"><li>' + responseJSON.success_message + '</li></ul>';
                }
                // SHOQ MANGOPAY ERROR MESSAGE , IF ANY
                if (responseJSON.mangopay_error) {
                    document.getElementById('show_mangopay_form_massges').innerHTML = responseJSON.mangopay_message;
                }
                if (responseJSON.mangopay_bankDetail_error) {
                    document.getElementById('show_mangopay_bank_detail_form_massges').innerHTML = responseJSON.mangopay_bankDetail_message;
                }
                // SHOW PAYPAL ERROR MESSAGE, IF ANY
                display_error = true;
                TempStr = "";
                if (responseJSON.email_error) {
                    TempStr = '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.email_error + '</li></ul></li>';
                    TempStr += '</ul>';
                }

                if (responseJSON.error_message) {
                    TempStr += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.error_message + '</li></ul></li></ul>';
                }
                if (TempStr) {
                    document.getElementById('show_paypal_form_massges').innerHTML = TempStr;
                }
            <?php foreach ($this->enablePaymentGateway as $paymentGateway) : ?>
            <?php if (Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->sitegateway()->isValidGateway($paymentGateway)): ?>
            <?php $gatewayName = strtolower($paymentGateway); ?>

                if (responseJSON.<?php echo $gatewayName ?>_info_error) {
                    window['<?php echo $gatewayName ?>' + 'TempStr'] = '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.<?php echo $gatewayName ?>_info_error + '</li></ul></li></ul>';
                }

                if (responseJSON.error_message_<?php echo $gatewayName ?>) {
                    window['<?php echo $gatewayName ?>' + 'TempStr'] = '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.error_message_<?php echo $gatewayName ?> + '</li></ul></li></ul>';
                }

                if (window['<?php echo $gatewayName ?>' + 'TempStr'] && document.getElementById('show_<?php echo $gatewayName; ?>_form_massges')) {
                    document.getElementById('show_<?php echo $gatewayName; ?>_form_massges').innerHTML = window['<?php echo $gatewayName ?>' + 'TempStr'];
                }
            <?php endif; ?>
            <?php endforeach; ?>

            }
        });
        request.send();

    }
    en4.core.runonce.add(function () {
    <?php if (empty($this->enablePaymentGateway)) : ?>

    <?php $paymentGateway = $this->paymentMethod; ?>
    <?php if ($paymentGateway != 'paypal' && Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->sitegateway()->isValidGateway($paymentGateway)): ?>
    <?php $gatewayName = strtolower($paymentGateway); ?>

        if (document.getElementById('sitecrowdfunding_payment_info_<?php echo $gatewayName ?>')) {
            document.getElementById('sitecrowdfunding_payment_info_<?php echo $gatewayName ?>').addEvent('submit', function (e) {
                e.stop();
                var TempStr = '';
                var enabled = 0;
                var display_error = true;
                if (document.getElementById('enabled-1').checked) {
                    enabled = 1;
                }
                document.getElementById('show_<?php echo $gatewayName ?>_form_massges').innerHTML = '';
                document.getElementById('spiner-image').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitecrowdfunding/externals/images/loading.gif" /></center>';
                new Request.JSON({
                    url: en4.core.baseUrl + 'sitecrowdfunding/project/set-payment-info-additional-gateway',
                    method: 'POST',
                    data: {
                        format: 'json',
                        gatewayName: '<?php echo $gatewayName ?>',
                        gatewayCredentials: $("sitecrowdfunding_payment_info_<?php echo $gatewayName; ?>").toQueryString(),
                        enabled: enabled,
                        project_id: <?php echo $this->project_id ?>
            },
                onSuccess: function (responseJSON) {

                    document.getElementById('spiner-image').innerHTML = '';

                    if (responseJSON.success_message) {
                        TempStr += '<ul class="form-notices"><li>' + responseJSON.success_message + '</li></ul>';
                    }

                    if (responseJSON.<?php echo $gatewayName ?>_info_error) {
                        display_error = false;
                        TempStr += '<li><ul class="error"><li>' + responseJSON.<?php echo $gatewayName ?>_info_error + '</li></ul></li></ul>';
                    }
                else
                    {
                        TempStr += '</ul>'
                    }


                    if (display_error == true)
                    {
                        if (responseJSON.<?php echo $gatewayName ?>_info_error) {
                        TempStr += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.<?php echo $gatewayName ?>_info_error + '</li></ul></li></ul>';
                    }
                    }

                    if (responseJSON.error_message) {
                        TempStr += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.error_message + '</li></ul></li></ul>';
                    }

                    document.getElementById('show_<?php echo $gatewayName ?>_form_massges').innerHTML = TempStr;
                }

            }).send();

            });
        }
    <?php endif; ?>

        if (document.getElementById('sitecrowdfunding_payment_info')) {
            document.getElementById('sitecrowdfunding_payment_info').addEvent('submit', function (e) {
                e.stop();
                var TempStr = '';
                var enabled = 0;
                var display_error = true;
                if (document.getElementById('enabled-1').checked) {
                    enabled = 1;
                }
                document.getElementById('show_paypal_form_massges').innerHTML = '';
                document.getElementById('spiner-image').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitecrowdfunding/externals/images/loading.gif" /></center>';
                new Request.JSON({
                    url: en4.core.baseUrl + 'sitecrowdfunding/project/set-payment-info',
                    method: 'POST',
                    data: {
                        format: 'json',
                        username: document.getElementById('username').value,
                        password: document.getElementById('password').value,
                        signature: document.getElementById('signature').value,
                        email: document.getElementById('email').value,
                        enabled: enabled,
                        project_id: <?php echo $this->project_id ?>
            },
                onSuccess: function (responseJSON) {
                    document.getElementById('spiner-image').innerHTML = '';
                    if (responseJSON.success_message) {
                        TempStr += '<ul class="form-notices"><li>' + responseJSON.success_message + '</li></ul>';
                    }

                    if (responseJSON.email_error) {
                        TempStr += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.email_error + '</li></ul></li>';

                        if (responseJSON.paypal_info_error) {
                            display_error = false;
                            TempStr += '<li><ul class="error"><li>' + responseJSON.paypal_info_error + '</li></ul></li></ul>';
                        }
                        else
                        {
                            TempStr += '</ul>'
                        }
                    }

                    if (display_error == true)
                    {
                        if (responseJSON.paypal_info_error) {
                            TempStr += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.paypal_info_error + '</li></ul></li></ul>';
                        }
                    }

                    if (responseJSON.error_message) {
                        TempStr += '<ul class="form-errors"><li><ul class="error"><li>' + responseJSON.error_message + '</li></ul></li></ul>';
                    }

                    document.getElementById('show_paypal_form_massges').innerHTML = TempStr;
                }

            }).send();

            });
        }
    <?php endif; ?>
    });
</script>
