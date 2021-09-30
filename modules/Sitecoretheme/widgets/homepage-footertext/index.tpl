<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
if( !$this->viewer()->getIdentity() ): ?>
  <h3>
    <?php echo $this->translate("_SITECORETHEME_FOOTER_TITLE"); ?>
  </h3>

  <p class="desc-text">
    <?php echo $this->translate("_SITECORETHEME_FOOTER_DESCRIPTION"); ?>
  </p>

  <div class="signupblock">
    <a href="<?php echo $this->url(array(), "user_signup", true) ?>" class="user_signup_link"><?php echo $this->translate("_SITECORETHEME_FOOTER_BUTTON_TEXT"); ?></a>
  </div>
<?php endif; ?>