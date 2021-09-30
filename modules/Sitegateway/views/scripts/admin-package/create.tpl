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

<div class="settings">
    <?php echo $this->form->render($this) ?>
</div>
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
        $('recurrence-select').addEvent('change', displayBillingGateways);
        displayBillingGateways();
    });

    String.prototype.capitalize = function () {
        return this.charAt(0).toUpperCase() + this.slice(1);
    }
</script>
