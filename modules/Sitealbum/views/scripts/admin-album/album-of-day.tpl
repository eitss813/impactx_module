<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: album-of-day.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
  <?php echo $this->translate("Advanced Photo Albums Plugin") ?>
</h2>
<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
<?php endif; ?>
<h3>
  <?php echo $this->translate("Album of the Day") ?>
</h3>
<?php if( count($this->subNavigation) ): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->subNavigation)->render(); ?>
  </div>
<?php endif; ?>
<p>
  <?php echo $this->translate('Below you can manage the entries for "Album of the Day" widget. To mark an album, please click on the "Add an Album of the Day" link below and select the dates. If more than one albums of the day are found for a date then randomly one will be displayed. Note that for the "Album of the Day" to be shown, you must first place its widget at the desired location.') ?>
</p>
<br />
<div class="tip"> <span> <?php echo $this->translate('You should only make those albums as "Album of the Day" which have their view privacy set as \'Everyone\' or \'All Registered Members\'; If you have made "Album of the Day" an album which has a different privacy, then its entry below will be specially highlighted.'); ?> </span> </div>
<br />
<div>
  <a href="<?php echo $this->url(array('action' =>'add-album-of-day')) ?>" class="smoothbox buttonlink seaocore_icon_add" title="<?php echo $this->translate('Add an Album of the Day');?>"><?php echo $this->translate('Add an Album of the Day');?></a>
</div>
<br />
<div>
<?php echo $this->albumOfDaysList->getTotalItemCount(). $this->translate(' results found');?>
</div>
<br />
<div>
	<?php if ($this->albumOfDaysList->getTotalItemCount() > 0): ?>
		<div class='admin_search'>
			<?php echo $this->formFilter->render($this) ?>
		</div>
	  <form id='multidelete_form' method="post" action="<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'album', 'action' => 'multi-delete-album'), 'admin_default'); ?>" onSubmit="return multiDelete()">
		  <table class='admin_table' width="100%">
		    <thead>
		      <tr>
						<th style='width: 1%;' align="left"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
						<?php $class = ( $this->order == 'engine4_album_albums.title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
						<th width="24%" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_album_albums.title', 'DESC');"><?php echo $this->translate("Album") ?></a></th>
						<?php $class = ( $this->order == 'start_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
						<th width="24%" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('start_date', 'DESC');"><?php echo $this->translate("Start Date") ?></a></th>
						<?php //Start End date work  ?>
						<?php $class = ( $this->order == 'end_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
						<th width="24%" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('end_date', 'DESC');"><?php echo $this->translate("End Date") ?></a></th>
						<th width="24%" align="left"><?php echo $this->translate("Options");?></th>
		      </tr>
		    </thead>
		    <tbody>
		       <?php $auth = Engine_Api::_()->authorization()->context; ?>
		      <?php foreach ($this->albumOfDaysList as $album): ?>
		      <?php if( 1 === $auth->isAllowed($album, 'everyone', 'view') ||  1 === $auth->isAllowed($album, 'registered', 'view')  ) :?>
		      	<tr>
		      <?php else:?>
		       <tr class="sitealbum_list_highlighted">
		      <?php endif; ?>
						<td width="1%"><input name='delete_<?php echo $album->itemoftheday_id; ?>' type='checkbox' class='checkbox' value="<?php echo $album->itemoftheday_id ?>"/></td>
						<td width="24%" class="sitealbum_table_img"><?php echo $this->htmlLink($album->getHref(),$this->itemPhoto($album, 'thumb.normal'),array('title'=>$album->getTitle())); ?></td>
						<td width="24%"> <?php echo $this->translate(gmdate('M d,Y',strtotime($album->start_date)))?></td>
						<td width="24%"> <?php echo $this->translate(gmdate('M d,Y',strtotime($album->end_date)))?></td>
						<td width="24%">
						<a href='<?php echo $this->url(array('action' => 'delete-album-of-day', 'id' => $album->itemoftheday_id)) ?>' class="smoothbox" title="<?php echo $this->translate("delete") ?>">
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
		<div class="tip"><span><?php echo $this->translate("No albums have been marked as Album of the Day."); ?></span> </div>
  <?php endif;?>
	<br />
	<?php echo $this->paginationControl($this->albumOfDaysList); ?>
</div>
<script type="text/javascript">

  function multiDelete()
  {
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected album ?")) ?>');
  }

  function selectAll()
  {
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