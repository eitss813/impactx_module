<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: mapping-category.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<div class="global_form_popup">
  <?php echo $this->form->render($this) ?>
</div>

<?php if( @$this->closeSmoothbox || $this->close_smoothbox): ?>
	<?php $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl(); ?>
	<script type="text/javascript">
		window.parent.location.href='<?php echo $baseurl ?>'+'/admin/sitealbum/settings/categories';
		window.parent.Smoothbox.close();
	</script>
<?php endif; ?>

<script type="text/javascript">
	function closeSmoothbox() {
		window.parent.Smoothbox.close();
	}
</script>

