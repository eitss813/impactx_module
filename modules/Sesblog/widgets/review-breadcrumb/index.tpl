<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: index.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php $tab_id = Engine_Api::_()->sesbasic()->getWidgetTabId(array('name' => 'sesblog.blog-reviews'));  ?>
<div class="sesbasic_breadcrumb">
  <a href="<?php echo $this->content_item->getHref(); ?>"><?php echo $this->content_item->getTitle(); ?></a>&nbsp;&raquo;
  <!--<a href="<?php //echo $this->content_item->getHref(array('tab' => $tab_id)); ?>"><?php //echo $this->translate("Reviews"); ?></a>&nbsp;&raquo;-->
  <?php echo $this->review->getTitle(); ?>
</div>
