<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
 ?>
<?php include APPLICATION_PATH .  '/application/modules/Sesnewsletter/views/scripts/dismiss_message.tpl';?>
<script type="text/javascript">
function multiDelete()
{
  return confirm("<?php echo $this->translate("Are you sure you want to delete the selected entries?") ?>");
}
function selectAll()
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}
</script>
<h3><?php echo "Manage Newsletters"; ?></h3>
<p>
	<?php echo $this->translate("This page lists all the newsletters created and sent by you to the subscribers. Here, you can also manage these newsletters and see the preview of Newsletter template before sending it to the user in actual. The newsletters will get created as Draft which you can Publish as per your requirement and also schedule it to be published at any later date of your choice.") ?>	
</p>
<br class="clear" />
<div class='admin_search sesbasic_search_form'>
  <?php echo $this->formFilter->render($this) ?>
</div>
<br />
<div class="sesnewsletter_search_reasult">
	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'managecampaigns', 'action' => 'create'), $this->translate("Create New Newsletter"), array('class'=>'sesbasic_icon_add buttonlink')) ?>
</div>
<br />
<?php if( count($this->paginator) ): ?>
  <div class="sesnewsletter_search_reasult">
    <?php echo $this->translate(array('%s newsletter found.', '%s newsletters found', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
  </div>
  <br />
  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
  <table class='admin_table'>
    <thead>
      <tr>
        <!--<th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>-->
        <th class='admin_table_short'>ID</th>
        <th><?php echo $this->translate("Title") ?></th>
        <th><?php echo $this->translate("Creation Date") ?></th>
        <th><?php echo $this->translate("Recipients") ?></th>
        <th align="center"><?php echo $this->translate("Status");?></th>
        <th><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <!--<td><input type='checkbox' class='checkbox' name='delete_<?php //echo $item->campaign_id;?>' value='<?php //echo $item->campaign_id ?>' /></td>-->
          <td><?php echo $item->campaign_id ?></td>
          
          <td><?php echo $item->title; ?></td>
          <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
          <td>
          <?php echo $this->translate("The newsletter has sent to %s recipients in %s", $item->email_count,$item->send_email_count); ?>
          </td>
          <td class="admin_table_centered">
          <?php if($item->status == 0) { ?>
            <?php echo "Draft"; ?>
          <?php } else if($item->status == 1 && $item->publish_type == 1) { ?>
            <?php echo "Published"; ?>
          <?php } else if($item->status == 1 && $item->publish_type == 2) { ?>
            <?php echo "Scheduled"; ?>
          <?php } else if($item->status == 2) { ?>
            <?php echo "Completed"; ?>
          <?php } ?>
          </td>
          <td>
            <?php if($item->status == 0) { ?>
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'managecampaigns', 'action' => 'publish', 'id' => $item->campaign_id), $this->translate("Publish"), array('class' => 'smoothbox')); ?>
              |
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'managecampaigns', 'action' => 'edit', 'id' => $item->campaign_id), $this->translate("Edit")); ?>
              |
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'managecampaigns', 'action' => 'delete', 'id' => $item->campaign_id), $this->translate("Delete"), array('class' => 'smoothbox')); ?>
              |
              <a href="admin/sesnewsletter/managecampaigns/preview/id/<?php echo $item->campaign_id; ?>" target="_blank">Preview</a>
            <?php } else if(in_array($item->status, array(1,2))) {  ?>
              <a href="admin/sesnewsletter/managecampaigns/create/id/<?php echo $item->campaign_id; ?>" target="_blank">Duplicate</a>
            <?php } ?>
            
            <?php if($item->stop == 1 && $item->status == 1) { ?>
              <?php //echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'managecampaigns', 'action' => 'stop', 'id' => $item->campaign_id), $this->translate("Stop")); ?>
            <?php } else if($item->stop == 0 && $item->status == 1) { ?>
              <?php //echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'managecampaigns', 'action' => 'stop', 'id' => $item->campaign_id), $this->translate("Start")); ?>
            <?php } ?>
            
            <?php if($item->status == 2) { ?>
              |
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'managecampaigns', 'action' => 'resend', 'id' => $item->campaign_id), $this->translate("Resend"), array('class' => 'smoothbox')); ?>
            <?php } ?>
            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesnewsletter.enabletestmode', 0)) { ?>
            |
            <a href="admin/sesnewsletter/managecampaigns/testemail/id/<?php echo $item->campaign_id; ?>">Test Email</a>
            <?php } ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <br />
  </form>
  <br />
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
<?php else: ?>
  <br />
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no entries yet.") ?>
    </span>
  </div>
<?php endif; ?>
