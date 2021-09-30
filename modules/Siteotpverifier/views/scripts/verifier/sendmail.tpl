<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    sendmail.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<form method="post" class="global_form_popup">
  <div>
    <h3>Send OTP</h3>
    <p>
      You want to send OTP via mail ?.
    </p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->offer_id?>"/>
      <button type='submit'>Yes</button>
       or
      <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        cancel</a>
      </p>
    </div>
  </form>

  <?php if( @$this->closeSmoothbox ): ?>
    <script type="text/javascript">
      TB_close();
    </script>
  <?php endif; ?>