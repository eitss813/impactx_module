<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: edit-category.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php include APPLICATION_PATH .  '/application/modules/Sesblog/views/scripts/dismiss_message.tpl';?>
<div class='clear sesbasic-form'>
  <div>
    <?php if( count($this->subNavigation) ): ?>
      <div class='sesbasic-admin-sub-tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->subNavigation)->render(); ?>
      </div>
    <?php endif; ?>
     <?php if( count($this->subsubNavigation) ): ?>
      <div class='sesbasic-admin-sub-tabs sesbasic-admin-sub-inner-tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->subsubNavigation)->render();?>
      </div>
    <?php endif; ?>
    <div class="sesbasic-form-cont">
     <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesblog', 'controller' => 'review-categories', 'action' => 'index'), $this->translate("Back to Manage Categories"), array('class'=>'sesbasic_icon_back buttonlink')) ?>
      <br /><br />
      <div class='settings sesbasic_admin_form'>
        <?php echo $this->form->render($this); ?>
      </div>
    </div>
  </div>
</div>
