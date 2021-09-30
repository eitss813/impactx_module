<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
 ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesnewsletter/externals/styles/styles.css'); ?>

<div class="sesnewsletter_newsletter_wrapper clearfix sesbasic_bxs">
	<div class="sesnewsletter_newsletter_wrapper_inner">
     <div class="head">
       <h4><?php echo $this->translate("Newsletter"); ?></h4>
     </div>
     <div class="input-box">
       <input name="email" id="sesnewsletter_newsletter_email" type="email" placeholder="<?php echo $this->translate('Enter Your Email Address'); ?>"/>
       <button id="sesnewsletter_newsletter" type="submit"><?php echo $this->translate("Subscribe"); ?></button>
     </div>
     <div  style="display:none;" class="sesnewsletter_newsletter_tip" id='sesnewsletter_newsletter_successmsg'><span><?php echo $this->translate("Thank you for subscribing."); ?></span></div>
     <div style="display:none;" class="sesnewsletter_newsletter_tip" id='sesnewsletter_newsletter_erromsg'><span><?php echo $this->translate("You have already subscribed."); ?></span></div>
  </div>
</div>
<script>
  sesJqueryObject(document).ready(function() {
    sesJqueryObject("#sesnewsletter_newsletter_email").click(function(e) {
      e.preventDefault();
      var sesnewsletter_newsletter_email = sesJqueryObject('#sesnewsletter_newsletter_email').val();
      if(sesnewsletter_newsletter_email)
        sendNewsletter();
    });
    
    sesJqueryObject('#sesnewsletter_newsletter_email').keydown(function(e) {
      if (e.which === 13) {
        sendNewsletter();
      }
    });
    
    sesJqueryObject("#sesnewsletter_newsletter").click(function(e) {
      e.preventDefault();
      var sesnewsletter_newsletter_email = sesJqueryObject('#sesnewsletter_newsletter_email').val();
      if(sesnewsletter_newsletter_email)
        sendNewsletter();
    });
  });
  
  function sendNewsletter() {
  
    var newsletteremail = sesJqueryObject('#sesnewsletter_newsletter_email').val();
    if(newsletteremail == '')
      return;
    sesJqueryObject('#sesnewsletter_newsletter_email').val('');
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'sesnewsletter/index/newsletter',
      data: {
        format: 'json',
        'email': newsletteremail,
      },
      onSuccess: function(responseJSON) {
        console.log(responseJSON.subscriber_id);
        if(responseJSON.subscriber_id) {
          sesJqueryObject('#sesnewsletter_newsletter_successmsg').show();
          sesJqueryObject('#sesnewsletter_newsletter_successmsg').fadeOut("slow", function(){
            setTimeout(function() {
              sesJqueryObject('#sesnewsletter_newsletter_successmsg').hide();
            }, 1000);
          });
        } else {
          sesJqueryObject('#sesnewsletter_newsletter_erromsg').show();
          sesJqueryObject('#sesnewsletter_newsletter_erromsg').fadeOut("slow", function(){
            setTimeout(function() {
              sesJqueryObject('#sesnewsletter_newsletter_erromsg').hide();
            }, 1000);
          });
        }
      }
    }));
  
  }
</script>
