<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: update-confirmation.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate('Change Package?'); ?></h3>
    <p>
      <?php echo $this->translate('Are you sure you want to change package for this Page? Once you change package, all the settings of this page will be applied according to the new package, including apps available, features available, price, etc.'); ?>
    </p>
    <br />
    <input type="hidden" name="package_id" value="<?php echo $this->package_id?>" />
    <p>     
      <button type='submit'><?php echo $this->translate('Change'); ?></button>
      or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
    </p>
  </div>
</form>
<?php if (@$this->closeSmoothbox): ?>
	<script type="text/javascript">
	  TB_close();
	</script>
<?php endif; ?>