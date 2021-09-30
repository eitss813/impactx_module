<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: level-settings.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
?>
<?php include APPLICATION_PATH .  '/application/modules/Sesblog/views/scripts/dismiss_message.tpl';?>

<div class='sesbasic-form sesbasic-categories-form'>
  <div>
    <?php if( count($this->subNavigation) ): ?>
      <div class='sesbasic-admin-sub-tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->subNavigation)->render();?>
      </div>
    <?php endif; ?>
    <div class='sesbasic-form-cont'>
    <div class='clear'>
		  <div class='settings sesbasic_admin_form'>
		    <?php echo $this->form->render($this); ?>
		  </div>
		</div>
		</div>
  </div>
</div>
<script type="text/javascript">
  $('level_id').addEvent('change', function() {
    var url = '<?php echo $this->url(array("id" => null)) ?>';
    window.location.href = url + '/id/' + this.get('value');
  });
</script>
