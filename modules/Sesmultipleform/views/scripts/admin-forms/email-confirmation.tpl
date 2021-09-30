<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: email-confirmation.tpl 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */ 
?>
<?php
 $id = $this->form_id;

?>

<script>
  function showConfirmationFields(value)
		{
			if (value==0)
			{
			if(document.getElementById('confirmation_subject-wrapper'))
				document.getElementById('confirmation_subject-wrapper').style.display = 'none';
			if(document.getElementById('confirmation_message-wrapper'))
				document.getElementById('confirmation_message-wrapper').style.display = 'none';
			}
			else
			{
			if(document.getElementById('confirmation_subject-wrapper'))
				document.getElementById('confirmation_subject-wrapper').style.display = 'block';
			if(document.getElementById('confirmation_message-wrapper'))
				document.getElementById('confirmation_message-wrapper').style.display = 'block';
			}
		}
</script>
<script type="application/javascript">
			window.addEvent('domready', function() {
				showConfirmationFields('<?php echo $this->formset->email_confirmation ?>');
			});
</script> 

<?php include APPLICATION_PATH .  '/application/modules/Sesmultipleform/views/scripts/dismiss_message.tpl';?>
<div class="sesbasic_search_reasult">
		<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'settings', 'action' => 'advance-Setting', 'id' => $id), $this->translate("Back to Edit Form Settings"), array('class'=>'sesbasic_icon_back buttonlink')) ?>
  	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'forms', 'action' => 'index'), $this->translate("Back to Manage Forms"), array('class'=>'sesbasic_icon_back buttonlink')) ?>
</div>
<div class='clear sesbasic_admin_form'>
	<div class='settings'>
		<?php echo $this->form->render($this); ?>
	</div>
</div>