<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/pluginLink.tpl'; ?>
<h2><?php echo $this->translate('Directory / Pages - Page Members Extension'); ?></h2>
<div class='tabs'>
	<?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
</div>
<h3>
  <?php echo $this->translate('Member of the Day'); ?>
</h3>
<p>
  <?php echo $this->translate('Below, you can manage the entries for "Member of the Day" widget. To mark a member, please click on the "Add Member of the Day" link below and select the dates. If more than one members of the day are found for a date then randomly one will be displayed.') ?>
</p>
<br />

<div>
  <a href="<?php echo $this->url(array('action' =>'add-member-of-day')) ?>" class="smoothbox buttonlink seaocore_icon_add" title="<?php echo $this->translate('Add Member of the Day');?>"><?php echo $this->translate('Add Member of the Day');?></a>
</div>
<br />

<?php if ($this->memberOfDaysList->getTotalItemCount() > 0): ?>
<div>
<?php echo $this->memberOfDaysList->getTotalItemCount(). $this->translate(' results found.');?>
</div>
<br />
<?php endif; ?>


<div>
	<?php if ($this->memberOfDaysList->getTotalItemCount() > 0): ?>
		<div class='admin_search'>
			<?php echo $this->formFilter->render($this) ?>
		</div>
	  <form id='multidelete_form' method="post" action="<?php echo $this->url(array('module' => 'sitepagemember', 'controller' => 'widgets', 'action' => 'multi-delete-member'), 'admin_default'); ?>" onSubmit="return multiDelete()">
		  <table class='admin_table' width="100%">
		    <thead>
		      <tr>
						<th style='width: 1%;' align="left"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
						<?php $class = ( $this->order == 'engine4_users.username' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
						<th width="24%" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_users.username', 'DESC');"><?php echo $this->translate("Members") ?></a></th>
						<?php $class = ( $this->order == 'start_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
						<th width="24%" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('start_date', 'DESC');"><?php echo $this->translate("Start Date") ?></a></th>
						<?php //Start End date work  ?>
						<?php $class = ( $this->order == 'end_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
						<th width="24%" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('end_date', 'DESC');"><?php echo $this->translate("End Date") ?></a></th>
						<th width="24%" align="left"><?php echo $this->translate("Options");?></th>
		      </tr>
		    </thead>
		    <tbody>
		      <?php foreach ($this->memberOfDaysList as $member): ?>
						<td width="1%"><input name='delete_<?php echo $member->itemoftheday_id; ?>' type='checkbox' class='checkbox' value="<?php echo $member->itemoftheday_id ?>"/></td>
						<td width="24%" class=""><?php echo $this->htmlLink($this->item('user', $member->user_id)->getHref()	, $this->item('user', $member->user_id)->displayname, array('title' => $this->item('user', $member->user_id)->displayname, 'target' => '_blank')) ?></td>
						<td width="24%"> <?php echo $this->translate(gmdate('M d,Y',strtotime($member->start_date)))?></td>
						<td width="24%"> <?php echo $this->translate(gmdate('M d,Y',strtotime($member->end_date)))?></td>
						<td width="24%">
						<a href='<?php echo $this->url(array('action' => 'delete-member-of-day', 'id' => $member->itemoftheday_id)) ?>' class="smoothbox" title="<?php echo $this->translate("delete") ?>">
						<?php echo $this->translate("delete") ?>
						</a>
						</td>
		      </tr>
		      <?php endforeach;?>
		    </tbody>
		  </table>
		  <br />
		  <div class='buttons'>
		  	<button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
		  </div>
		</form>
  <?php else: ?>
		<div class="tip"><span><?php echo $this->translate("No members have been marked as Member of the Day."); ?></span> </div>
  <?php endif;?>
	<br />
	<?php echo $this->paginationControl($this->memberOfDaysList); ?>
</div>
<script type="text/javascript">

  function multiDelete()	{
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected members?")) ?>');
  }

  function selectAll()	{
    var i;
    var multidelete_form = $('multidelete_form');
    var inputs = multidelete_form.elements;
    for (i = 1; i < inputs.length - 1; i++) {
      if (!inputs[i].disabled) {
        inputs[i].checked = inputs[0].checked;
      }
    }
  }
</script>

<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){

    if( order == currentOrder ) {
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }
</script>