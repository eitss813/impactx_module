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
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<h3>
  <?php echo $this->translate('Manage Members'); ?>
</h3>

<p>
  <?php echo $this->translate('This page lists all those members of your site who have joined the Pages created on your site. If you need to search for a specific member, then enter your search criteria in the fields below. Here, you can also make members featured / un-featured.');?>
</p>

<br />

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

	function multiDelete()
	{
		return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected page offers ?")) ?>');
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

<div class="admin_search">
  <div class="search">
    <form method="post" class="global_form_box" action="">
      <div>
				<label>
					<?php echo  $this->translate("Display Name") ?>
				</label>	
				<?php if( empty($this->owner)):?>
					<input type="text" name="owner" /> 
				<?php else: ?> 
					<input type="text" name="owner" value="<?php echo $this->translate($this->owner)?>" />
				<?php endif;?>
      </div>
      <div>
	      <label>
	      	<?php echo  $this->translate("Page Title") ?>
	      </label>
	      <?php if( empty($this->title)):?>
	      	<input type="text" name="title" /> 
	      <?php else: ?>
	      	<input type="text" name="title" value="<?php echo $this->translate($this->title)?>"/>
	      <?php endif;?>
      </div>
            <?php  $categories = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategories(); ?>
      <?php if(count($categories) > 0) :?>
        <div>
          <label>
            <?php echo  $this->translate("Page Category") ?>
          </label>
           <select id="" name="category_id" onchange="subcategory(this.value, '', '', '');">
            <option value=""></option>
             <?php if (count($categories) != 0) : ?>
              <?php $categories_prepared[0] = "";
                  foreach ($categories as $category) {
                    $categories_prepared[$category->category_id] = $category->category_name; ?>
                    <option value="<?php echo $category->category_id;?>" <?php if( $this->category_id == $category->category_id) echo "selected";?>><?php echo $this->translate($category->category_name);?></option>
                 <?php } ?>
             <?php endif ; ?>
          </select>
        </div>

			<div id="subcategory_backgroundimage" class="cat_loader"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/loading.gif" /></div>
		 	<div id="subcategory_id-label">
				<label>
						<?php echo  $this->translate("Page Subcategory") ?>	
				</label>
				
				<select name="subcategory_id" id="subcategory_id" onchange="changesubcategory(this.value, '')"></select>
			</div>
      <div id="subsubcategory_backgroundimage" class="cat_loader"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/loading.gif" /></div>
		 	<div id="subsubcategory_id-label">
				<label>
						<?php echo  $this->translate('Page 3%s Level Category', "<sup>rd</sup>") ?>
				</label>
				<select name="subsubcategory_id" id="subsubcategory_id"></select>
			</div>
      <?php endif;?>
      <div>
      <div class="fleft">
				<label>
	      	<?php echo  $this->translate("Featured Members") ?>	
	      </label>
        <select id="" name="featured">
          <option value="0" ></option>
          <option value="2" <?php if( $this->featured == 2) echo "selected";?> ><?php echo $this->translate("Yes") ?></option>
          <option value="1" <?php if( $this->featured == 1) echo "selected";?> ><?php echo $this->translate("No") ?></option>
         </select>
      </div>
      <div class="fleft" style="margin:10px 0 0 10px;">
        <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
      </div>
    </form>
  </div>
</div>

<br />

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

  <?php 
  	if( !empty($this->paginator) ) {
  		$counter = $this->paginator->getTotalItemCount(); 
  	}
  	if(!empty($counter)): 
  
  ?><br />
	<div class='admin_members_results'>
		<div>
			<?php echo $this->translate(array('%s member found.', '%s members found.', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
		</div>
		<?php echo $this->paginationControl($this->paginator); ?>
	</div>
	<br />
	  <form id='multidelete_form' method="post" action="<?php echo $this->url(array('module' => 'sitepagemember', 'controller' => 'manage', 'action' => 'multi-delete-member'), 'admin_default'); ?>" onSubmit="return multiDelete()">
		<table class='admin_table' border="0" width="50%">
			<thead>
				<tr>
					<th style='width: 4%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate('Displayname');?></a></th>
					<th style='width: 4%;' align="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'ASC');"><?php echo $this->translate('Featured Members');?></a></th>
		      <th style='width: 4%;' align="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Joined Pages'); ?></a></th>
				</tr>
			</thead>
			<tbody>
				<?php if(!empty($counter)): ?>
					<?php foreach( $this->paginator as $item ):  ?>
						<tr>
							<td class='admin_table_bold'><?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref()	, $this->item('user', $item->user_id)->displayname, array('title' => $this->item('user', $item->user_id)->displayname, 'target' => '_blank')) ?></td>
							
							<?php if($item->featured_member == 1):   ?>
								<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepagemember', 'controller' => 'admin-manage', 'action' => 'featured', 'user_id' => $item->user_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal1.gif', '', array('title'=> $this->translate('Make Un-featured')))); ?>
							<?php else: ?>
								<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepagemember', 'controller' => 'admin-manage', 'action' => 'featured', 'user_id' => $item->user_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal0.gif', '', array('title'=> $this->translate('Make Featured')))) ?>
								</td>
							<?php endif; ?>
							<td class='admin_table_bold admin_table_centered'>
							<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepagemember', 'controller' => 'admin-manage', 'action' => 'page-join', 'user_id' => $item->user_id), $this->translate($this->locale()->toNumber($item->JOINP_COUNT)), array('onclick' => 'owner(this);return false')); ?>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		<br />
	</form>
<?php else: ?>
<br />
	<div class="tip">
		<span>
			<?php echo $this->translate('No results were found.');?>
		</span>
	</div>
<?php endif; ?>
<script type="text/javascript" >
	function owner(thisobj) {
		var Obj_Url = thisobj.href ;
		Smoothbox.open(Obj_Url);
	}
</script>
<style type="text/css">
	table.admin_table tbody tr td {
		white-space: nowrap;
	}
.pages{margin-top:15px;}	
</style>
<script type="text/javascript">
	var subcategory = function(category_id, sub, subcatname, subsubcate)
	{
    if($('subcategory_backgroundimage'))
		$('subcategory_backgroundimage').style.display = 'block';
    if($('subcategory_id'))
		$('subcategory_id').style.display = 'none';
    if($('subcategory_id-label'))
		$('subcategory_id-label').style.display = 'none';
    if($('subsubcategory_id'))
		$('subsubcategory_id').style.display = 'none';
    if($('subsubcategory_id-label'))
		$('subsubcategory_id-label').style.display = 'none';
    changesubcategory(sub,subsubcate)
	  var url = '<?php echo $this->url(array('action' => 'subcategory'), 'sitepage_general', true);?>';
		en4.core.request.send(new Request.JSON({      	
			 url : url,
			data : {
				format : 'json',
				category_id_temp : category_id
				
			},
			onSuccess : function(responseJSON) {
        if($('subcategory_backgroundimage'))
				$('subcategory_backgroundimage').style.display = 'none';				
				clear('subcategory_id');				
	    	var  subcatss = responseJSON.subcats;
	      addOption($('subcategory_id')," ", '0');
        for (i=0; i< subcatss.length; i++) {
         addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);
           $('subcategory_id').value = sub;
        }				
				if(category_id == 0) {
					clear('subcategory_id');
          if($('subcategory_id'))
          $('subcategory_id').style.display = 'none';
          if($('subcategory_id-label'))
          $('subcategory_id-label').style.display = 'none';
          if($('subsubcategory_id'))
          $('subsubcategory_id').style.display = 'none';
          if($('subsubcategory_id-label'))
          $('subsubcategory_id-label').style.display = 'none';
				}
			}
		}));
	};
  
	function clear(ddName)
	{ 
		for (var i = (document.getElementById(ddName).options.length-1); i >= 0; i--) 
		{ 
				document.getElementById(ddName).options[ i ]=null; 
		} 
	}

	function addOption(selectbox,text,value )
	{
		var optn = document.createElement("OPTION");
		optn.text = text;
		optn.value = value;
		if(optn.text != '' && optn.value != '') {
			$('subcategory_id').style.display = 'block';
			$('subcategory_id-label').style.display = 'block';
			selectbox.options.add(optn);
		}
    else {
      $('subcategory_id').style.display = 'none';
      $('subcategory_id-label').style.display = 'none';
      selectbox.options.add(optn);
		}
	}

	var cat = '<?php echo $this->category_id ?>';
	if(cat != '') {
		var sub = '<?php echo $this->subcategory_id; ?>';
		var subcatname = "<?php echo $this->subcategory_name; ?>";
    var subsubcate = '<?php echo $this->subsubcategory_id; ?>';
		subcategory(cat, sub, subcatname,subsubcate);
	}

  function addSubOption(selectbox,text,value )
    {
      var optn = document.createElement("OPTION");
      optn.text = text;
      optn.value = value;
      if(optn.text != '' && optn.value != '') {
        $('subsubcategory_id').style.display = 'block';
         if($('subsubcategory_id-wrapper'))
          $('subsubcategory_id-wrapper').style.display = 'block';
         if($('subsubcategory_id-label'))
          $('subsubcategory_id-label').style.display = 'block';
        selectbox.options.add(optn);
      } else {
        $('subsubcategory_id').style.display = 'none';
         if($('subsubcategory_id-wrapper'))
          $('subsubcategory_id-wrapper').style.display = 'none';
         if($('subsubcategory_id-label'))
          $('subsubcategory_id-label').style.display = 'none';
        selectbox.options.add(optn);
      }

    }
    function changesubcategory(subcatid,subsubcate) {
      if($('buttons-wrapper')) {
		  	$('buttons-wrapper').style.display = 'none';
			}
      if(subcatid != 0)
      $('subsubcategory_backgroundimage').style.display = 'block';
      var url = '<?php echo $this->url(array('action' => 'subsubcategory'), 'sitepage_general', true);?>';
      var request = new Request.JSON({
        url : url,
        data : {
          format : 'json',
          subcategory_id_temp : subcatid
        },
        onSuccess : function(responseJSON) {
          $('subsubcategory_backgroundimage').style.display = 'none';
  	  		if($('buttons-wrapper')) {
				  	$('buttons-wrapper').style.display = 'block';
					}

          clear('subsubcategory_id');
          var  subsubcatss = responseJSON.subsubcats;

          addSubOption($('subsubcategory_id')," ", '0');
          for (i=0; i< subsubcatss.length; i++) {
            addSubOption($('subsubcategory_id'), subsubcatss[i]['category_name'], subsubcatss[i]['category_id']);
              $('subsubcategory_id').value = subsubcate;
          }
        }
      });
      request.send();
    }
            if($('subcategory_backgroundimage'))
		$('subcategory_backgroundimage').style.display = 'none';
		
		        if($('subsubcategory_backgroundimage'))
		$('subsubcategory_backgroundimage').style.display = 'none';
  if($('subcategory_id'))
  $('subcategory_id').style.display = 'none';
  if($('subcategory_id-label'))
	$('subcategory_id-label').style.display = 'none';
  if($('subsubcategory_id'))
	$('subsubcategory_id').style.display = 'none';
  if($('subsubcategory_id-label'))
	$('subsubcategory_id-label').style.display = 'none';
</script>