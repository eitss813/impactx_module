<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: approve-page.tpl 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate("Approve Request?") ?></h3>
    <p>
      <?php echo $this->translate("Are you sure that you want to approve this request?"); ?>
    </p>
    <br />
    <p>
      <button type='submit'><?php echo $this->translate("Approve Request") ?></button>
      <?php echo $this->translate(" or ") ?>
      <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close();'>
        <?php echo $this->translate("cancel") ?></a>
    </p>
  </div>
</form>
