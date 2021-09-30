<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    faq.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
    <?php echo 'One Time Password (OTP) Plugin' ?>
</h2>

<?php if( count($this->navigation) ): ?>
      <div class='siteotpverifier_admin_tabs clr'>
        <?php
    // Render the menu
    //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
      </div>
    <?php endif; ?>
<?php
include_once APPLICATION_PATH .
        '/application/modules/Siteotpverifier/views/scripts/admin-settings/faq_help.tpl';
?>
