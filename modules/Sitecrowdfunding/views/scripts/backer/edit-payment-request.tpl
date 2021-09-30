<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit-payment-request.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
    function gatewayEnable()
    {
        parent.window.location.href = '<?php echo $this->url(array('action' => 'payment-info', 'project_id' => $this->project_id), 'sitecrowdfunding_specific', true); ?>';
        parent.Smoothbox.close();
    }
</script>
<div class="global_form_popup sitecrowdfunding_dashbord_popup_form">
    <!-- IF ADMIN HAS DELETED THE PAYMENT REQUEST -->
    <?php if (!empty($this->sitecrowdfunding_payment_request_deleted)) : ?> 
        <div class="tip">
            <span>
                <?php echo $this->translate("Site administrator has deleted this payment request. So you can't edit this payment request."); ?>
                <div class='buttons'>
                    <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
                </div>
            </span>
        </div>
        <?php
        return;
    endif;
    ?>

    <!-- IF ADMIN HAS COMPLETED THE PAYMENT REQUEST -->
    <?php if (!empty($this->sitecrowdfunding_payment_request_completed)) : ?> 
        <div class="tip">
            <span>
                <?php echo $this->translate("Site administrator has completed this payment request. So you can't edit this payment request."); ?>
                <div class='buttons'>
                    <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
                </div>
            </span>
        </div>
        <?php
        return;
    endif;
    ?>

    <!-- IF ADMIN IS RESPONDING THE PAYMENT REQUEST -->
    <?php if (!empty($this->sitecrowdfunding_admin_responding_request)): ?>
        <div class="tip">
            <span>
                <?php echo $this->translate("Site administrator is responding your request. So you can't change your request."); ?>
                <div class='buttons'>
                    <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
                </div>
            </span>
        </div>
        <?php
        return;
    endif;
    ?>

    <!-- IF THERE IS NO PAYMENT GATEWAY ENABLE -->
    <?php if (!empty($this->gateway_disable)) : ?>
        <!-- IF EVENT ADMIN REQUEST FOR PAYMENT -->
        <?php if (empty($this->req_page_owner)) : ?>
            <div class="tip">
                <span>
                    <?php echo $this->translate("Project owner has not enabled any payment gateway. So you can\'t proceed for payment request. Please contact to project owner.") ?>
                    <div class='buttons'>
                        <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
                    </div>
                </span>
            </div>
            <?php
            return;
        endif;
        ?>

        <div class="tip">
            <span>
                <?php echo $this->translate('You have not enabled any payment gateway. Please first enable the payment gateway, then proceed for payment request. %1$sClick here%2$s to enable the payment gateway.', '<a href="javascript:void(0)" onclick="gatewayEnable();">', '</a>'); ?>
                <div class='buttons'>
                    <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
                </div>
            </span>
        </div>
        <?php
        return;
    endif;
    ?>

    <?php echo $this->form->render($this); ?>
</div>

