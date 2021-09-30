<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete-icon.tpl 6590 2012-18-05 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate('Delete Category Icon ?'); ?></h3>
    <p>
      <?php echo $this->translate('Are you sure that you want to delete this category icon ? It will not be recoverable after being deleted.'); ?>
    </p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->category_id?>"/>
      <button type='submit'><?php echo $this->translate('Delete'); ?></button>
      <?php echo $this->translate('or'); ?> <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
    </p>
  </div>
</form>

<?php if( @$this->closeSmoothbox || $this->close_smoothbox): ?>
	<script type="text/javascript">
		window.parent.location.href=en4.core.baseUrl +'admin/sitepage/settings/sitepagecategories';
		window.parent.Smoothbox.close();
	</script>
<?php endif; ?>