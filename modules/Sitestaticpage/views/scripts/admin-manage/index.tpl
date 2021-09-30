<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">

function multiDelete() {
  return confirm("<?php echo 'Are you sure you want to delete the selected static page entries?';?>");
}

function selectAll() {
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

<h2><?php echo 'Static Pages, HTML Blocks and Multiple Forms Plugin'; ?></h2>
<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<h3>
  <?php echo "Manage Static Pages and HTML Blocks"; ?>
</h3>

<br>
<p>
  <?php echo 'This page lists all the Static Pages and HTML Blocks created by you to be displayed on your site. When you create a Static Page from here, then you have the option to create a widgetized page for that Static Page while creating it. You can create new Static Page by using "Create New Static Page / HTML Block" link below.<br />
    You can also create an HTML Block content from here, and choose to place that block at the desired location using the “Static HTML Block” widget from the Layout Editor.
'; ?>
</p>
<br />
<?php if(!empty($this->canCreate)): ?>
<div>
  <?php echo $this->htmlLink(array('action' => 'create', 'reset' => false), 'Create New Static Page / HTML Block', array(
  'class' => 'buttonlink seaocore_icon_add',
  )) ?>
</div>
<br />
<?php endif; ?>

<?php if( count($this->paginator) ): ?>
<form id='multidelete_form' method='post' action='<?php echo $this->url();?>' onSubmit='return multiDelete()'>
<table class='seaocore_admin_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
      <th class='admin_table_short'>ID</th>
      <th><?php echo "Title" ?></th>
      <th><?php echo "Owner" ?></th>
      <th><?php echo "Type" ?></th>
      <th><?php echo "Page URL" ?></th>
      <th><?php echo "Views" ?></th>
      <th><?php echo "Creation Date" ?></th>
      <th><?php echo "Modified Date" ?></th>
      <th><?php echo "Options" ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
    <tr>
      <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->getIdentity(); ?>' value="<?php echo $item->getIdentity(); ?>" /></td>
      <td><?php echo $item->getIdentity() ?></td>
      <td><?php echo $item->getTitle() ?></td>
      <td><?php echo $item->getOwner() ?></td>
      <?php if(!empty($item->type)):?>
      <td><?php echo 'HTML Block'; ?></td>
      <?php else:?>
      <td><?php echo 'Static Page'; ?></td>
      <?php endif;?>
      <?php if(!empty($item->page_url)):?>
      <td><?php echo $item->page_url ?></td>
      <?php else:?>
      <td><?php echo '-'; ?></td>
      <?php endif;?>
      <?php if(!empty($item->type)):?>
      <td><?php echo '-'; ?></td>
      <?php else:?>
      <td><?php echo $this->locale()->toNumber($item->view_count) ?></td>
      <?php endif;?>
      <td><?php echo gmdate('M d,Y',strtotime($item->creation_date)); ?></td>
      <td><?php echo gmdate('M d,Y',strtotime($item->modified_date)); ?></td>
      <td>
        <?php echo $this->htmlLink(
        array('action' => 'edit', 'staticpage_id' => $item->getIdentity(), 'reset'=> false),
        "edit") ?>
        <?php $page_id = Engine_Api::_()->sitestaticpage()->getWidetizedpageId($item->getIdentity());?>
        <?php if(!empty($item->page_url) && !empty($page_id)):?>
        |
        <a href="<?php echo $this->url(array('controller'=>'content','action'=>'index'),'admin_default',TRUE)."?page=$page_id";?>" target="_blank">
    <?php echo "manage layout";?>
</a>
        <?php endif;?>
        
        <?php $page_id = Engine_Api::_()->sitestaticpage()->getMobileWidetizedpageId($item->getIdentity());?>
        <?php if(!empty($item->page_url) && $this->mobile_enabled && $page_id):?>
        |
        <a href="<?php echo $this->url(array('module' => 'sitemobile', 'controller'=>'content','action'=>'index'),'admin_default',TRUE)."?page=$page_id";?>" target="_blank">
    <?php echo "manage mobile layout";?>
</a>
        <?php endif;?>
        |
        <?php if($item->getIdentity() == $this->default_pageid || $item->getIdentity() == 2 || $item->getIdentity() == 3):?>
        <?php echo 'delete'; ?> 
        <?php else:?>
        <?php echo $this->htmlLink(
        array('action' => 'delete', 'staticpage_id' => $item->getIdentity(), 'reset'=> false),
        "delete",
        array('class' => 'smoothbox')) ?>
        <?php endif;?>
        <?php if(!empty($item->page_url)):?>
        |
        <?php echo $this->htmlLink(
        array('route' => 'sitestaticpage_index_index_staticpageid_' . $item->getIdentity() . '', 'action' => 'index', 'staticpage_id' => $item->getIdentity()),
        "view", array('target' => '_blank')) ?>
        <?php endif;?>
        <?php if(!empty($item->page_url)):?>
        |
        <?php echo $this->htmlLink(
        array('action' => 'copy-url', 'staticpage_id' => $item->getIdentity(), 'reset'=> false),
        "URL",
        array('class' => 'smoothbox')) ?>
        <?php endif;?>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
  <br />
<div class='buttons'>
  <button type='submit'><?php echo "Delete Selected" ?></button>
</div>
</form>
<br />
<div>
  <?php echo '<br>' .$this->paginationControl($this->paginator); ?>
</div>
<?php else: ?>
<div class="tip">
  <span>
    <?php echo "There are no page entries by your members yet."; ?>
  </span>
</div>
<?php endif; ?>