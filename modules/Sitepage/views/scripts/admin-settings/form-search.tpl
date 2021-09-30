<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: form.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2 class="fleft"><?php echo $this->translate('Directory / Pages Plugin'); ?></h2>
<?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/manageExtensions.tpl'; ?>
<?php if (count($this->navigation)) {?>
  <div class='seaocore_admin_tabs clr'>
		<?php
		// Render the menu
		//->setUlClass()
		echo $this->navigation()->menu()->setContainer($this->navigation)->render()
		?>
	</div>
<?php } ?>

<h3>
  <?php echo $this->translate("Search Form Settings") ?>
</h3>

<p>
  <?php echo $this->translate('This page lists all the fields which will be displayed to the users in the search form widget at "Pages Home", "Browse Pages" and "My Pages". Below, you can set the sequence of items in the order in which they should appear to users in the search form. To do so, drag-and-drop the items vertically and click on "Save Order" to save the sequence. You can also hide/display the individual fields in this search form.') ?>
</p>

<br />
 
<table class='admin_table' width="50%">
	<thead>
		<tr>
			<th>      
				<div style="width:70%;" class='admin_table_bold fleft'>
					<?php echo $this->translate("Item Label") ?>
			</div>
				<div style="width:20%;" class='admin_table_centered admin_table_bold fleft'>
				<?php echo $this->translate("Hide / Display") ?>
			</div>
			</th>
		</tr>
	</thead>
</table>
 
<form id='saveorder_form' method='post' action='<?php echo $this->url(array('action' =>'form-search')) ?>' class="clr" style="overflow:hidden;">
	<input type='hidden'  name='order' id="order" value=''/>
	<div class="seaocore_admin_order_list" id='order-element'>
		<ul style="float:left;width:50%;">
			<?php foreach ( $this->searchForm  as $item) :?>
				<?php if((!$this->enableBadgePlugin && $item->name=='badge_id') || (!$this->enableReviewPlugin && $item->name=='has_review')) : ?>
				<?php continue; ?>
				<?php endif; ?>
					<?php if(!$this->enableGeoLocationPlugin &&$item->name=='has_currentlocation') : ?>
				<?php continue; ?>
				<?php endif; ?>
				<li class="package-list">
					<input type='hidden'  name='order[]' value='<?php echo $item->searchformsetting_id; ?>'>
					<table class='admin_table' width='100%'>
						<tbody>
							<tr>
								<td>
									<div style="width:70%;" class='admin_table_bold fleft'>
										<?php echo $this->translate($item->label) ?>
									</div>
									<div style="width:20%;" class='admin_table_centered fleft'>
										<?php if($item->display == 1):?>
											<?php   echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepage', 'controller' => 'admin', 'action' => 'diplay-form', 'id' => $item->searchformsetting_id,'display'=>0), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_approved1.gif', '', array('title'=> $this->translate('Hide')))); ?>
										<?php else: ?>
											<?php   echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepage', 'controller' => 'admin', 'action' => 'diplay-form', 'id' => $item->searchformsetting_id,'display'=>1), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_approved0.gif', '', array('title'=> $this->translate('Display')))); ?>
											
										<?php endif; ?>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</li>
			<?php endforeach; ?>
    </ul>
  </div>
</form>
<br />
<button onClick="javascript:saveOrder(true);" type='submit' class="clear">
	<?php echo $this->translate("Save Order") ?>
</button>

<script type="text/javascript">

  var saveFlag=false;
  var origOrder;
	var changeOptionsFlag = false;

		function saveOrder(value){
			saveFlag=value;
    	var finalOrder = [];
			var li = $('order-element').getElementsByTagName('li');
			for (i = 1; i <= li.length; i++)
        finalOrder.push(li[i]);
      $("order").value=finalOrder;

      	$('saveorder_form').submit();
		}
  window.addEvent('domready', function(){
				//         We autogenerate a list on the fly
				var initList = [];
				var li = $('order-element').getElementsByTagName('li');
				for (i = 1; i <= li.length; i++)
						initList.push(li[i]);
				origOrder = initList;
				var temp_array = $('order-element').getElementsByTagName('ul');
				temp_array.innerHTML = initList;
				new Sortables(temp_array);
		});

		window.onbeforeunload = function(event){
			var finalOrder = [];
			var li = $('order-element').getElementsByTagName('li');
			for (i = 1; i <= li.length; i++)
				finalOrder.push(li[i]);



			for (i = 0; i <= li.length; i++){
				if(finalOrder[i]!=origOrder[i])
				{
					changeOptionsFlag = true;
					break;
				}
			}

			if(changeOptionsFlag == true && !saveFlag){
				var answer=confirm("<?php echo $this->string()->escapeJavascript($this->translate("A change in the order of the packages has been detected. If you click Cancel, all unsaved changes will be lost. Click OK to save change and proceed.")); ?>");
				if(answer) {
          $('order').value=finalOrder;
					$('saveorder_form').submit();

				}
			}
		}
</script>
