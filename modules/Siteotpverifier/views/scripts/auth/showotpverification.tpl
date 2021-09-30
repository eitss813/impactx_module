<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    otpverify.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Siteotpverifier/externals/styles/siteotpverifier_style.css');
?>
<?php echo $this->form->render($this) ?>

<script type="text/javascript">
    var otpverified = '<?php echo $this->otpverifieda; ?>';
    console.log(otpverified);
    if (otpverified) {
        console.log(Smoothbox);
        console.log(parent.document.forms["signup_account_form"]);
        Smoothbox.close();
        parent.document.forms["signup_account_form"].submit(); 
    }
    
</script>

<script type="text/javascript">
//<![CDATA[

    function resendCode(){
         en4.core.request.send(new Request.JSON({
              url: "<?php echo $this->url(array('module' => 'siteotpverifier', 'controller' => 'auth', 'action' => 'verify-mobile-no'), 'default', true); ?>",
              method: 'post',
              data: {
                format: 'json',
              },
              onRequest: function(){
                var el = new Element('div', {
                'id':'sent_loading_button'    
                });  
                var parentDiv = document.getElementById('siteotpverifier_signupform_verify');
                el.inject(parentDiv.getElement('.form-elements'));
                document.getElementById('sent_loading_button').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Siteotpverifier/externals/images/loading.gif" />';
              },
              onSuccess: function(responseJSON) {
                if (responseJSON.otpSent) {
                  document.getElementById('sent_loading_button').innerHTML = '';
                }
              }
            }));
    }

</script>