<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: process.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if($this->isSupported) { ?>
<div>
  <div>
    <center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/loading.gif" /></center>
  </div>
  <div id="LoadingImage" style="text-align:center;margin-top:15px;font-size:17px;">  
    <?php echo "Processing Request. Please wait ....." ?>
  </div>
</div>

<?php if (!empty($this->user_gateway_disable)): ?>
  <div class="tip">
    <span>
      <?php echo 'No payment gateway is enabled.<br>So you can\'t proceed further for payment process'; ?>
    </span>
  </div>
<?php endif; ?>


<script type="text/javascript">
  window.addEvent('load', function () {

    var url = '<?php echo $this->transactionUrl ?>';
    var data = <?php echo Zend_Json::encode($this->transactionData) ?>;
    var request = new Request.Post({
      url: url,
      data: data
    });
    request.send();
  });
</script>

<?php } else { ?>
<div class="tip"><span> This Payment gateway does not support this currency please choose another payment method for processing further.</span></div>
<?php } ?>