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
  return confirm("<?php echo $this->translate("Are you sure you want to delete the selected entries ?") ?>");
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
<h3><?php echo "Manage Subscribers"; ?></h3>
<p>
	<?php echo $this->translate("This page lists all the subscribers who have subscribed to the Newsletters on your website. Here, you can also manage these subscribers and delete any if required.") ?>	
</p>
<br class="clear" />
<div class='admin_search sesbasic_search_form'>
  <?php echo $this->formFilter->render($this) ?>
</div>
<br />
<div class="sesnewsletter_search_reasult">
	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'manage', 'action' => 'addsubscriber'), $this->translate("Add New Subscriber"), array('class'=>'smoothbox sesbasic_icon_add buttonlink')) ?>
</div>
<br />
<?php if( count($this->paginator) ): ?>
  <div class="sesnewsletter_search_reasult">
    <?php echo $this->translate(array('%s subscriber found.', '%s subscribers found', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
  </div>
  <br />
  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
  <table class='admin_table'>
    <thead>
      <tr>
        <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
        <th class='admin_table_short'>ID</th>
        <th><?php echo $this->translate("Display Name") ?></th>
        <th><?php echo $this->translate("Subscriber Email") ?></th>
        <th><?php echo $this->translate("Member Type") ?></th>
        <th align="center"><?php echo $this->translate("Status");?></th>
        <th><?php echo $this->translate("Option") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <?php if($item->resource_id) { ?>
          <?php $resource = Engine_Api::_()->getItem('user', $item->resource_id); ?>
        <?php } ?>
        <tr>
          <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->subscriber_id;?>' value='<?php echo $item->subscriber_id ?>' /></td>
          <td><?php echo $item->subscriber_id ?></td>
          <td>
            <?php if($item->resource_id) { ?>
              <a href="<?php echo $resource->getHref(); ?>"><?php echo $resource->getTitle(); ?></a>
            <?php } else { ?>
              <?php echo "---"; ?>
            <?php } ?>
          </td>
          
          <td><?php echo $item->email; ?></td>
          <td>
            <?php if($item->resource_type == 'user') { ?>
              <?php echo "Site Member"; ?>
            <?php } else { ?>
              <?php echo "Guest"; ?>
            <?php } ?>
          </td>
          <td class="admin_table_centered"><?php echo ( $item->enabled ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'manage', 'action' => 'enabled', 'id' => $item->subscriber_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesnewsletter/externals/images/check.png', '', array('title' => $this->translate('Disable'))), array()) : $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'manage', 'action' => 'enabled', 'id' => $item->subscriber_id), $this->htmlImage('application/modules/Sesnewsletter/externals/images/error.png', '', array('title' => $this->translate('Enable')))) ) ?></td>
          <td>
            <?php //echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'manage', 'action' => 'edit', 'id' => $item->subscriber_id, 'resource_type' => $item->resource_type), $this->translate("Edit")); ?>
            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'manage', 'action' => 'delete', 'id' => $item->subscriber_id), $this->translate("Delete"),array('class' => 'smoothbox')) ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <br />

  <div class='buttons'>

    <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
  </div>
  </form>

  <br />

  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>

<?php else: ?>
  <br />
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no subscribers yet.") ?>
    </span>
  </div>
<?php endif; ?>
