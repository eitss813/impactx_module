<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
	var importfile_id;
	function startImporting(file_id) {
	  var confirmation =  confirm('If you want import profile photos also with the page import then use "Upload Photos" link for photos." \n\n If you do not want upload photos or already uploaded then click on "Ok" button otherwise "click" on Cancel button.');
	  if(confirmation == true) {
			Smoothbox.open($('startImporting').innerHTML);
			importfile_id = file_id;
			en4.core.request.send(new Request({
				url : en4.core.baseUrl+'admin/sitepage/importlisting/data-import?importfile_id='+importfile_id,
				method: 'get',
				data : {
					//'format' : 'json',
				},

				onSuccess : function(responseJSON) {
					parent.window.location.reload();
					parent.Smoothbox.close();
						
					}
			}))
		}
	}

	function stopImoprt() {

	var request = new Request.JSON({
  	'url' : en4.core.baseUrl+'admin/sitepage/importlisting/stop?importfile_id='+importfile_id,
  	'data' : {
    					'format' : 'json'
             
  					},
  	onSuccess : function(responseJSON) {
  		parent.window.location.reload();
				parent.Smoothbox.close();
  	}
	});
	request.send();
	}

	function multiDelete()
	{
		return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure that you want to delete the selected file entries ? These will not be recoverable after being deleted. Note that deleting them will also delete the corresponding entries which were going to be used to import the Pages from those files.")) ?>');
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

<div id="startImporting" style="display:none;">
	<center class="bold">
		<?php echo $this->translate("Importing pages..."); ?>
	</center>
	<center class="mtop10">
		<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/admin/loader.gif" alt="Importing pages" />
	</center>
	<br />
	<center><button name="submit" id="submit" type="submit" onclick='stopImoprt();'><?php echo $this->translate("Stop");?></button></center>
</div>

<h2 class="fleft"><?php echo $this->translate('Directory / Pages Plugin'); ?></h2>
<?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/manageExtensions.tpl'; ?>
<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'> <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?> </div>
<?php endif; ?>

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'importlisting', 'action' => 'index'), $this->translate('Import a new file'), array('class'=> 'buttonlink icon_sitepage_admin_import')) ?>

<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'log', 'action' => 'index'), $this->translate('Import History'), array('class'=> 'buttonlink icon_sitepages_log')) ?><br/><br/>

<?php if($this->paginator->getTotalItemCount()): ?>	

	<h3><?php echo $this->translate("Manage CSV Import Files"); ?></h3>
	<p class="form-description"><?php echo $this->translate('This page contains all the CSV files uploaded by you for importing Pages from them. You can start, stop, rollback and delete the import corresponding to each file now. Rollback will delete all the pages imported from that file. Delete will only delete those entries which were going to be used to import the corresponding Pages from that file and also delete the entry of that file from here. Upload the folder containing profile pictures for the pages uploaded via the csv file by using the ‘Upload Photos” link. Below are the meanings of status for the Files uploaded:'); ?></p> 

	<ul class="importlisting_form_list">

		<li>
			<?php echo $this->translate("Pending: You have not started the page importing from this file. Click on 'start' link and start importing.");?>
		</li>

		<li>
			<?php echo $this->translate("Running: You have started the page importing from this file.");?>
		</li>

		<li>
			<?php echo $this->translate("Stopped: You have stopped the page importing from this file. You can continue it anytime from the same point.");?>
		</li>

		<li>
			<?php echo $this->translate("Completed: Page importing has been done successfully from this file.");?>
		</li>

	</ul>
	<br />
	<p class="form-description"><?php echo $this->translate('NOTE: Please start only one import at a time. Initializing more than 1 parallel imports may create problems.') ?></p> 
	<br /><br />

	<div class="admin_files_pages">
		<?php $pageInfo = $this->paginator->getPages(); ?>
		<?php echo $this->translate("Showing ");?><?php echo $pageInfo->firstItemNumber ?>-<?php echo $pageInfo->lastItemNumber ?><?php echo $this->translate(" of "); ?><?php echo $pageInfo->totalItemCount ?><?php echo $this->translate(" files.")?>
	</div>

	<form id='multidelete_form' name='multidelete_form' method="post" action="<?php echo $this->url(array('action'=>'multi-delete'));?>" >
		<table class='admin_table' width='100%'>
			<thead>
				<tr>
					<th width="1%" align="left"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
					<th width="1%" align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('importfile_id', 'ASC');"><?php echo $this->translate('Id'); ?></a></th>
					<th width="30%" align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('filename', 'ASC');"><?php echo $this->translate('File Name'); ?></a></th>
					<th width="15%" align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'ASC');"><?php echo $this->translate('Upload Date / Time'); ?></a></th>							
					<th width="10%" align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('status', 'ASC');"><?php echo $this->translate('Status'); ?></a></th>
					<th width="20%" align="left"><?php echo $this->translate('Options'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach( $this->paginator as $item ):?>
				<tr>
					<td width="1%">
						<input name='delete_<?php echo $item->importfile_id;?>' type='checkbox' class='checkbox' value="<?php echo $item->importfile_id ?>"/>
					</td>
					<td width="1%">
						<?php echo $item->importfile_id ?>
					</td>
			
					<td width="30%">
						<span title='<?php echo $item->filename; ?>'>
							<?php $widget_title = strip_tags($item->filename);
							$widget_title = Engine_String::strlen($widget_title) > 50 ? Engine_String::substr($widget_title, 0, 50) . '..' : $widget_title;?>
							<?php echo $widget_title ?>
						</span>
					</td>

					<td width="15%">
						<?php echo $item->creation_date ?>
					</td>
						
					<td width="10%">
						<span title='<?php echo $item->status; ?>'>
							<?php if($item->status == 'Pending'):?>
								<?php echo $this->translate("Pending"); ?>
							<?php elseif($item->status == 'Running'):?>
								<?php echo $this->translate("Running"); ?>
							<?php elseif($item->status == 'Completed'):?>
								<?php echo $this->translate("Completed"); ?>
							<?php elseif($item->status == 'Stopped'):?>
								<?php echo $this->translate("Stopped"); ?>
							<?php endif; ?>
						</span>
					</td>

					<td width="20%" style="text-align:left;">
						<?php if($item->status != 'Running'): ?>
							<?php if($item->status != 'Completed' && empty($this->runningSomeImport)): ?>
								<a href="javascript:void(0);" onclick='startImporting("<?php echo $item->importfile_id; ?>");'><?php echo $this->translate('Start') ?></a>
								|
							<?php endif; ?>
							<?php if($item->status != 'Pending'): ?>
								<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'importlisting', 'action' => 'rollback', 'importfile_id' => $item->importfile_id), $this->translate('Rollback'), array('class' => 'smoothbox')) ?>
								|
							<?php endif; ?>
							<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'importlisting', 'action' => 'delete', 'importfile_id' => $item->importfile_id), $this->translate('Delete'), array('class' => 'smoothbox')) ?>
						<?php else: ?>
							<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'importlisting', 'action' => 'stop', 'importfile_id' => $item->importfile_id, 'forceStop' => 1), $this->translate('Stop')) ?>
						<?php endif;?>
        |
						<a href="javascript:void(0);" onclick="javascript:uploadphoto('<?php echo $item->importfile_id;?>');"><?php echo $this->translate('Upload Photos'); ?></a>
					</td>
						
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<br /><?php echo $this->paginationControl($this->paginator); ?>

		<br />
		&nbsp;<button type='submit' name="delete" onclick="return multiDelete()" value="delete_image"><?php echo $this->translate('Delete Selected'); ?></button>	&nbsp;&nbsp;&nbsp;
	</form>

<?php else:?>
	<div class="tip">
		<span>
			<?php echo $this->translate('You have not imported any file yet.'); ?>
		</span>
	</div>	
<?php endif; ?>

<script type="text/javascript" >
  
  function uploadphoto(importfile_id) {
    var zip_enabled = '<?php echo extension_loaded('zip');?>';
    if(zip_enabled != '') {
      window.location.href = en4.core.baseUrl+'admin/sitepage/importlisting/upload-photo/importfile_id/'+importfile_id;
    }
    else {
      alert('You have not enabled the php_zip extension on your server. Please enable it to upload photos.');
    }
  }
</script>
