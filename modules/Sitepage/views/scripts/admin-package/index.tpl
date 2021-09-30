<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
	var currentOrder = '<?php echo $this->filterValues['order'] ?>';
	var currentOrderDirection = '<?php echo $this->filterValues['direction'] ?>';
	var changeOrder = function(order, default_direction){
		// Just change direction
		if( order == currentOrder ) {
			$('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
		} else {
			$('order').value = order;
			$('direction').value = default_direction;
		}
		$('filter_form').submit();
	}
</script>
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
<?php if (count($this->subnavigation) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.isActivate', 0)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->subnavigation)->render() ?>
    </div>
<?php endif; ?>
<h3>
  <?php echo $this->translate("Manage Packages") ?>
</h3>

<p>
  <?php echo $this->translate('This page allows you to create highly configurable and effective page packages, enabling you to get the best results. You can create both Free and Paid page packages. Below, you can manage existing page packages on your site, or create new ones. You can also set the sequence of page packages in the order in which they should appear to members on the package listing page in the first step of page creation. To do so, drag-and-drop the packages vertically and click on "Save Order" to save the sequence. (Note: You will be able to create a new package over here only if you have enabled Packages from Global Settings.)') ?>
</p>

<br />

<?php if( !empty($this->error) ): ?>
  <ul class="form-errors">
    <li>
      <?php echo $this->error ?>
    </li>
  </ul>

  <br />
<?php  endif; ?>
<?php  if (Engine_Api::_()->sitepage()->hasPackageEnable() && !empty( $this->canCreate)) : ?>
<div>
  <?php echo $this->htmlLink(array('action' => 'create', 'reset' => false), $this->translate('Create New Package'), array(
    'class' => 'buttonlink icon_sitepage_admin_add',
  )) ?>
</div>
  <?php elseif(!Engine_Api::_()->sitepage()->hasPackageEnable()):?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Packages are not enabled in Global Settings.');?>
    </span>
  </div>
<?php endif; ?>
<br />

  <div class='admin_search'>
    <?php echo $this->formFilter->render($this) ?>
  </div>
  
  <br />

<div class='admin_results'>
  <div>
    <?php $count = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s package found.", "%s packages found.", $count), $count) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->filterValues,
      'pageAsQuery' => true,
    )); ?>
  </div>
</div>

<br />

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

  <table class='admin_table' width="100%">
    <thead>
      <tr>
        <th style="padding:7px 0;">
        <?php $class = ( $this->order == 'package_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <div style='width: 2%;' class="<?php echo $class ?> admin_table_centered">
          <a href="javascript:void(0);" onclick="javascript:changeOrder('package_id', 'DESC');">
            <?php echo $this->translate("ID") ?>
          </a>
        </div>
        <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <div  style='width: 25%;' class="<?php echo $class ?>">
          <a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');">
            <?php echo $this->translate("Title") ?>
          </a>
        </div>
       
        <?php $class = ( $this->order == 'price' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <div  style='width: 11%;'  class="<?php echo $class ?> ">
          <a href="javascript:void(0);" onclick="javascript:changeOrder('price', 'DESC');">
            <?php echo $this->translate("Price") ?>
          </a>
        </div>
        <div style='width: 11%;' class="">
          <?php echo $this->translate("Duration") ?>
        </div>
        <div style='width: 11%;' class="">
          <?php echo $this->translate("Billing Cycle") ?>
        </div>
        <?php $class = ( $this->order == 'enabled' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <div style='width: 7%;' class='admin_table_centered <?php echo $class ?>'>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('enabled', 'DESC');">
            <?php echo $this->translate("Enabled") ?>
          </a>
        </div>
       
        <div style='width: 11%;' class='admin_table_centered'>
          <?php echo $this->translate("Total Pages") ?>
        </div>
        <div style='width: 12%;' class='admin_table_options admin_table_centered'>
          <?php echo $this->translate("Options") ?>
        </div>
        </th>
      </tr>
    </thead>
  </table>
  <form id='saveorder_form' method='post' action='<?php echo $this->url(array('action' =>'update')) ?>'>
     	<input type='hidden'  name='order' id="order" value=''/>
     <div class="seaocore_admin_order_list" id='order-element'>
      <ul>
      <?php foreach ($this->paginator as $item) :
      ?>
        <li class="package-list">
          <input type='hidden'  name='order[]' value='<?php echo $item->package_id; ?>'>
          <table class='admin_table' width='100%'>
    <tbody>
        <tr>
          <td style="padding:7px 0;">
          <div style="width:2%;" class="admin_table_centered"><?php echo $item->package_id ?></div>
          <div style="width:25%;" class='admin_table_bold'>
            <?php echo $item->title ?>
          </div>
         
          <div style="width:11%;" class="">
            <?php echo ($item->isFree())?  $this->translate('FREE') : $this->locale()->toCurrency($item->price,Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')) ?>
          </div>
          <div style="width:11%;" class="">
            <?php echo $item->getPackageQuantity() ?>
          </div>
          <div style="width:11%;" class="">
            <?php echo $item->getBillingCycle() ?>
          </div>
          <div style="width:7%;" class='admin_table_centered'>
            <?php echo ( $item->enabled ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'package', 'action' => 'enabled', 'id' => $item->package_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/enabled1.gif', '', array('title' => $this->translate('Disable Package'))), array('class' => 'smoothbox'))  : $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'package', 'action' => 'enabled', 'id' => $item->package_id), $this->htmlImage($this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/enabled0.gif', '', array('title' => $this->translate('Enable Package')))) ) ?>
          </div>
         
          <div style="width:11%;" class='admin_table_centered'>
            <?php echo $this->locale()->toNumber(@$this->memberCounts[$item->package_id]) ?>
          </div>

          <div style="width:12%;" class='admin_table_options admin_table_centered'>
            <?php  if (Engine_Api::_()->sitepage()->hasPackageEnable() && !empty( $this->canCreate)) : ?>
	            <a href='<?php echo $this->url(array('action' => 'edit', 'package_id' => $item->package_id)) ?>'>
	              <?php echo $this->translate("Edit") ?>
	            </a>
            	|
        		<?php endif;?>
            <a href="javascript:void(0);" onclick="viewPage(<?php echo $item->package_id ?>)" ><?php echo  $this->translate('View Pages') ?></a>
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

   <button onClick="javascript:saveOrder(true);" type='submit'>
    <?php echo $this->translate("Save Order") ?>
  </button>
    
<form id='view_selected' method='post' action='<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'viewsitepage', 'action' => 'index'),'admin_default') ?>'>
    <input type="hidden" id="package_id" name="package_id" value=""/>
    <input type="hidden" id="search" name="search" value="1"/>
  </form>
<?php endif; ?>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
	<script type="text/javascript">
		var viewPage =function(id){

			$('package_id').value = id;

			$('view_selected').submit();
		}

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
<?php endif;?>
