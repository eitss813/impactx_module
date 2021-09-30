<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: photo-of-day.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/pluginLink.tpl'; ?>
<h2>
  <?php echo $this->translate("Directory / Pages - Albums Extension") ?>
</h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<h3>
  <?php echo $this->translate("Photo of the Day") ?>
</h3>
<?php if( count($this->subNavigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->subNavigation)->render()
    ?>
  </div>
<?php endif; ?>
<p>
  <?php echo $this->translate('Below you can manage the entries for "Photo of the Day" widget. To mark a photo, please click on the "Add a Photo of the Day" link below and select the dates. If more than one photos of the day are found for a date then randomly one will be displayed. Note that for the "Photo of the Day" to be shown, you must first place its widget at the desired location.') ?>
</p>
<br />
<div class="tip"> <span> <?php echo $this->translate('You should only make those photos as "Photo of the Day" which have their page view privacy set as \'Everyone\' or \'All Registered Members\'.'); ?> </span> </div>
<br />
<div>
  <a href="<?php echo $this->url(array('action' =>'add-photo-of-day')) ?>" class="smoothbox buttonlink seaocore_icon_add" title="<?php echo $this->translate('Add a Photo of the Day');?>"><?php echo $this->translate('Add a Photo of the Day');?></a>
</div>
<br />
<div>
	<?php echo $this->photoOfDaysList->getTotalItemCount(). $this->translate(' results found');?>
</div>
<br />
<div>
	<?php if ($this->photoOfDaysList->getTotalItemCount() > 0): ?>
		<div class='admin_search'>
			<?php echo $this->formFilter->render($this) ?>
		</div>
	  <form id='multidelete_form' method="post" action="<?php echo $this->url(array('module' => 'sitepagealbum', 'controller' => 'settings', 'action' => 'multi-delete-photo'), 'admin_default'); ?>" onSubmit="return multiDelete()">
		  <table class='admin_table' width="100%">
		    <thead>
		      <tr>
						<th style='width: 1%;' align="left"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
						<?php $class = ( $this->order == 'engine4_sitepage_photos.title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
						<th width="18%" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_sitepage_photos.title', 'DESC');"><?php echo $this->translate("Photo") ?></a></th>
						<th width="18%" align="left"><?php echo $this->translate("Album") ?></th>
            <th width="18%" align="left"><?php echo $this->translate("Page Name") ?></th>
						<?php $class = ( $this->order == 'start_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
						<th width="18%" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('start_date', 'DESC');"><?php echo $this->translate("Start Date") ?></a></th>
						<?php //Start End date work  ?>
						<?php $class = ( $this->order == 'end_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
						<th width="18%" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('end_date', 'DESC');"><?php echo $this->translate("End Date") ?></a></th>
						<th width="18%" align="left"><?php echo $this->translate("Options"); ?></th>
		      </tr>
		    </thead>
		    <tbody>
		      <?php $auth = Engine_Api::_()->authorization()->context; ?>
		      <?php foreach ($this->photoOfDaysList as $photo): ?>
          <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
						$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.photos-sitepage', $photo->page_id, $layout);?>
          <?php $sitepagephoto_object = Engine_Api::_()->getItem('sitepage_photo', $photo->resource_id);?>
		      <?php  $parent = Engine_Api::_()->getItem('sitepage_album', $photo->album_id);?>
		        <td width="1%"><input name='delete_<?php echo $photo->itemoftheday_id; ?>' type='checkbox' class='checkbox' value="<?php echo $photo->itemoftheday_id ?>"/></td>
		        <td width="18%" class="sitealbum_table_img"><?php echo $this->htmlLink($sitepagephoto_object->getHref(),$this->itemPhoto($sitepagephoto_object, 'thumb.normal'),array('title'=>$sitepagephoto_object->getTitle())); ?></td>
		        <td width="18%" class="admin_table_bold"><?php echo $this->htmlLink($parent->getHref(array('tab' => $tab_id)), $parent->getTitle()); ?></td>
            <?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $photo->page_id);?>
            <?php             
             	$truncation_limit = 13;
							$tmpBodytitle = strip_tags($sitepage_object->title);
							$item_sitepagetitle = ( Engine_String::strlen($tmpBodytitle) > $truncation_limit ? Engine_String::substr($tmpBodytitle, 0, $truncation_limit) . '..' : $tmpBodytitle );             
            ?>          
              
						<td width="18%" class='admin_table_bold'><?php echo $this->htmlLink($sitepage_object->getHref(), $item_sitepagetitle, array('title' => $sitepage_object->title, 'target' => '_blank')) ?></td>

		        <td width="18%"><?php echo $this->translate(gmdate('M d,Y',strtotime($photo->start_date)))?></td>
		        <td width="18%"><?php echo $this->translate(gmdate('M d,Y',strtotime($photo->end_date)))?></td>
		        <td width="18%">
		          <a href='<?php echo $this->url(array('action' => 'delete-photo-of-day', 'id' => $photo->itemoftheday_id)) ?>' class="smoothbox" title="<?php echo $this->translate("delete") ?>">
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
		<div class="tip"><span><?php echo $this->translate("No photos have been marked as Photo of the Day."); ?></span> </div>
	<?php endif;?>
	<br />
	<?php echo $this->paginationControl($this->photoOfDaysList); ?>
</div>


<script type="text/javascript">

  function multiDelete()
  {
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected photos?")) ?>');
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