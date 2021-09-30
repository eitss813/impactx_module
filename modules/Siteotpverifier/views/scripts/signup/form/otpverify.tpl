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
<?php $otpverifySkip = new Zend_Session_Namespace('Siteotpverifier_otpverifyskip');
if($otpverifySkip->skip):?>
<script type="text/javascript">
    document.getElementById("siteotpverifier_signupform_verify").submit();
</script>
<?php endif; ?>
<script type="text/javascript">
//<![CDATA[

    function resendSinupCode(){
         en4.core.request.send(new Request.JSON({
              url: "<?php echo $this->url(array('module' => 'siteotpverifier', 'controller' => 'auth', 'action' => 'verify-mobile-no'), 'default', true); ?>",
              method: 'post',
              data: {
                format: 'json',
              },
              onRequest: function(){
                if ($('sent_loading_button')) {
                  $('sent_loading_button').destroy();
                }
                  var el = new Element('div', {
                  'id':'sent_loading_button'    
                  });  
                  var parentDiv = document.getElementById('siteotpverifier_signupform_verify');
                  el.inject(parentDiv.getElementById('buttons-element'));
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