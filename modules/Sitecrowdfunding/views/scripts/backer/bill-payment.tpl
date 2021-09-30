<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: bill-payment.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


<?php if (!empty($this->noAdminGateway)) : ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("Site Administrator has not enabled any payment gateway. So you can't pay commission. Please contact to site administrator.") ?>
    </span>
  </div>
  <?php return; ?>
<?php endif; ?>

<div class="global_form_popup sitecrowdfunding_dashbord_popup_form">
  <?php echo $this->form->render($this); ?>
</div>
