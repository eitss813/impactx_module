<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    delete-mobile.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Siteotpverifier/externals/styles/siteotpverifier_style.css');
?>
<form method="post" class="global_form_popup">
  <div>
    <h3>Remove Mobile Number ?</h3>
    <p>
      Are you sure you want to remove the phone number?
    </p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="123"/>
      <button type='submit'>Delete</button>
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