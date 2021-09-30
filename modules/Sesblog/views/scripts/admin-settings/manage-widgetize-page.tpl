<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: manage-widgetized-page.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php include APPLICATION_PATH .  '/application/modules/Sesblog/views/scripts/dismiss_message.tpl'; ?>

<?php $widgetPgsArray = array('sesblog_index_welcome', 'sesblog_index_home', 'sesblog_index_browse', 'sesblog_category_browse', 'sesblog_index_locations', 'sesblog_review_browse', 'sesblog_index_manage', 'sesblog_index_claim', 'sesblog_index_create', 'sesblog_index_view_1', 'sesblog_index_view_2','sesblog_index_view_3','sesblog_index_view_4', 'sesblog_index_tags', 'sesblog_review_view', 'sesblog_album_view', 'sesblog_photo_view', 'sesblog_index_claim-requests', 'sesblog_index_list', 'sesblog_category_index'); ?>

<h3><?php echo $this->translate("Links to Widgetized Pages") ?></h3>
<p><?php echo $this->translate('This page lists all the Widgetized Pages of this plugin. From here, you can easily go to particular widgetized page in "Layout Editor" by clicking on "Widgetized Page" link. The user side link of the Page can be viewed by clicking on "User Page" link.'); ?></p>
<br />

<table class='admin_table'>
  <thead>
    <tr>
      <th><?php echo $this->translate("Page Name") ?></th>
      <th><?php echo $this->translate("Option") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($widgetPgsArray as $item): ?>
      <?php $widgetPge = Engine_Api::_()->sesblog()->getwidgetizePage(array('name' => $item));
      $page = explode("_",$widgetPge->name);
      ?>
      <tr>
        <td><?php echo $widgetPge->displayname; ?></td>
        <td>
          <a href="<?php echo $this->url(array('module' => 'core', 'controller' => 'content', 'action' => 'index'), 'admin_default').'?page='.$widgetPge->page_id; ?>"  target="_blank"><?php echo "Widgetized Page";?></a>

          <?php if($item != 'sesblog_index_view_1' && $item != 'sesblog_index_view_2' && $item != 'sesblog_index_view_3' && $item != 'sesblog_index_view_4' && $item != 'sesblog_album_view' && $item != 'sesblog_photo_view' && $item != 'sesblog_index_list' && $item != 'sesblog_category_index' && $item != 'sesblog_review_view' && $item != 'sesblog_index_claim-requests' && $item != 'sesblog_index_claim'):?>
            &nbsp;|&nbsp;
            <?php $viewPageUrl = $this->url(array('module' => $page[0], 'controller' => $page[1], 'action' => $page[2]), 'default');?>
            <a href="<?php echo $viewPageUrl; ?>" target="_blank"><?php echo $this->translate("User Page") ?></a>
          <?php endif;?>
					&nbsp;|&nbsp;
          <a title="<?php echo $this->translate('Reset Page'); ?>" href="<?php echo $this->url(array('module'=> 'sesblog', 'controller' => 'settings', 'action' => 'reset-page-settings', 'page_id' => $widgetPge->page_id, 'page_name' => $item,'format' => 'smoothbox'),'admin_default',true); ?>" class=" smoothbox"><?php echo $this->translate('Reset Page'); ?></a>

        <?php if($item == 'sesblog_index_welcome') :?>
          &nbsp;|&nbsp;
          <a title="<?php echo $this->translate('Set as Landing Page'); ?>" href="<?php echo $this->url(array('module'=> 'sesblog', 'controller' => 'settings', 'action' => 'landingpagesetup', 'page_id' => $widgetPge->page_id, 'page_name' => $item,'format' => 'smoothbox'),'admin_default',true); ?>" class=" smoothbox"><?php echo $this->translate('Set as Landing Page'); ?></a>
        <?php endif;?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
