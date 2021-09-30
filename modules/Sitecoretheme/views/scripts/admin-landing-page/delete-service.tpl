<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete-service.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<div class="global_form_popup">
  <?php echo $this->form->render($this) ?>
</div>

<?php if( @$this->closeSmoothbox || $this->close_smoothbox): ?>
	<?php $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl(); ?>
<script type="text/javascript">
  window.parent.Smoothbox.close();
</script>
<?php endif; ?>

<script type="text/javascript">
  function closeSmoothbox() {
    window.parent.Smoothbox.close();
  }
</script>