<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: process.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div>
    <div>
        <center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/loading.gif" /></center>
    </div>
    <div id="LoadingImage" style="text-align:center;margin-top:15px;font-size:17px;">  
        <?php echo $this->translate("Processing Request. Please wait .....") ?>
    </div>
</div>
<script type="text/javascript">
    window.addEvent('load', function () {
        var paypal_type = "<?php echo $this->paypal_type;?>";
        var url = '<?php echo $this->transactionUrl ?>';
        if(paypal_type === "PAYPAL_DONATE"){
            var data = {};
            data.cmd = "_donations";
            data.business = "<?php echo $this->project_payment_set_email ;?>";
            data.return = "<?php echo $this->return_url ;?>";
            data.amount = <?php echo $this->amount ;?>;
            var request = new Request.Post({
                url: url,
                data: data
            });
            request.send();
        }else{
            var data = <?php echo Zend_Json::encode($this->transactionData) ?>;
            var request = new Request.Post({
                url: url,
                data:data
            });
            request.send();
        }
    });
</script>