<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitefaq
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit-icon.tpl 6590 2012-18-05 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="global_form_popup">
  <?php echo $this->form->render($this) ?>
</div>

<?php if( @$this->closeSmoothbox || $this->close_smoothbox): ?>
	<script type="text/javascript">
		window.parent.location.href=en4.core.baseUrl +'admin/sitepage/settings/sitepagecategories';
		window.parent.Smoothbox.close();
	</script>
<?php endif; ?>