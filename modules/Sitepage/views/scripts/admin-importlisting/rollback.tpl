<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: rollback.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate('Rollback pages ?'); ?></h3>
    <p>
      <?php echo $this->translate('Are you sure that you want to rollback the pages imported from this file ? Doing so will delete all the Pages created through this import.'); ?>
    </p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->importfile_id?>"/>
			<input type="hidden" name="redirect" value="1"/>
      <button type='submit'><?php echo $this->translate('Rollback'); ?></button>
      or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
    </p>
  </div>
</form>

<?php if( @$this->closeSmoothbox ): ?>
	<script type="text/javascript">
		TB_close();
	</script>
<?php endif; ?>