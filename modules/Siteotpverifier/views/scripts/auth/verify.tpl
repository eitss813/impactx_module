<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    verify.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Siteotpverifier/externals/styles/siteotpverifier_style.css');
?>
<?php echo $this->form->render($this) ?>
<script type="text/javascript">
//<![CDATA[
    function resendotpCode(){
        //Smoothbox.open(en4.core.baseUrl + 'siteotpverifier/auth/resend?user_id=<?php echo $this->user_id ?>');

        en4.core.request.send(new Request.JSON({
              url: "<?php echo $this->url(array('module' => 'siteotpverifier', 'controller' => 'auth', 'action' => 'resend'), 'default', true); ?>",
              method: 'post',
              data: {
                format: 'json',
                user_id : '<?php echo $this->user_id ?>',
                type : 'forgot',
              },
              onRequest: function(){
                var el = new Element('div', {
                'id':'sent_loading_button'    
                });  
                var parentDiv = document.getElementById('siteotpverifier_form_verify');
                el.inject(parentDiv.getElement('.form-elements'));
                document.getElementById('sent_loading_button').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Siteotpverifier/externals/images/loading.gif" />';
              },
              onSuccess: function(responseJSON) {
                if (responseJSON.otpSent) {
                    document.getElementById('sent_loading_button').innerHTML = '';
                } else {
                    document.getElementById('sent_loading_button').innerHTML =responseJSON.errormessage;
                }
              }
            }));
       
    }

</script>
