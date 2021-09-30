<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: remove-cover-photo.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="post" class="global_form_popup">
  <div>
		<h3><?php echo $this->translate('Remove Page Profile Cover Photo?'); ?></h3>
		<p><?php echo $this->translate("Are you sure you want to remove your Page Profile Cover Photo?"); ?></p>
    <br />
    <p>
      <input type="hidden" name="confirm" value=""/>
      <button type='submit' data-theme="b"><?php echo $this->translate('Remove'); ?></button>
      or <a data-role="button" href="#" data-rel="back"><?php echo $this->translate('Cancel'); ?></a>
    </p>
  </div>
</form>