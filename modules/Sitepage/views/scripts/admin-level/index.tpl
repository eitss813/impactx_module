<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  <?php $user = Engine_Api::_()->user()->getViewer(); ?>
	window.addEvent('domready', function() {
		contactoption('<?php echo Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'contact') ?>');
	});

	var fetchLevelSettings =function(level_id){
		window.location.href= en4.core.baseUrl+'admin/sitepage/level/index/id/'+level_id;
	}

	function contactoption(option) {
		if(option == 1) {
			if($('contact_detail-wrapper')) {
				$('contact_detail-wrapper').style.display = 'block';
			}
		} 
		else {
			if($('contact_detail-wrapper')) {
				$('contact_detail-wrapper').style.display = 'none';
			}
		}
	}
</script>
<h2 class="fleft"><?php echo $this->translate('Directory / Pages Plugin'); ?></h2>
<?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/manageExtensions.tpl'; ?>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='clear seaocore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>
