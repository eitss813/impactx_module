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

<h2>
    <?php echo 'Advanced Payment Gateways / Stripe Connect Plugin'; ?>
</h2>
<script type="text/javascript">
    function replaceSkeletonName(obj) {
        var gatewayName = obj.value;
        
        $$('.gatewayNameUc').each(function (elem) {
            elem.innerHTML = gatewayName.charAt(0).toUpperCase() + gatewayName.slice(1);
        });
        
        $$('.gatewayNameLc').each(function (elem) {
            elem.innerHTML = gatewayName.charAt(0).toLowerCase() + gatewayName.slice(1);
        });        
    }
</script>
<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
<?php endif; ?>

<div class='' style="margin-top:15px;">
    <h3>Instructions for new payment gateway integration.</h3>
    <p>This plugin allows you to integrate your own payment gateways by following the simple steps and instructions. Below, you can find the step-by-step explanation to integrate a new payment gateway for your website. You can create and download the skeleton of required files, follow the complete steps and do the required changes in the files to integrate your new payment gateway.
        <br /><br />
    <div class="admin_seaocore_guidelines_wrapper">
        <ul class="admin_seaocore_guidelines">

            <li>	
                <div class="steps">
                    <b>Step 1: Creating Skeleton of files for your new payment gateway</b>

                    <div style="margin-left: 48px;"><?php echo $this->form->render($this); ?><br/>
                        <div class="highlights"><b>Note:</b> The '<?php echo $this->gatewayNameLc; ?>' used throughout the below steps are for reference purpose only. It will replaced with your new gateway name in your downloaded skeleton files.</div>
                    </div>
                </div>
            </li>

            <li>	
                <div class="steps">
                    <b>Step 2: Extract and merge the files</b>
                    <div style="margin-left: 48px;">Extract the downloaded skeleton files and merge the file with its proper path. (i.e.- Merge the extracted 'application' file with your 'application' directory of your socialengine website.)</div>
                </div>
            </li>            

            <li>	
                <div class="steps">
                    <b>Step 3: Replace the dummy icon with your new gateway icon</b>
                    <div style="margin-left: 48px;">
                        Go to this file directory and replace the dummy icon with your new gateway icon:
                        <br/>
                        <i>"application/modules/Sitegateway/externals/images/<?php echo $this->gatewayNameLc; ?>.png"</i>
                    </div>
                </div>
            </li> 

            <li>	
                <div class="steps">
                    <b>Step 4: Form creation for your new payment gateway</b>

                    <div style="margin-left: 48px;">Go to this file directory path and create the form with required fields to input the required credentials of you new payment gateway: 
                        <br/>
                        <i>"application/modules/Sitegateway/Form/Admin/Gateway/<?php echo $this->gatewayNameUc; ?>.php"</i> and <i>"application/modules/Sitegateway/Form/Order/<?php echo $this->gatewayNameUc; ?>.php"</i>.<br/>
                        <div class="references"><b>References:</b> You can go to this file directory and view the code to take a reference for this process:<br/>
                            <i>"application/modules/Sitegateway/Form/Admin/Gateway/Stripe.php"</i> <b>(For Stripe)</b><br/>
                            <i>"application/modules/Payment/Form/Admin/Gateway/PayPal.php"</i> <b>(For PayPal)</b><br/> 
                            <i>"application/modules/Payment/Form/Admin/Gateway/2Checkout.php"</i> <b>(For 2Checkout)</b></div> 
                    </div>
                </div>
            </li>             

            <li>	
                <div class="steps">
                    <b>Step 5: Create payment related functions</b>

                    <div style="margin-left: 48px;">Go to this file directory path and create the payment related functions and write the respective code for your new payment gateway:
                        <br/>
                        <i>"application/libraries/Engine/Service/<?php echo $this->gatewayNameUc; ?>.php"</i>.<br/>
                        <div class="references"><b>References:</b> You can go to this file directory and view the code to take a reference for these payment related functions:<br/>
                            <i>"application/libraries/Engine/Service/Stripe.php"</i> <b>(For Stripe)</b><br/>
                            <i>"application/libraries/Engine/Service/Paypal.php"</i> <b>(For PayPal)</b><br/> 
                            <i>"application/libraries/Engine/Service/2Checkout.php"</i> <b>(For 2Checkout)</b></div>
                    </div>
                </div>
            </li>                

            <li>	
                <div class="steps">
                    <b>Step 6: Create functions for credentials authentication and handling payments</b>

                    <div style="margin-left: 48px;">Go to this file directory path and create the functions for your new payment gateway credentials authentication process and payment handling:
                        <br/>
                        <i>"application/libraries/Engine/Payment/Gateway/<?php echo $this->gatewayNameUc; ?>.php"</i>.<br/>
                        <div class="references"><b>References:</b> You can go to this file directory and view the code to take a reference for gateway credentials authentication and payment handling processes:<br/>
                            <i>"application/libraries/Engine/Payment/Gateway/Stripe.php"</i> <b>(For Stripe)</b><br/>
                            <i>"application/libraries/Engine/Payment/Gateway/Paypal.php"</i> <b>(For PayPal)</b><br/> 
                            <i>"application/libraries/Engine/Payment/Gateway/2Checkout.php"</i> <b>(For 2Checkout)</b></div>

                        <div class="highlights"><b>Important Methods:</b><br/>
                            <b>getGatewayUrl:</b> In this method you need to define that where you will redirect to user when user is ready for checkout and going to pay amount. If you want to redirect on gateway website for payment then take the reference of Paypal else you can take the reference of Stripe.</div>
                    </div>
                </div>
            </li>    

            <li>	
                <div class="steps">
                    <b>Step 7: Create functions for your gateway transactions and IPN</b>

                    <div style="margin-left: 48px;">Go to this file directory path and create the functions and write the respective code related to your gateway transactions and IPN (Instant payment notification):
                        <br/>
                        <i>"application/modules/Sitegateway/Plugin/Gateway/<?php echo $this->gatewayNameUc; ?>.php"</i>.<br/>
                        <div class="references"><b>References:</b> You can go to this file directory and view the code to take a reference for these functions and code:<br/>
                            <i>"application/modules/Sitegateway/Plugin/Gateway/Stripe.php"</i> <b>(For Stripe)</b><br/>
                            <i>"application/modules/Payment/Plugin/Gateway/PayPal.php"</i> <b>(For PayPal)</b><br/> 
                            <i>"application/modules/Payment/Plugin/Gateway/2Checkout.php"</i> <b>(For 2Checkout)</b></div>
                    </div>
                </div>
            </li>               

            <li>	
                <div class="steps">
                    <b>Step 8: Add exceptions handling code</b>

                    <div style="margin-left: 48px;">Go to this file directory path and add the exceptions handling code for your new payment gateway:
                        <br/>
                        <i>"application/libraries/Engine/Service/<?php echo $this->gatewayNameUc; ?>/Exception.php"</i>.<br/>
                        <div class="references"><b>References:</b> You can go to this file directory and view the code to take a reference for these exception handling:<br/>
                            <i>"application/libraries/Engine/Service/Stripe/Exception.php"</i> <b>(For Stripe)</b> <br/>
                            <i>"application/libraries/Engine/Service/Paypal/Exception.php"</i> <b>(For PayPal)</b><br/> 
                            <i>"application/libraries/Engine/Service/2Checkout/Exception.php"</i> <b>(For 2Checkout)</b></div>
                    </div>
                </div>
            </li>              

            <li>	
                <div class="steps">
                    <b>Step 9: Adding new payment gateway libraries</b>

                    <div style="margin-left: 48px;">If required, go to this file directory path and add your new payment gateway libraries: 
                        <br/>
                        <i>"application/libraries"</i>.<br/>
                        You can include this added libraries as per your new gateway requirement. i.e. As it included here: <i>"application/libraries/Engine/Service/Stripe.php"</i> for Stripe gateway.<br/>
                        <div class="references"><b>References:</b> You can go to this file directory and take a reference for adding gateway libraries:<br/>

                            <i>"application/libraries/Stripe"</i> <b>(For Stripe)</b></div>
                    </div>
                </div>
            </li>  

            <li>	
                <div class="steps">
                    <b>Step 10: Run the sql query</b>

                    <div style="margin-left: 48px;">Go to this file directory path and run the sql query which is mentioned there: 
                        <br/>
                        <i>"application/modules/Sitegateway/settings/<?php echo $this->gatewayNameLc; ?>_queries.sql"</i>.<br/>
                        <br/>
                        <div class="highlights"><b>Note:</b> You can delete this file after running the query.</div>
                    </div>
                </div>
            </li>              

            <li>
                <div class="steps">You are done with your new payment gateway integration work and you are ready to use this payment gateway for your website.</div>
            </li>
        </ul>
    </div>
</div>		

<style type="text/css">
    i {
        display: inline-block;
        margin: 5px 5px 5px 0;
    }
    .highlights{
        border-width: 1px; padding: 10px; background-color: rgba(0, 0, 0, 0.04);
    }
    .references{
        padding: 10px; margin: 10px 0px; border: 1px dashed rgb(204, 204, 204);
    }
</style>