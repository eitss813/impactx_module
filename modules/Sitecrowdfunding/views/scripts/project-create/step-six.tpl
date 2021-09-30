<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding_dashboard.css'); ?>
<?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project-create/common.tpl'; ?>
<div class="sitecrowdfunding_project_new_steps">
    <form class="global_form">
        <div>
            <div>
                <h3 >
                    See Your Project Profile
                </h3>
            </div>
        </div>
    </form>
    <div class="project_ready_intro">
        <br/>
        <!--<h2 style="text-align: center;padding: 15px;font-weight: bold">
            Your project is ready. Here is link: <a style="color: #44AEC1;"  href="<?php echo (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'].$this->project->getHref() ?>">
                <?php echo (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'].$this->project->getHref() ?>
            </a>
        </h2>-->
        <div class="info_content">
            Now that you have created a Project Profile, you can add additional information about your project by going to the ImpactX Dashboard
        </div>

        <br/>

        <div class="sitecrowdfunding_leaders">

            <div class="options_btn">
                <div class="options_set_btn">
                    <button name="previous_btn" id="previous_btn" type="button" onclick="window.location.href='<?php echo $this->backURL; ?>'">Previous</button>
                </div>
                <div class="options_set_btn">
                    <?php echo $this->htmlLink(array('route' => 'sitecrowdfunding_dashboard','action' =>
                    'overview','project_id' => $this->project_id), $this->translate('Edit your project'), array('class'
                    => 'button view_project_btn')); ?>
                </div>
                <div class="options_set_btn">
                    <?php echo $this->htmlLink($this->project->getHref(), $this->translate('See Your Project Profile'),
                    array("class" => 'button view_project_btn')) ?>
                </div>
            </div>

        </div>

        <br/>
        <br/>

    </div>
</div>

<style>

    .info_content{
        display: flex;
        justify-content: center;
        font-size: 16px;
        /*padding-left: 20px;*/
        /*padding-right: 20px;*/
    }

    #previous_btn{
        margin-top: -6px !important;
    }
    .sitecrowdfunding_project_new_steps {
        min-width: 430px;
        max-width: 850px;
        padding: 20px;
        margin-left: auto;
        margin-right: auto;
        position: relative;
        border-radius: 3px;
        margin-bottom: 30px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.23);
        background: rgba(255, 255, 255, .9)
    }

    .form_title {
        text-align: center;
        margin: 10px;
    }

    .form_sub_title {
        font-size: 17px;
        border-bottom: 1px solid #f2f0f0;
        padding: 10px 10px;
        margin: -10px -10px 10px -10px;
    }

    .sitecrowdfunding_leaders_list > div {
        display: inline-block;
        vertical-align: middle;
    }

    .sitecrowdfunding_members_details {
        background: none !important;
        border: none !important;
        padding: 0px !important;
        font-size: 14px !important;
    }

    .sitecrowdfunding_leaders_detail a:hover {
        color: #444;
    }

    .options_set_btn > a {
        font-weight: unset !important
    }

    .options_btn{
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    .options_set_btn {
        margin: 10px;
    }

</style>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js');?>

<!-- todo: PAYMENT DEFAULT -->
<!-- Set payment info by default-->
<script>
    var $j = jQuery.noConflict();
    function setPaymentByDefault(){

        // set paypal payment into project profile
        var setPaypalPayment = "<?php echo $this->setPaypalPayment; ?>";
        var setStripePayment = "<?php echo $this->setStripePayment; ?>";

        var payment_email = "<?php echo $this->payment_email; ?>";
        var payment_username = "<?php echo $this->payment_username; ?>";
        var payment_password = "<?php echo $this->payment_password; ?>";
        var payment_signature = "<?php echo $this->payment_signature; ?>";
        var payment_secret_key = "<?php echo $this->payment_secret_key; ?>";
        var payment_publishable_key = "<?php echo $this->payment_publishable_key; ?>";

        var payment_payload = {
            format: 'json',
            project_id: '<?php echo $this->project_id ?>'
        };

        var send = false;
        if(setPaypalPayment){
            send = true;
            var data2 = {};
            data2.paypal = `email=${encodeURIComponent(payment_email)}&username=${payment_username}&password=${payment_password}&signature=${payment_signature}`;
            payment_payload.data = JSON.encode(data2);
        }else{
            payment_payload.data = "{}";
        }

        if(setStripePayment) {
            send = true;
            var data2 = `secret=${payment_secret_key}&publishable=${payment_publishable_key}`;
            payment_payload.additionalGatewayDetailArray = {
                stripe: data2,
            }
        }

        if(send == true) {
            request = new Request.JSON({
                url: en4.core.baseUrl + 'sitecrowdfunding/project/set-project-gateway-info',
                method: 'POST',
                data: payment_payload,
                onRequest: function () {
                    //console.log('onRequest');
                },
                onSuccess: function (responseJSON) {
                    //console.log('responseJSON',responseJSON);
                }
            });
            request.send();
        }
    }

    $j(document).ready(function() {
        <?php if($this->project->is_fund_raisable): ?>
            setPaymentByDefault();
        <?php endif; ?>
    });


</script>
