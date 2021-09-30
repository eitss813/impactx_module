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
<h3><?php echo "Manage Newsletter Types"; ?></h3>
<p>
	<?php echo $this->translate("This page lists all the newsletter types that you create for your users to subscribe. While creating a new newsletter, you will be able to choose subscriber of which all newsletter types should receive the newsletter from 'Manage Newsletter' section of this plugin.") ?>	
</p>
<br class="clear" />
<div class="sesnewsletter_search_reasult">
	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'managenewslettertype', 'action' => 'create'), $this->translate("Create New Newsletter Type"), array('class'=>'smoothbox sesbasic_icon_add buttonlink')) ?>
</div>
<br />
<?php if( count($this->paginator) ): ?>
  <div class="sesnewsletter_search_reasult">
    <?php echo $this->translate(array('%s newsletter type found.', '%s newsletter types found.', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
  </div>
  <br />
  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
  <table class='admin_table'>
    <thead>
      <tr>
        <!--<th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>-->
        <th class='admin_table_short'>ID</th>
        <th><?php echo $this->translate("Title") ?></th>
        <th align="center"><?php echo $this->translate("Newly Signed-up Members");?></th>
        <th align="center"><?php echo $this->translate("Existing Members");?></th>
        <th align="center"><?php echo $this->translate("Guest Members");?></th>
        <th align="center"><?php echo $this->translate("Status");?></th>
        <th><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <!--<td><input type='checkbox' class='checkbox' name='delete_<?php //echo $item->type_id;?>' value='<?php //echo $item->type_id ?>' /></td>-->
          <td><?php echo $item->type_id ?></td>
          
          <td><?php echo $item->title; ?></td>
          
          <td class="admin_table_centered"><?php echo ( $item->singupuser ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'managenewslettertype', 'action' => 'singupuser', 'id' => $item->type_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesnewsletter/externals/images/check.png', '', array('title' => $this->translate('Disable'))), array()) : $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'managenewslettertype', 'action' => 'singupuser', 'id' => $item->type_id), $this->htmlImage('application/modules/Sesnewsletter/externals/images/error.png', '', array('title' => $this->translate('Enable')))) ) ?></td>
          
          <td class="admin_table_centered"><?php echo ( $item->existinguser ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'managenewslettertype', 'action' => 'existinguser', 'id' => $item->type_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesnewsletter/externals/images/check.png', '', array('title' => $this->translate('Disable'))), array()) : $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'managenewslettertype', 'action' => 'existinguser', 'id' => $item->type_id), $this->htmlImage('application/modules/Sesnewsletter/externals/images/error.png', '', array('title' => $this->translate('Enable')))) ) ?></td>
          
          <td class="admin_table_centered"><?php echo ( $item->guestuser ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'managenewslettertype', 'action' => 'guestuser', 'id' => $item->type_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesnewsletter/externals/images/check.png', '', array('title' => $this->translate('Disable'))), array()) : $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'managenewslettertype', 'action' => 'guestuser', 'id' => $item->type_id), $this->htmlImage('application/modules/Sesnewsletter/externals/images/error.png', '', array('title' => $this->translate('Enable')))) ) ?></td>
          
          
          
          <td class="admin_table_centered"><?php echo ( $item->enabled ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'managenewslettertype', 'action' => 'enabled', 'id' => $item->type_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesnewsletter/externals/images/check.png', '', array('title' => $this->translate('Disable'))), array()) : $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'managenewslettertype', 'action' => 'enabled', 'id' => $item->type_id), $this->htmlImage('application/modules/Sesnewsletter/externals/images/error.png', '', array('title' => $this->translate('Enable')))) ) ?></td>
          <td>            
            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'managenewslettertype', 'action' => 'create', 'id' => $item->type_id), $this->translate("Edit"), array('class' => 'smoothbox')); ?>
            <?php if($item->type_id != 1) { ?>
            |
            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesnewsletter', 'controller' => 'managenewslettertype', 'action' => 'delete', 'id' => $item->type_id), $this->translate("Delete"),array('class' => 'smoothbox')) ?>
            <?php } ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <br />
<!--  <div class='buttons'>
    <button type='submit'><?php //echo $this->translate("Delete Selected") ?></button>
  </div>-->
  </form>
  <br />
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
<?php else: ?>
  <br />
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no newsletter type yet.") ?>
    </span>
  </div>
<?php endif; ?>
