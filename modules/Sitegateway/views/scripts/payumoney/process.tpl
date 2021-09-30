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

<?php 
  
  $allParams = $this->allParams; 
  $action=$allParams['url'];

?>
<div id="processingPayment">
    <div>
        <center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/loading.gif" /></center>
    </div>
    <div id="LoadingImage" style="text-align:center;margin-top:15px;font-size:17px;">  
        <?php echo $this->translate("Processing Request. Please wait .....") ?>
    </div>
</div>

<form action="<?php echo $action ?>" method="POST" name="payuForm" id="payuForm">
    <input type="hidden" name="key" value="<?php echo $allParams['key'] ?>" />
    <input type="hidden" name="hash" value="<?php echo $allParams['hash'] ?>"/>
    <input type="hidden" name="txnid" value="<?php echo $allParams['txnid'] ?>" />
    <input type="hidden" name="amount"  value="<?php echo $allParams['amount'] ?>" />
    <input type="hidden" name="firstname" value="<?php echo $allParams['firstname'] ?>" />
    <input type="hidden" name="email"  value="<?php echo $allParams['email'] ?>" />
    <input type="hidden" name="productinfo" value='<?php echo $allParams["productinfo"] ?>' />
    <input type="hidden" name="surl" value="<?php echo $allParams['surl'] ?>" />
    <input type="hidden" name="furl" value="<?php echo $allParams['furl'] ?>" />
    <input type="hidden" name="service_provider" value="payu_paisa" size="64" />
</form>

<script type="text/javascript">
  myfun();

  function myfun()
  {
    document.getElementById("payuForm").submit();
  }

</script>


