<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
    window.addEvent('domready', function () {
        showcommissionType();
    });

    function showcommissionType() {
        if (document.getElementById('commission_handling')) {
            if (document.getElementById('commission_handling').value == 1) {
                document.getElementById('commission_fee-wrapper').style.display = 'none';
                document.getElementById('commission_rate-wrapper').style.display = 'block';
            } else {
                document.getElementById('commission_fee-wrapper').style.display = 'block';
                document.getElementById('commission_rate-wrapper').style.display = 'none';
            }
        }
    }
</script>
<h2>
    <?php echo 'Crowdfunding / Fundraising / Donations Plugin'; ?>
</h2>

<?php if (count($this->navigation)) { ?>
    <div class='seaocore_admin_tabs clr'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php } ?>

<div>
    <?php echo $this->htmlLink(array('action' => 'manage', 'reset' => false), 'Back to Manage Packages', array('class' => 'icon_sitecrowdfunding_admin_back buttonlink')) ?>
</div>

<br />
<div class="sitecrowdfunding_pakage_form">
    <div class="settings">
        <?php echo $this->form->render($this) ?>
    </div>
</div>	

<script type="text/javascript">
    function setRenewBefore() {

        if ($('duration-select').value == "forever" || $('duration-select').value == "lifetime" || ($('recurrence-select').value !== "forever" && $('recurrence-select').value !== "lifetime")) {
            $('renew-wrapper').setStyle('display', 'none');
            $('renew_before-wrapper').setStyle('display', 'none');
        } else {
            $('renew-wrapper').setStyle('display', 'block');
            if ($('renew').checked)
                $('renew_before-wrapper').setStyle('display', 'block');
            else
                $('renew_before-wrapper').setStyle('display', 'none');
        }
    }
    $('duration-select').addEvent('change', function () {
        setRenewBefore();
    });
    window.addEvent('domready', function () {
        setRenewBefore();

        if ('<?php echo $this->package->video ?>' != 0) {
            $('video_count-wrapper').style.display = 'block';
        }
        else {
            $('video_count-wrapper').style.display = 'none';
        }

        if ('<?php echo $this->package->photo; ?>' != 0) {
            $('photo_count-wrapper').style.display = 'block';
        }
        else {
            $('photo_count-wrapper').style.display = 'none';
        }
    });
    function showVideoOption(optionValue) {
        if (optionValue == 0)
            $('video_count-wrapper').style.display = 'none';
        else
            $('video_count-wrapper').style.display = 'block';
    }

    function showPhotoOption(optionValue) {
        if (optionValue == 0)
            $('photo_count-wrapper').style.display = 'none';
        else
            $('photo_count-wrapper').style.display = 'block';
    }
</script>


<script type="text/javascript">
    var supportedBillingIndex;
    var gateways;
    var row = $('recurrence-element');
    var mySecondElement = new Element('div#recurrence-select-element');
    mySecondElement.inject(row);

    var displayBillingGateways = function () {

        var recurrence = $('recurrence-select').get('value');

        var has = [], hasNot = [];
        var supportString = '';
        mySecondElement.set('html', supportString);
        recurrence = recurrence.capitalize();
        gateways.each(function (title, id) {
            if (!supportedBillingIndex.has(title)) {
                hasNot.push(title);
            } else if (!supportedBillingIndex.get(title).contains(recurrence))
            {
                hasNot.push(title);
            } else {
                has.push(title);
            }
        });
        supportString = '<br />';
        otherMPGateways = [];
        if (hasNot.contains('MangoPay')) {
            otherMPGateways.push('MangoPay');
        }
        if (recurrence != 'Forever') {
            if (has.length > 0) {
                supportString += '<span class="billing-gateway-supported"><b>Supported Gateways</b> for this billing cycle: ' + has.join(", ") + '</span>';
            }
            if (has.length > 0 && hasNot.length > 0) {
                supportString += '<br /><br />';
            }
            if (hasNot.length > 0) {
                supportString += '<span class="billing-gateway-unsupported"><b>Unsupported Gateways</b> for this billing cycle: ' + hasNot.join(", ") + '</span>';
            }
        } else {
            hasNot.erase('MangoPay');
            supportString += '<span class="billing-gateway-supported"> <b>Supported Gateways</b> for this billing cycle: ' + hasNot.join(", ") + '</span>';
            supportString += '<br /><br /><span class="billing-gateway-unsupported"> <b>Unsupported Gateways</b> for this billing cycle: ' + otherMPGateways.join(", ") + '</span>';
        }
        supportString += '<br /><br /><span > <b>Note: </b> You can enable / disable gateways accordingly for your selected billing cycle.</span>';
        mySecondElement.set('html', supportString);
    }
    window.addEvent('load', function () {
        supportedBillingIndex = new Hash(<?php echo Zend_Json::encode($this->supportedBillingIndex) ?>);
        gateways = new Hash(<?php echo Zend_Json::encode($this->gateways) ?>); 
        displayBillingGateways();
    });

    String.prototype.capitalize = function () {
        return this.charAt(0).toUpperCase() + this.slice(1);
    }
</script>