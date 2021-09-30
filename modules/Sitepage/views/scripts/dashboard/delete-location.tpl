<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete-location.tpl 2012-08-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate('Delete Location ?'); ?></h3>
    <p>
      <?php echo $this->translate('Are you sure that you want to remove this location form this page?'); ?>
    </p>
    <br />
    <p>
      <input type="hidden" name="confirm" />
      <button type='submit'><?php echo $this->translate('Delete'); ?></button>
      <?php echo $this->translate('or') ?> <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
    </p>
  </div>
</form>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>