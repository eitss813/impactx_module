<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: fields.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> 

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
    //'topLevelId' => (int) @$this->topLevelId,
    //'topLevelValue' => (int) @$this->topLevelValue
  ));
?>
<?php if(!$this->is_ajax) {
  echo $this->partial('dashboard/left-bar.tpl', 'sesblog', array('blog' => $this->blog));	
?>
	<div class="sesbasic_dashboard_content sesbm sesbasic_clearfix">
<?php }  ?>
	<div class="sesbasic_dashboard_form">
		<?php echo $this->form->render() ?>  
	</div>
<?php if(!$this->is_ajax) { ?>
	</div>
<?php } ?>

