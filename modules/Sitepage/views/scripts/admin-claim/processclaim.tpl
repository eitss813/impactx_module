<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: processclaim.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction)
  {  
    if( order == currentOrder ) { 
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } 
    else { 
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
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
</script>

<h2 class="fleft"><?php echo $this->translate('Directory / Pages Plugin'); ?></h2>
<?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/manageExtensions.tpl'; ?>

<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Claims for Directory Items / Pages'); ?></h3>
<p><?php echo $this->translate('Whenever someone makes a claim for a page, that claim comes to you (admin) for review. Below, you can configure the settings for page claims and manage the claims made for pages.'); ?></p><br />

<div class='tabs'>
  <ul class="navigation">
		<li>
			<?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitepage','controller'=>'claim','action'=>'index'), $this->translate('Claimable Page Creators'), array())
			?>
		</li>
		<li class="active">
			<?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitepage','controller'=>'claim','action'=>'processclaim'), $this->translate('Page Claims'), array())
			?>
		</li>
  </ul>
</div>

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<div class='clear'>
	<h3><?php echo $this->translate("Claims received for Directory Items / Pages") ?> </h3>
	<p>
		<?php echo $this->translate('Below you can see all the claims filed by users for Pages on your site. These claims are awaiting your action. You can approve a claim, or decline it, or put it on hold. To view details about a claim and to take an action on it, click on the "take action" link for it.') ?>
	</p><br />					
  <?php 
  	if( !empty($this->paginator) ) {
  		$counter=$this->paginator->getTotalItemCount(); 
  	}
  	if(!empty($counter)): 
  
  ?>
	
		<div>
			<?php echo $this->translate(array('%s claim found.', '%s claims found.', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
		</div>
		<br />
	  <table class='admin_table' width="100%">
	    <thead>
	      <tr>				
	        <th class='admin_table_short pleft5'><input type='checkbox' class='checkbox' /></th>
           <?php $class = ( $this->order == 'page_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
					<th align="center" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('page_id', 'DESC');"><?php echo $this->translate('Page ID'); ?></a></th>
					 <?php $class = ( $this->order == 'engine4_sitepage_pages.title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_sitepage_pages.title', 'DESC');"><?php echo $this->translate('Page Title'); ?></a></th>
					 <?php $class = ( $this->order == 'engine4_users.displayname' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" title="<?php echo $this->translate('Claimer Display Name') ?>" onclick="javascript:changeOrder('engine4_users.displayname', 'DESC');"><?php echo $this->translate('Display Name'); ?></a></th>
					 <?php $class = ( $this->order == 'engine4_sitepage_claims.user_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th align="center" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_sitepage_claims.user_id', 'DESC');"><?php echo $this->translate('Member Id'); ?></a></th>
				   <?php $class = ( $this->order == 'engine4_sitepage_claims.nickname' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" title="<?php echo $this->translate('Claimer Name') ?>" onclick="javascript:changeOrder('engine4_sitepage_claims.nickname', 'DESC');"><?php echo $this->translate('Claimer Name'); ?></a></th>
          <th align="left"><?php echo $this->translate('About'); ?></th>
				   <?php $class = ( $this->order == 'engine4_sitepage_claims.email' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_sitepage_claims.email', 'DESC');"><?php echo $this->translate('Email'); ?></a></th>
					 <?php $class = ( $this->order == 'engine4_sitepage_claims.contactno' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th align="left" class="<?php echo $class ?>"><a title="<?php echo $this->translate('Contact Number') ?>" href="javascript:void(0);" onclick="javascript:changeOrder('engine4_sitepage_claims.contactno', 'DESC');"><?php echo $this->translate('Contact No.'); ?></a></th>
				   <?php $class = ( $this->order == 'creation_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="admin_table_centered <?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');" title="<?php echo $this->translate('Claimed Date'); ?>"><?php echo $this->translate('Creation Date'); ?></a></th>
				   <?php $class = ( $this->order == 'engine4_sitepage_claims.status' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th align="center" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_sitepage_claims.status', 'DESC');"><?php echo $this->translate('Status'); ?></a></th>
					<th align="left"><?php echo $this->translate('Options'); ?></th>
				</tr>	
			</thead>								
			<tbody>
				<?php foreach ($this->paginator as $item): ?>
					<tr> 
					  <td class="pleft5"><input name='delete_<?php echo $item->claim_id;?>' type='checkbox' class='checkbox' value="<?php echo $item->claim_id ?>"/></td>	
						<td class='admin_table_centered admin-txt-normal'><?php echo $item->page_id;?>	</td>
			      <td class="admin-txt-normal" title="<?php echo $this->translate($item->title) ?>">
			      <a href="<?php echo $this->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($item->page_id)), 'sitepage_entry_view') ?>"  target='_blank'>
			      <?php echo $this->translate(Engine_Api::_()->sitepage()->truncation($item->title,10)) ?></a>
			      </td>
						<td title="<?php echo $this->item('user', $item->user_id)->getTitle()?>">
			        <?php
			          $display_name = $this->item('user', $item->user_id)->getTitle();
			          $display_name = Engine_Api::_()->sitepage()->truncation($display_name,16);
			          echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $display_name, array('target' => '_blank'))
			        ?>
			      </td>			
						<td class="admin_table_centered admin-txt-normal"><?php echo $item->user_id;?>	</td>
						<td title="<?php echo $item->nickname;?>"><?php echo $this->translate(Engine_Api::_()->sitepage()->truncation($item->nickname,15)) ?></td>
						<td class="admin-txt-normal" title="<?php echo $item->about;?>"><?php echo $this->translate(Engine_Api::_()->sitepage()->truncation($item->about,20)) ?></td>
						<td class="admin-txt-normal" title="<?php echo $item->email;?>"><?php echo Engine_Api::_()->sitepage()->truncation($item->email, 16);?>	</td>
						<?php if(!empty($item->contactno)):?>
						  <td class="admin-txt-normal"><?php echo $item->contactno;?>	</td>	
						<?php else : ?>
							<td class="admin_table_centered" ><?php echo "-" ?>	</td>	
						<?php endif;?>
						<td align="center" class="admin_table_centered"><?php echo $this->translate(gmdate('M d,Y',strtotime($item->creation_date))) ?></td>							
						<?php if($item->status == 1 ):?>
							<?php $status = 'Approved';?>
						<?php elseif($item->status == 2 ):?>
							<?php $status = 'Declined';?>
						<?php elseif($item->status == 3 ):?>
							<?php $status = 'Pending';?>
						<?php elseif($item->status == 4 ):?>
							<?php $status = 'Hold';?>
						<?php endif;?>
						<td class="admin_table_centered"><?php echo $status;?>	</td>									
						<td>	
						<?php if($item->status == 1 || $item->status == 2): ?>
						<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepage', 'controller' => 'admin-claim', 'action' => 'take-action', 'claim_id'=> $item->claim_id,'page_id' => $item->page_id), $this->translate('details'), array(
			      'class' => 'smoothbox',
			    )) ?> |
			      <?php else :?>
			      <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepage', 'controller' => 'admin-claim', 'action' => 'take-action', 'claim_id'=> $item->claim_id,'page_id' => $item->page_id), $this->translate('take action'), array(
			      'class' => 'smoothbox',
			    )) ?> |
			       <?php endif;?>
					  <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepage', 'controller' => 'admin-claim', 'action' => 'request-delete', 'claim_id' => $item->claim_id), $this->translate('delete'), array(
					                'class' => 'smoothbox',
					              )) ?> 	              
						</td>
					</tr>
			  <?php  endforeach; ?>
		  </tbody>			
		</table>
	<?php else:?>         
		<div class="tip">
			<span><?php  echo $this->translate("No pages have been claimed yet."); ?></span> 
		</div>  		
	<?php endif;?>
	<?php  if( !empty($counter)):?>
		<?php if($this->paginator->getTotalItemCount()): ?>
			<?php echo $this->paginationControl($this->paginator); ?>
			<br />
				<div class='buttons clear'>
				<button onclick="javascript:delectSelected();" type='submit'>
					<?php echo $this->translate("Delete Selected") ?>
				</button>
			</div>
			<form id='delete_selected' method='post' action='<?php echo $this->url(array('action' =>'multi-delete-request')) ?>'>
				<input type="hidden" id="ids" name="ids" value=""/>
			</form>
		<?php endif;?>
	<?php endif;?>
</div>
