<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2 class="fleft"><?php echo $this->translate('Directory / Pages Plugin'); ?></h2>
<?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/manageExtensions.tpl'; ?>

<?php if (count($this->navigation)) { ?>
  <div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php } ?>

<div>
  <?php echo $this->htmlLink(array('action' => 'index', 'reset' => false), $this->translate('Back to Manage Packages'), array('class' => 'icon_sitepage_admin_back buttonlink')) ?>
</div>

<br />
<div class="sitepage_pakage_form">
	<div class="settings">
	  <?php echo $this->form->render($this) ?>
	</div>
</div>

<script type="text/javascript">
  function setRenewBefore(){

    if($('duration-select').value=="forever"|| $('duration-select').value=="lifetime" || ($('recurrence-select').value!=="forever" && $('recurrence-select').value!=="lifetime")){
      $('renew-wrapper').setStyle('display', 'none');
      $('renew_before-wrapper').setStyle('display', 'none');
    }else{
      $('renew-wrapper').setStyle('display', 'block');
      if($('renew').checked)
        $('renew_before-wrapper').setStyle('display', 'block');
      else
        $('renew_before-wrapper').setStyle('display', 'none');
    }
  }
  $('duration-select').addEvent('change', function(){
    setRenewBefore();
  });

  //window.addEvent('domready', function() {
    //setRenewBefore();
  //});
</script>
<style type="text/css">
    
    #modules-sitepagemember {
        display:none;
    } 
    

    label[for="modules-sitepagemember"] { display:none; }
</style>
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