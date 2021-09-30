<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/pluginLink.tpl'; ?>
<h2><?php echo $this->translate('Directory / Pages - Videos Extension'); ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<h3>
  <?php echo $this->translate('Manage Page Videos'); ?>
</h3>

<p>
  <?php echo $this->translate('Here, you can see all the videos your users have created for the Pages on your site. Here you can monitor these videos and delete offensive ones if necessary. You can also make page videos featured / un-featured, highlighted / un-highlighted by clicking on the corresponding icons. 
(Note: Highlighted videos will be distinguished from the rest videos by highlighted background.)');?>
</p>

<br />

<div class='admin_members_results'>
  <?php 
  	if( !empty($this->paginator) ) {
  		$counter=$this->paginator->getTotalItemCount(); 
  	}
  	if(!empty($counter)): 
  
  ?>
		<div class="">
			<?php  echo $this->translate(array('%s page video found.', '%s page videos found.', $counter), $this->locale()->toNumber($counter)) ?>
		</div>
  <?php else:?>
		<div class="tip"><span>
			<?php  echo $this->translate("No results were found.") ?></span>
		</div>
  <?php endif; ?>
</div>

<script type="text/javascript">

  function killProcess(video_id) {
    (new Request.JSON({
      'format': 'json',
      'url' : '<?php echo $this->url(array('module' => 'sitepagevideo', 'controller' => 'admin-manage', 'action' => 'kill'), 'default', true) ?>',
      'data' : {
        'format' : 'json',
        'video_id' : video_id
      },
      'onRequest' : function(){
        $$('input[type=radio]').set('disabled', true);
      },
      'onSuccess' : function(responseJSON, responseText)
      {
        window.location.reload();
      }
    })).send();

  }
  en4.core.runonce.add(function(){$$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){ $$('input[type=checkbox]').set('checked', $(this).get('checked', false)); })});

  var delectSelected =function(){
    var checkboxes = $$('input[type=checkbox]');
    var selecteditems = [];

    checkboxes.each(function(item, index){
      var checked = item.get('checked', false);
      var value = item.get('value', false);
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });

    $('ids').value = selecteditems;
    $('delete_selected').submit();
  }

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

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<br />

<?php  if( !empty($counter)):?>
  <table class='admin_table seaocore_admin_table'>
    <thead>
      <tr>
        <th style='width: 1%;' class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('video_id', 'DESC');"><?php echo $this->translate('ID'); ?></a></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Title'); ?></a></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate('Owner');?></a></th>
				<th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('sitepage_title', 'ASC');"><?php echo $this->translate('Page Title');?></a></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('highlighted', 'ASC');"><?php echo $this->translate('Highlighted'); ?></a></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'ASC');"><?php echo $this->translate('Featured'); ?></a></th>
        <th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('view_count', 'ASC');"><?php echo $this->translate('Views'); ?></a></th>
        <th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('comment_count', 'ASC');"><?php echo $this->translate('Comments'); ?></a></th>
        <th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('like_count', 'ASC');"><?php echo $this->translate('Likes'); ?></a></th>
        <th style='width: 1%;'><?php echo $this->translate('Type'); ?>
        </th>
        <th style='width: 1%;'><?php echo $this->translate("State") ?></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate('Creation Date'); ?></a></th>
        <th style='width: 1%;' class='admin_table_options'><?php echo $this->translate('Options'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php  if( !empty($counter)):?>
        <?php foreach( $this->paginator as $item ): ?>
          <tr>
            <td><input name='delete_<?php echo $item->video_id;?>' type='checkbox' class='checkbox' value="<?php echo $item->video_id ?>"/></td>
            
            <td><?php echo $item->video_id ?></td>
            <?php             
             	$truncation_limit = 13;
							$tmpBody = strip_tags($item->title);
							$item_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
            ?>
            <td class='admin_table_bold'><?php echo $this->htmlLink($item->getHref(), $item_title, array('title' => $item->title, 'target' => '_blank')) ?></td>
            
            <td class='admin_table_bold'><?php  echo $this->htmlLink($this->item('user', $item->owner_id)->getHref(), $item->truncateOwner($this->user($item->owner_id)->username), array('title' => $item->username, 'target' => '_blank')) ?></td>
						
            <?php             
             	$truncation_limit = 13;
							$tmpBodytitle = strip_tags($item->sitepage_title);
							$item_sitepagetitle = ( Engine_String::strlen($tmpBodytitle) > $truncation_limit ? Engine_String::substr($tmpBodytitle, 0, $truncation_limit) . '..' : $tmpBodytitle );
            ?>	
            <td class='admin_table_bold'>
              <?php if(!empty($item->page_id)):?>
								<?php echo $this->htmlLink($this->item('sitepage_page', $item->page_id)->getHref(),$item_sitepagetitle, array('title' => $item->sitepage_title, 'target' => '_blank')) ?>
							<?php endif;?>
            </td>
           <?php if($item->highlighted == 1):?>
								<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'sitepagevideo_highlightedvideo', 'id' => $item->video_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/icons/highlighted.png', '', array('title'=> $this->translate('Highlighted')))) ?> 
								</td>       
							<?php else: ?>  
								<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'sitepagevideo_highlightedvideo', 'id' => $item->video_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/icons/unhighlighted.png', '', array('title'=> $this->translate('Un-highlighted')))) ?>
								</td>
							<?php endif; ?>
           <?php if($item->featured == 1):?>
								<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'sitepagevideo_featuredvideo', 'id' => $item->video_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/featured.png', '', array('title'=> $this->translate('Featured')))) ?> 
								</td>       
							<?php else: ?>  
								<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'sitepagevideo_featuredvideo', 'id' => $item->video_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/unfeatured.png', '', array('title'=> $this->translate('Un-featured')))) ?>
								</td>
							<?php endif; ?>
									
            <td align="center" class="admin_table_centered"><?php echo $item->view_count ?></td>
            
            <td align="center" class="admin_table_centered"><?php echo $item->comment_count ?></td>
            
            <td align="center" class="admin_table_centered"><?php echo $item->like_count ?></td>
            <td>
							<?php
								switch( $item->type ) {
									case 1:
										$type = $this->translate("YouTube");
										break;
									case 2:
										$type = $this->translate("Vimeo");
										break;
									case 3:
										$type = $this->translate("Uploaded");
										break;
									default:
										$type = $this->translate("Unknown");
										break;
								}
								echo $type;
							?>
            </td>
            <td>
							<?php
								switch ($item->status){
									case 0:
										$status = $this->translate("queued");
										break;
									case 1:
										$status = $this->translate("ready");
										break;
									case 2:
										$status = $this->translate("processing");
										break;
									default:
										$status = $this->translate("failed");
								}
								echo $status;
							?>
							<?php if($item->status == 2):?>
							(<a href="javascript:void(0);" onclick="javascript:killProcess('<?php echo $item->video_id?>');">
								<?php echo $this->translate("end"); ?>
							</a>)
							<?php endif;?>
            </td>
            <td align="center"><?php echo gmdate('M d,Y g:i A',strtotime($item->creation_date)) ?></td>
            
            <td class='admin_table_options'>
							 
						<?php echo $this->htmlLink($item->getHref(), 'view', array('target' => '_blank')) ?>
						</a>
						|
						<?php echo $this->htmlLink(array('route' => 'sitepagevideoadmin_delete', 'video_id' => $item->video_id,'page_id' => $item->page_id), $this->translate('delete'), array(
							'class' => 'smoothbox',
						)) ?> 
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  <br />
  <?php  echo $this->paginationControl($this->paginator); ?><br  />
  <div class='buttons'>
		<button onclick="javascript:delectSelected();" type='submit'>
			<?php echo $this->translate("Delete Selected") ?>
		</button>
 </div>  

	<form id='delete_selected' method='post' action='<?php echo $this->url(array('action' =>'delete-selected')) ?>'>
		<input type="hidden" id="ids" name="ids" value=""/>
	</form>
<?php endif; ?>