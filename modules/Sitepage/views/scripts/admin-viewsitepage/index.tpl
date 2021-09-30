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
<h2 class="fleft"><?php echo $this->translate('Directory / Pages Plugin'); ?></h2>
<?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/manageExtensions.tpl'; ?>

<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Directory Items / Pages'); ?></h3>
<?php if((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge')):?>
	<h4><?php echo $this->translate('This page lists all the directory items / pages your members have posted. You can use this page to monitor these items and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific page entries. Leaving the filter fields blank will show all the page entries on your social network. Here, you can also make pages featured / un-featured, sponsored / un-sponsored and approve / dis-approve them. Additional actions like changing category, owner, editing page, %1$sassigning a badge%2$s, etc can also be done.', '<a href="http://www.socialengineaddons.com/socialengine-directory-page-badges-plugin" target="_blank">', '</a>');?></h4>
<?php else: ?>
		<h4><?php echo $this->translate('This page lists all the directory items / pages your members have posted. You can use this page to monitor these items and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific page entries. Leaving the filter fields blank will show all the page entries on your social network. Here, you can also make pages featured / un-featured, sponsored / un-sponsored and approve / dis-approve them. Additional actions like changing category, owner, editing page, etc can also be done.');?></h4>
<?php endif; ?>
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
		return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected pages ?")) ?>');
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

<div class="admin_search sitepage_admin_search">
  <div class="search">
    <form method="post" class="global_form_box" action="<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'viewsitepage', 'action' => 'index'),'admin_default', true) ?>">
      <div>
	      <label>
	      	<?php echo  $this->translate("Title") ?>
	      </label>
	      <?php if( empty($this->title)):?>
	      	<input type="text" name="title" /> 
	      <?php else: ?>
	      	<input type="text" name="title" value="<?php echo $this->translate($this->title)?>"/>
	      <?php endif;?>
      </div>
      <div>
      	<label>
      		<?php echo  $this->translate("Owner") ?>
      	</label>	
      	<?php if( empty($this->owner)):?>
      		<input type="text" name="owner" /> 
      	<?php else: ?> 
      		<input type="text" name="owner" value="<?php echo $this->translate($this->owner)?>" />
      	<?php endif;?>
      </div>
       <?php if(Engine_Api::_()->sitepage()->hasPackageEnable()):?>
      <div>
	    	<label>
	      	<?php echo  $this->translate("Package") ?>
	      </label>
        <select id="package_id" name="package_id">
          <option value="0" ></option>
          <?php foreach ( $this->packageList as $package): ?>
          <option value="<?php echo $package->package_id ?>" <?php if( $this->package_id == $package->package_id) echo "selected";?> > <?php echo ucfirst($package->title) ?></option>
         <?php  endforeach; ?>

         </select>
      </div>
      <?php endif; ?>
      <!-- <div>
	    	<label>
	      	<?php echo  $this->translate("Featured") ?>	
	      </label>
        <select id="" name="featured">
          <option value="0" ></option>
          <option value="2" <?php if( $this->featured == 2) echo "selected";?> ><?php echo $this->translate("Yes") ?></option>
          <option value="1" <?php if( $this->featured == 1) echo "selected";?> ><?php echo $this->translate("No") ?></option>
         </select>
      </div>
      <div>
	    	<label>
	      	<?php echo  $this->translate("Sponsored") ?>	
	      </label>
        <select id="sponsored" name="sponsored">
            <option value="0"  ></option>
          <option value="2" <?php if( $this->sponsored == 2) echo "selected";?> ><?php echo $this->translate("Yes") ?></option>
          <option value="1"  <?php if( $this->sponsored == 1) echo "selected";?>><?php echo $this->translate("No") ?></option>
         </select>
      </div>  -->
      <!-- <div>
	    	<label>
	      	<?php echo  $this->translate("Status") ?>
	      </label>
        <select id="" name="page_status">
          <option value="0" ></option>
          <option value="1" <?php if( $this->page_status == 1) echo "selected";?> ><?php echo $this->translate("Approval Pending") ?></option>
          <option value="2" <?php if( $this->page_status == 2) echo "selected";?> ><?php echo $this->translate("Approved") ?></option>
          <option value="3" <?php if( $this->page_status == 3) echo "selected";?> ><?php echo $this->translate("Dis-Approved") ?></option>
          <option value="4" <?php if( $this->page_status == 4) echo "selected";?> ><?php echo $this->translate("Declined") ?></option>

         </select>
      </div>-->
        <div>
            <label>
                <?php echo  $this->translate("Status") ?>
            </label>
            <select id="" name="page_status">
                <option value="0" ></option>
                <option value="1" <?php if( $this->page_status == 1) echo "selected";?> ><?php echo $this->translate("Approval Pending") ?></option>
                <option value="2" <?php if( $this->page_status == 2) echo "selected";?> ><?php echo $this->translate("Approved") ?></option>
                <option value="3" <?php if( $this->page_status == 3) echo "selected";?> ><?php echo $this->translate("Dis-Approved") ?></option>
                <option value="4" <?php if( $this->page_status == 4) echo "selected";?> ><?php echo $this->translate("Declined") ?></option>

            </select>
        </div>
      <div>
	    	<label>
	      	<?php echo  $this->translate("Open/Closed") ?>
	      </label>
        <select id="" name="status">
          <option value="0" ></option>
          <option value="1" <?php if( $this->status == 1) echo "selected";?> ><?php echo $this->translate("Only Open Pages") ?></option>
          <option value="2" <?php if( $this->status == 2) echo "selected";?> ><?php echo $this->translate("Only Closed Pages") ?></option>
          
         </select>
      </div>
      <?php  $categories = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategories(); ?>
      <?php if(count($categories) > 0) :?>
        <div>
          <label>
            <?php echo  $this->translate("Category") ?>
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
						<?php echo  $this->translate("Subcategory") ?>	
				</label>
				
				<select name="subcategory_id" id="subcategory_id" onchange="changesubcategory(this.value, '')"></select>
			</div>
      <div id="subsubcategory_backgroundimage" class="cat_loader"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/loading.gif" /></div>
		 	<div id="subsubcategory_id-label">
				<label>
						<?php echo  $this->translate('3%s Level Category', "<sup>rd</sup>") ?>
				</label>
				<select name="subsubcategory_id" id="subsubcategory_id"></select>
			</div>
      <?php endif;?>
      <div>
	    	<label>
	      	<?php echo  $this->translate("Browse By") ?>	
	      </label>
        <select id="" name="pagebrowse">
          <option value="0" ></option>
          <option value="1" <?php if( $this->pagebrowse == 1) echo "selected";?> ><?php echo $this->translate("Most Viewed") ?></option>
          <option value="2" <?php if( $this->pagebrowse == 2) echo "selected";?> ><?php echo $this->translate("Most Recent") ?></option>
					<option value="3" <?php if( $this->pagebrowse == 3) echo "selected";?> ><?php echo $this->translate("Most Commented") ?></option>  
					<option value="4" <?php if( $this->pagebrowse == 4) echo "selected";?> ><?php echo $this->translate("Most Liked") ?></option>          
         </select>
      </div>      
      <div class="sitepage_search_button">
        <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
      </div>
    </form>
  </div>
</div>
<br />

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<div class='admin_members_results'>
  <?php $counter=$this->paginator->getTotalItemCount(); if(!empty($counter)): ?>
  <div class="">
    <?php  echo $this->translate(array('%s page found.', '%s pages found.', $counter), $this->locale()->toNumber($counter)) ?>
  </div>
  <?php else:?>
  <div class="tip"><span>
    <?php  echo $this->translate("No results were found.") ?></span>
  </div>
  <?php endif; ?>
  <?php echo $this->paginationControl($this->paginator, null, null, array(
				'pageAsQuery' => true,
				'query' => $this->formValues,
			));
		?>
</div>

<br />

<?php  if( $this->paginator->getTotalItemCount()>0):?>
	<form id='multidelete_form' method="post" action="<?php echo $this->url(array('action'=>'multi-delete'));?>" onSubmit="return multiDelete()">
		<table class='admin_table' width="100%">
			<thead>
				<tr>
					<th width="30" align="center"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
           <?php $class = ( $this->order == 'page_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
					<th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('page_id', 'DESC');" title="<?php echo $this->translate('ID'); ?>" ><?php echo $this->translate('ID'); ?></a></th>
           <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
					<th align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');" title="<?php echo $this->translate('Page Title'); ?>" ><?php echo $this->translate('Title'); ?></a></th>
            <?php $class = ( $this->order == 'username' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
					<th align="left"  class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');" title="<?php echo $this->translate('Owner Name'); ?>"><?php echo $this->translate('Owner');?></a></th>
           <?php $class = ( $this->order == 'view_count' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="admin_table_centered <?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('view_count', 'DESC');" title="<?php echo $this->translate('Views'); ?>"><?php echo $this->translate('Views'); ?></a></th>
            <?php $class = ( $this->order == 'comment_count' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
					<th class="admin_table_centered <?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('comment_count', 'DESC');" title="<?php echo $this->translate('Comments'); ?>"><?php echo $this->translate('Comments'); ?></a></th>
            <?php $class = ( $this->order == 'like_count' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
					<th class="admin_table_centered <?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('like_count', 'DESC');" title="<?php echo $this->translate('Likes'); ?>"><?php echo $this->translate('Likes'); ?></a></th>

          <?php if(Engine_Api::_()->sitepage()->hasPackageEnable()):?>
            <th align="left"  title="<?php echo $this->translate('Package'); ?>" ><?php echo $this->translate('Package')  ?></th>
          <?php endif; ?>
          <th align="left"> <?php echo $this->translate('Status'); ?> </th>
          <th align="left"> <?php echo $this->translate('Sub Page'); ?> </th>
          <?php if(Engine_Api::_()->sitepage()->hasPackageEnable()):?>
            <th align="left" title="<?php echo $this->translate('Payment'); ?>"><?php echo $this->translate('Payment')  ?></th>
					<?php endif; ?>
              <?php $class = ( $this->order == 'approved' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class='admin_table_centered <?php echo $class ?>'><a href="javascript:void(0);" onclick="javascript:changeOrder('approved', 'ASC');" title="<?php echo $this->translate('Approved'); ?>" ><?php echo $this->translate('A'); ?></a></th>
                    <?php $class = ( $this->order == 'approved' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class='admin_table_centered <?php echo $class ?>'><a href="javascript:void(0);" onclick="javascript:changeOrder('approved', 'ASC');" title="<?php echo $this->translate('Rejected'); ?>" ><?php echo $this->translate('R'); ?></a></th>
          <!--    <?php $class = ( $this->order == 'featured' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class='admin_table_centered <?php echo $class ?>'><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'ASC');" title="<?php echo $this->translate('Featured'); ?>" ><?php echo $this->translate('F'); ?></a></th>
				    <?php $class = ( $this->order == 'sponsored' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="admin_table_centered <?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('sponsored', 'DESC');" title="<?php echo $this->translate('Sponsored'); ?>" ><?php echo $this->translate('S'); ?></a></th>-->
              <?php $class = ( $this->order == 'closed' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class='admin_table_centered <?php echo $class ?>'><a href="javascript:void(0);" onclick="javascript:changeOrder('closed', 'ASC');" title="<?php echo $this->translate('Open/Closed'); ?>"><?php echo $this->translate('O/C'); ?></a></th>
					    <?php $class = ( $this->order == 'creation_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="admin_table_centered <?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');" title="<?php echo $this->translate('Creation Date'); ?>"><?php echo $this->translate('Creation Date'); ?></a></th>
          <?php if(Engine_Api::_()->sitepage()->hasPackageEnable()):?>
           <th align="left" title="<?php echo $this->translate('Expiration Date'); ?>"><?php echo $this->translate('Expiration Date')  ?></th>
					<?php endif; ?>
					<th align="left" title="<?php echo $this->translate('Options'); ?>"
           ><?php echo $this->translate('Options'); ?></th>
				</tr>
			</thead>

			<tbody>
				<?php if( count($this->paginator) ): ?>
					<?php foreach( $this->paginator as $item ): ?>
						<tr>
							<td class="admin_table_centered"><input name='delete_<?php echo $item->page_id;?>' type='checkbox' class='checkbox' value="<?php echo $item->page_id ?>"/></td>

              <td ><?php echo $item->page_id ?></td>

							 <td class='admin_table_bold admin-txt-normal'><?php echo $this->htmlLink($this->item('sitepage_page', $item->page_id)->getHref(), $this->translate(Engine_Api::_()->sitepage()->truncation($item->getTitle(),10)), array('title' => $this->translate($item->getTitle()), 'target' => '_blank')) ?></td>
             
							<td class='admin_table_bold' title="<?php echo $item->getOwner()->getTitle() ?>"> <?php echo $this->htmlLink($item->getOwner()->getHref(), Engine_Api::_()->sitepage()->truncation($item->getOwner()->getTitle(),10), array('target' => '_blank')) ?></td>
             
						
							<td align="center" class="admin_table_centered"><?php echo $item->view_count ?></td>
							<td align="center" class="admin_table_centered"><?php echo $item->comment_count  ?></td>
							<td align="center" class="admin_table_centered"><?php echo $item->like_count  ?></td>
					<?php if(Engine_Api::_()->sitepage()->hasPackageEnable()):?>
              <td align="left">		<?php  echo $this->htmlLink(
				array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'package', 'action' => 'packge-detail', 'id' => $item->package_id), $this->translate(ucfirst(Engine_Api::_()->sitepage()->truncation($item->getPackage()->title, 14))), array('class' => 'smoothbox','title'=>ucfirst($item->getPackage()->title)));  ?></td>
							<?php endif; ?>

              <td align="left"><?php echo Engine_Api::_()->sitepage()->getPageStatus($item); ?></td>
              
              <?php if(!empty($item->subpage)) : ?>
								<td align="left"><?php echo "Yes"; ?></td>
              <?php else: ?>
								<td align="left"><?php echo "No"; ?></td>
              <?php endif; ?>

              <?php if(Engine_Api::_()->sitepage()->hasPackageEnable()):?>
                <td align="center" class="admin_table_centered">
                  <?php if(!$item->getPackage()->isFree()):  ?>

                      <?php if($item->status=="initial"):
                          echo $this->translate("No");
                      elseif($item->status=="active"):
                           echo $this->translate("Yes");
                          else:
                             echo $this->translate(ucfirst($item->status));
                            endif;
                              ?>
                  <?php else:?>
                  <?php echo $this->translate("NA (Free)"); ?>
                  <?php endif ?>
                </td>
                <?php endif ?>
              	<?php if($item->declined == 0):?>
                  <?php if($item->approved == 0 && ($item->state == 'submitted' || $item->state == 'rejected' )):?>
                            <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepage', 'controller' => 'admin', 'action' => 'approved', 'id' => $item->page_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_approved1.gif', '', array('title'=> $this->translate('Make Approved')))) ?>
                            </td>
                   <?php else: ?>
                            <td align="center" class="admin_table_centered">-</td>
                  <?php endif;?>
                  <?php if($item->approved == 1 || $item->state == 'submitted'):?>
                            <td align="center" class="admin_table_centered"> <?php
                             echo $this->htmlLink(
                                    array(
                                        'route' => 'default',
                                        'module' => 'sitepage',
                                        'controller' => 'admin',
                                        'action' => 'reject',
                                        'id' => $item->page_id
                                    ),
                                    $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_approved0.gif', '', array('title'=> $this->translate('Make Dis-Approved'))),
                                    array('class' => 'smoothbox')
                                ) ?>
                            </td>
                   <?php else: ?>
                            <td align="center" class="admin_table_centered">-</td>
                   <?php endif; ?>
                  <!--<?php if($item->featured == 1):?>
                    <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepage', 'controller' => 'admin', 'action' => 'featured', 'id' => $item->page_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal1.gif', '', array('title'=> $this->translate('Make Un-featured')))) ?>
                  <?php else: ?>
                    <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepage', 'controller' => 'admin', 'action' => 'featured', 'id' => $item->page_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal0.gif', '', array('title'=> $this->translate('Make Featured')))) ?>
                    </td>
                  <?php endif; ?>
                  <?php if($item->sponsored == 1):?>
                    <td align="center" class="admin_table_centered"> <?php   echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepage', 'controller' => 'admin', 'action' => 'sponsored', 'id' => $item->page_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sponsored.png', '', array('title'=> $this->translate('Make Unsponsored')))); ?>
                  <?php else: ?>
                    <td align="center" class="admin_table_centered"> <?php   echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepage', 'controller' => 'admin', 'action' => 'sponsored', 'id' => $item->page_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/unsponsored.png', '', array('title'=> $this->translate('Make Sponsored')))); ?>
                    </td>
                  <?php endif; ?>  -->
                  
              <?php else: ?>
                    <?php if($item->approved == 1):?>
                      <td align="center" class="admin_table_centered"> <?php echo  $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_approved1.gif', '', array('title'=> $this->translate('Approved'))) ?>
                      </td>
                    <?php else: ?>
                       <?php  $approvedtitle='Dis-Approved';  if(empty($item->aprrove_date)): $approvedtitle="Approval Pending"; endif;?>
                      <td align="center" class="admin_table_centered"> <?php echo  $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_approved0.gif', '', array('title'=> $this->translate($approvedtitle))) ?>
                      </td>
                    <?php endif; ?>
                    <!--<?php if($item->featured == 1):?>
                      <td align="center" class="admin_table_centered"> <?php echo  $this->htmlImage( $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal1.gif', '', array('title'=> $this->translate('Featured'))) ?>
                    <?php else: ?>
                      <td align="center" class="admin_table_centered"> <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal0.gif', '', array('title'=> $this->translate('Un-featured'))) ?>
                      </td>
                    <?php endif; ?>
                    <?php if($item->sponsored == 1):?>
                      <td align="center" class="admin_table_centered"> <?php   echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sponsored.png', '', array('title'=> $this->translate('Sponsored'))); ?>
                    <?php else: ?>
                      <td align="center" class="admin_table_centered"> <?php   echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/unsponsored.png', '', array('title'=> $this->translate('Unsponsored'))) ?>
                      </td>
                    <?php endif; ?>-->

                    
             <?php endif; ?>

               	<?php if($item->closed == 0):?>

								<td align="center" class="admin_table_centered">  <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepage', 'controller' => 'admin', 'action' => 'openclose', 'id' => $item->page_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/unclose.png', '', array('title'=> $this->translate('Make Closed')))) ?>
							<?php else: ?>
								<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepage', 'controller' => 'admin', 'action' => 'openclose', 'id' => $item->page_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/close.png', '', array('title'=> $this->translate('Make Open')))) ?>
							<?php endif; ?>

              <td align="center" class="admin_table_centered"><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'viewsitepage', 'action' => 'edit-creation-date', 'page_id' => $item->page_id), $this->translate(gmdate('M d,Y',strtotime($item->creation_date))), array('class' => 'smoothbox')); ?>
              <?php //echo $this->translate(gmdate('M d,Y',strtotime($item->creation_date))) ?></td>
             

              	<?php if(Engine_Api::_()->sitepage()->hasPackageEnable()):?>
              <td align="left" ><?php echo Engine_Api::_()->sitepage()->getExpiryDate($item)  ?></td>

							<?php endif; ?>

							<td style="width:130px;" class="admin-txt-normal">
             
								<?php echo $this->htmlLink(
										array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'viewsitepage', 'action' => 'detail', 'id' => $item->page_id),
									$this->translate('details'), array('class' => 'smoothbox')) ?>
									|
									<?php echo $this->htmlLink($this->item('sitepage_page', $item->page_id)->getHref(), $this->translate("view"), array('target' => '_blank')) ?>

                  <?php if($item->declined == 0):?>
                  |
                  <?php echo $this->htmlLink(
                      array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'viewsitepage', 'action' => 'edit', 'id' => $item->page_id),
                    $this->translate('edit'), array()) ?>

                  <?php if(Engine_Api::_()->sitepage()->canAdminShowRenewLink($item->page_id)):?>
                    |
                    <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepage', 'controller' => 'admin', 'action' => 'renew', 'id' => $item->page_id), $this->translate('renew'), array(
                    'class' => 'smoothbox',
                    )) ?>
                  <?php endif; ?>
                <?php if(count($categories) > 0) :?>
								|
								<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepage', 'controller' => 'admin', 'action' => 'change-category', 'id' => $item->page_id), $this->translate('change category'), array(
									'class' => 'smoothbox',
								)) ?>
                <?php endif; ?>

                <?php  if (Engine_Api::_()->sitepage()->hasPackageEnable()) : ?>
                |
								<?php echo $this->htmlLink(array('route' => 'sitepage_packages', 'action' => 'update-package', 'page_id' => $item->page_id), $this->translate('edit package'), array(
									'target' => '_blank',
								)) ?>
        		   <?php endif;?>

								|
								<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepage', 'controller' => 'admin', 'action' => 'change-owner', 'id' => $item->page_id), $this->translate('change owner'), array(
									'class' => 'smoothbox',
								)) ?>
                
                  <?php if((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge')):?>
                    |
                    <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepagebadge', 'controller' => 'admin-manage', 'action' => 'assign-badge', 'page_id' => $item->page_id), $this->translate('badge'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  <?php endif; ?>
                <?php endif; ?>
								|
								<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepage', 'controller' => 'admin', 'action' => 'delete', 'id' => $item->page_id), $this->translate('delete'), array(
									'class' => 'smoothbox',
								)) ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		<br />
		<div class='buttons'>
			<button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
		</div>
	</form>
<?php endif;?>

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
  if($('subcategory_id'))
  $('subcategory_id').style.display = 'none';
  if($('subcategory_id-label'))
	$('subcategory_id-label').style.display = 'none';
  if($('subsubcategory_id'))
	$('subsubcategory_id').style.display = 'none';
  if($('subsubcategory_id-label'))
	$('subsubcategory_id-label').style.display = 'none';
</script>
