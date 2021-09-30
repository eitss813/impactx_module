<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    faq_help.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
    function faq_show(id) {
        if ($(id).style.display == 'block') {
            $(id).style.display = 'none';
        } else {
            $(id).style.display = 'block';
        }
    }
</script>
<?php $style = 'display:none;' ?>
<?php $flag = 0; ?>

<div class="admin_seaocore_files_wrapper">

    <ul class="admin_seaocore_files seaocore_faq">
        <br />
        <h3>General</h3>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">What are the payment gateways currently available in this plugin ?</a>
            <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
               This plugin currently supports Stripe, Stripe connect, Mangopay, PayUmoney, Paynow and Mollie payment gateways. If you want to integrate a new payment gateway, then please <a href="http://www.socialengineaddons.com/contact" target="_blank">contact us</a> for same.
            </div>
        </li>
        <?php $newGatewayIntegrationURL = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sitegateway', 'controller' => 'integration'), 'admin_default', true); ?>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">Can I integrate my own payment gateway in this plugin? If yes, then how can I integrate a new gateway in this plugin?</a>
            <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
                Yes, you can integrate your own payment gateway in this plugin. If you want to integrate your own payment gateway in this plugin, you just need to follow the steps provided under <a href="<?php echo $newGatewayIntegrationURL; ?>" target="_blank">New Payment Gateway Integration</a> tab available at admin panel of this plugin and write the code accordingly.
            </div>
        </li>       

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">Can I enable multiple gateway on my website?</a>
            <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
                Yes, you can enable multiple gateways on your website.
            </div>
        </li>  
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">From where can I get complete information about the payment gateways included in this plugin?</a>
            <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
                Please go through below websites to get complete information about the payment gateways included in this plugin:<br />
                Stripe: <a href="https://stripe.com" target="_blank">https://stripe.com</a><br />
                Stripe Connect: <a href="https://stripe.com/connect" target="_blank">https://stripe.com/connect</a><br />
                MangoPay: <a href="https://www.mangopay.com/" target="_blank">https://www.mangopay.com</a><br /> 
                PayUmoney: <a href="https://www.payumoney.com/" target="_blank">https://www.payumoney.com</a><br /> 
                Paynow: <a href="https://www.paynow.co.zw/" target="_blank">https://www.paynow.co.zw/</a><br /> 

            </div>
        </li> 
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">Why I am unable to see MangoPay payment gateways for purchasing the package?</a>
            <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
                MangoPay payment gateway divide payment between site owner and seller (in both payment methods: Split Immediately and Escrow.) But, in case of ‘Packages’ the amount is charged by site owner only. So, there is no need to divide the payment and hence MangoPay payment gateway cannot be used for purchasing the package.
            </div>
        </li> 
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">How can I use Escrow payment method of MangoPay Payment Gateway?</a>
            <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
                Please refer below video to configure Escrow payment method of MangoPay payment gateway with Stores / Marketplace - Ecommerce Plugin.<br />
                <a href="https://www.youtube.com/watch?v=mdROYA4qIj4" target="_blank">https://www.youtube.com/watch?v=mdROYA4qIj4</a>
            </div>
        </li>
    </ul>
    <br/>
    <ul class="admin_seaocore_files seaocore_faq"> 
        <br />
        <h3>Stripe / Stripe Connect</h3>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">What is Stripe Connect and how Stripe Connect is different from a normal Stripe account ?</a>
            <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
                To know all about the Stripe Connect details, please follow this URL: <a href="https://stripe.com/docs/connect" target="_blank">https://stripe.com/docs/connect</a> and it also gives you the idea that how Stripe Connect is different from a normal Stripe account.
            </div>
        </li>        

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">Can you please recommend to me, how can I decide to use Stripe / Stripe Connect for SocialApps.tech plugins installed on my website ?</a>
            <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
                Yes, you can decide about Stripe / Stripe Connect uses with our plugins like as follows:
                <ul>  

                    <li><a href="http://www.socialengineaddons.com/socialengine-stores-marketplace-ecommerce-plugin" target="_blank">Stores / Marketplace - Ecommerce Plugin</a> - Stripe Connect, because it will provide a platform for your sellers where they can quickly connect to your platform and start selling the products / services. Also, you can get your commissions directly into your Stripe account once any order has been placed on your website. You can read more about Stripe Connect benefits by following this URL:<a href="https://stripe.com/docs/connect" target="_blank">https://stripe.com/docs/connect</a></li>
                    <li><a href="http://www.socialengineaddons.com/eventextensions/socialengine-advanced-events-events-booking-tickets-selling-paid-events-extension" target="_blank">Advanced Events - Events Booking, Tickets Selling & Paid Events Extension</a> - Stripe Connect, reason same as above.</li>
                    <li><a href="http://www.socialengineaddons.com/socialengine-directory-pages-plugin" target="_blank">Directory / Pages Plugin</a> - Stripe, for making payment to subscribe any package.</li>
                    <li><a href="http://www.socialengineaddons.com/socialengine-directory-businesses-plugin" target="_blank">Directory / Businesses Plugin</a> - Stripe, for making payment to subscribe any package.</li>
                    <li><a href="http://www.socialengineaddons.com/socialengine-groups-communities-plugin" target="_blank">Groups / Communities Plugin</a> - Stripe, for making payment to subscribe any package.</li>
                    <li><a href="http://www.socialengineaddons.com/socialengine-advanced-events-plugin" target="_blank">Advanced Events Plugin</a> - Stripe, for making payment to subscribe any package.</li>
                    <li><a href="http://www.socialengineaddons.com/multiplelistingtypesextensions/socialengine-multiple-listing-types-paid-listings" target="_blank">Multiple Listing Types - Paid Listings Extension</a> - Stripe, for making payment to subscribe any package.</li>
                    <li><a href="http://www.socialengineaddons.com/socialengine-advertisements-community-ads-plugin" target="_blank">Advertisements / Community Ads Plugin</a> - Stripe, for making payment to subscribe any package.</li>
                </ul><br/>
                Note: You can also use normal Stripe for your <a href="http://www.socialengineaddons.com/socialengine-stores-marketplace-ecommerce-plugin" target="_blank">Stores / Marketplace - Ecommerce Plugin</a> and <a href="http://www.socialengineaddons.com/eventextensions/socialengine-advanced-events-events-booking-tickets-selling-paid-events-extension" target="_blank">Advanced Events - Events Booking, Tickets Selling & Paid Events Extension</a> installed on your website.
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">Can I use Stripe to make payments for SocialEngine sign-up subscription plans ?</a>
            <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
                Yes, you can use the Stripe to make payments for SocialEngine sign-up subscription plans which are created through Admin => Billing => Plans on your website.
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">How can I start to integrate Stripe Connect with Stores / Marketplace - Ecommerce Plugin and Advanced Events - Events Booking, Tickets Selling & Paid Events Extension ?</a>
            <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
                To start Stripe Connect integration with <a href="http://www.socialengineaddons.com/socialengine-stores-marketplace-ecommerce-plugin" target="_blank">Stores / Marketplace - Ecommerce Plugin</a> and <a href="http://www.socialengineaddons.com/eventextensions/socialengine-advanced-events-events-booking-tickets-selling-paid-events-extension" target="_blank">Advanced Events - Events Booking, Tickets Selling & Paid Events Extension</a> on your website, please do the followings:
                <ul>                
                    <li>Go to 'Global Settings' at admin panel of this plugin.</li>
                    <li>Select 'Yes' for Stripe Connect option.</li>
                    <li>Select the method to charge Stripe’s fees as per your requirement.</li>
                    <li>Go to 'Gateways' tab at admin panel of this plugin.</li>
                    <li>Click on 'Edit' option available with Stripe.</li>
                    <li>Fill-up the details like: API Secret Key, API Publishable Key and Client Id (You can get these details by following the process mentioned on the same page).</li>
                    <li>Select 'Yes' for Enabled and click on 'Save Changes' button. If the details are correct, your Stripe Connect account has been integrated successfully with your website for these plugins and 'Connect with Stripe' button will start showing to your sellers on the respective plugin's dashboard from where they can configure their Stripe accounts.</li>
                    <br/>
                    <div class="tip">
                        <span>
                            Note: For more details about how to configure and integrate Stripe Connect with your website, please view this video tutorial: <a href="https://www.youtube.com/watch?v=vCXAJHEwsJ8" target="_blank">https://www.youtube.com/watch?v=vCXAJHEwsJ8</a>
                            <span>
                                </div>

                                </ul>
                                </div>
                                </li>    

                                <li>
                                    <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">Can I run the Stripe / Stripe Connect in test mode before running it in live / production mode ?</a>
                                    <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
                                        Yes, you can run the Stripe / Stripe Connect in test mode before running it in live / production mode. To do so, please go to admin panel of this plugin, and enable the test mode option available at Stripe / Stripe Connect account configuration page. Please ensure that you have entered correct test mode Stripe / Stripe Connect credentials, if you select this option.
                                    </div>
                                </li>       

                                <li>
                                    <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">Can you please recommend to me, which option should I select to charge Stripe’s fees under Stripe Connect, if Stores / Marketplace - Ecommerce Plugin or Advanced Events - Events Booking, Tickets Selling & Paid Events Extension are installed my website ?</a>
                                    <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
                                        Yes, we recommend you to select ' Connected Stripe accounts (sellers) will pay Stripe’s fees' option in Stripe Connect, if you are running <a href="http://www.socialengineaddons.com/socialengine-stores-marketplace-ecommerce-plugin" target="_blank">Stores / Marketplace - Ecommerce Plugin</a> or <a href="http://www.socialengineaddons.com/eventextensions/socialengine-advanced-events-events-booking-tickets-selling-paid-events-extension" target="_blank">Advanced Events - Events Booking, Tickets Selling & Paid Events Extension</a> on your website, because you will not be liable for any Stripe charges for the transactions done through Stripe gateway. It is the seller who will pay the Stripe fees if you will select this option. You will only get your commissions amount directly into your Stripe account once any order payment has been made through Stripe gateway. You can read more about the benefits of using this option by visiting this URL: <a href="https://stripe.com/docs/connect/payments-fees" target="_blank">https://stripe.com/docs/connect/payments-fees</a>
                                    </div>
                                </li>     

                                <li>
                                    <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">How the "Commissions Bill" and "Payment Request" options works, if Stripe Connect is enabled and how can I disable them if I do not require ?</a>
                                    <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
                                        If Stripe Connect is enabled for your website, then the amount shown under 'Commissions Bill' or 'Payment Request' tab of various plugins will be not added the commissions which raised by Stripe Connect transactions, because once Stripe Connect is enabled on your website, the respectives commissions / payments are transferred into respective admin / sellers accounts directly at the time of payment made by any user, hence those commissions / payment request amounts are not added there. In other way, the amount shown under 'Commissions Bill' or 'Payment Request' are not included the Stripe Connect payment gateway transactions to calculate the 'Commissions Bill' or 'Payment Request' amount. If you want to disable the 'Commissions Bill' and 'Payment Request' items from sellers dashboard, you can follow these steps:
                                        <br/><br/>              
                                        For <a href="http://www.socialengineaddons.com/socialengine-stores-marketplace-ecommerce-plugin" target="_blank">Stores / Marketplace - Ecommerce Plugin</a>
                                        <ul>
                                            <li>Go to admin panel of your website</li>
                                            <li>Go to Layout => Menu Editor => Select 'Stores - Store Dashboard menu' from dropdown</li>
                                            <li>Search Your Bill or Payment Request menu item, click edit button available with menu item and disable it.</li>
                                            <li>Now this disable menu item will not visible to sellers on their corresponding store dashboard.</li>
                                        </ul>
                                        <br/>
                                        For <a href="http://www.socialengineaddons.com/eventextensions/socialengine-advanced-events-events-booking-tickets-selling-paid-events-extension" target="_blank">Advanced Events - Events Booking, Tickets Selling & Paid Events Extension</a>
                                        <ul>
                                            <li>Go to admin panel of your website</li>
                                            <li>Go to Layout => Menu Editor => Select 'Advanced Events - Dashboard Navigation (Ticketing)' menu' from dropdown</li>
                                            <li>Search Commissions Bill or Payment Request menu item, click edit button available with menu item and disable it.</li>
                                            <li>Now this disable menu item will not visible to sellers on their corresponding event dashboard.</li>
                                        </ul>
                                    </div>
                                </li>       

                                <li>
                                    <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">Can this plugin also supports Stripe Connect Managed Accounts, If not then why ?</a>
                                    <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
                                        No, this plugin does not support Stripe Connect Managed Accounts. It only supports Stripe Connect Standalone Accounts. Because the BETA version is only available for Stripe Connect Managed Accounts and, it is also supported in very limited countries, but still if you want to implement Stripe Connect Managed Accounts, then please <a href="http://www.socialengineaddons.com/contact" target="_blank">contact us</a>, we will customize and implement Stripe Connect Managed account for your website.
                                    </div>
                                </li>        

                                <li>
                                    <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">Do customers need a Stripe account to buy my products / services on my website ?</a>
                                    <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
                                        No, your customers do not required to have a Stripe account to purchase your offered products and services on your website. All they need is a valid credit card or debit card. For more details, please visit: <a href="https://support.stripe.com/questions/which-cards-and-payment-types-can-i-accept-with-stripe" target="_blank">which cards and payment types are accepted with Stripe</a>.
                                    </div>
                                </li>       

                                <li>
                                    <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">What information does sellers requires to connect on my platform through Stripe Connect ?</a>
                                    <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
                                        To get started, your sellers are only required Stripe account to connect on your platform.
                                    </div>
                                </li>       



                                <li>
                                    <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">Do I need an SSL (Secure Sockets Layer) on my payment page if I use Stripe ?</a>
                                    <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
                                        Yes, you should have SSL (Secure Sockets Layer) on your website if you are using Stripe / Stripe Connect in live mode. If you want to run the Stripe / Stripe Connect in test mode, you do not require SSL on your website. For more details about SSL for Stripe / Stripe Connect, you can visit this URL: <a href="https://stripe.com/help/ssl" target="_blank">https://stripe.com/help/ssl</a>. If you need any help in installation of SSL on your website, you can opt for our <a href="http://www.socialengineaddons.com/services/ssl-certification-installation" target="_blank">SSL Certificate Installation Service</a> where we will help you to set up SSL on your website.
                                    </div>
                                </li> 
                                </ul>
                                <br/>
    <ul class="admin_seaocore_files seaocore_faq"> 
            <br />
            <h3>MangoPay</h3>
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">How can I configure MangoPay payment gateway in Escrow payment method for ‘Stores / Marketplace - Ecommerce Plugin’?</a>
                <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
                   Please refer below video to refer the detailed procedure to configure MangoPay payment gateway in Escrow payment method for ‘Stores / Marketplace - Ecommerce Plugin’:<br />
                   <a href="https://www.youtube.com/watch?v=mdROYA4qIj4&feature=youtu.be" target="_blank">https://www.youtube.com/watch?v=mdROYA4qIj4&feature=youtu.be</a>
                </div>
            </li>  

    </ul>  <br/>
    <ul class="admin_seaocore_files seaocore_faq"> 
        <br />
        <h3>PayUmoney</h3>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">What are the different currencies supported by PayUMoney?</a>
            <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
               PayUmoney only supports INR currency.
            </div>
        </li>  
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">What are the different countries supported by PayUMoney?</a>
            <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
                PayUmoney is currently available in India only.
            </div>
        </li> 
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">What do I need to do to integrate PayUMoney?</a>
            <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
                You need to submit following three documents to integrate PayUMoney:<br />
                - Pan Card <br />
                - Savings Bank Account<br />
                - Business Address<br />
            </div>
        </li> 
    </ul>  <br/>
    <ul class="admin_seaocore_files seaocore_faq"> 
            <br />
            <h3>Paynow</h3>
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">Which countries are supported by Paynow payment gateway?</a>
                <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
                   Paynow  is currently in Zimbabwe only.
                </div>
            </li>  
    </ul></br>

    <ul class="admin_seaocore_files seaocore_faq"> 
        <br />
        <h3>Mollie</h3>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">What are the different currencies supported by Mollie?</a>
            <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
             Mollie only supports Euro (EUR) currency.
         </div>
        </li> 

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">What are the different countries supported by Mollie?</a>
            <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
             Mollie is currently available in: Germany, United States of America, Spain, France, Belgium, Netherlands.
         </div>
        </li>  
    </ul>

</div>

