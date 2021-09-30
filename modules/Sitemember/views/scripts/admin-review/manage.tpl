<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<h2><?php echo $this->translate("Advanced Members Plugin - Better Browse & Search, User Reviews, Ratings & Location Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<?php if( count($this->subNavigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->subNavigation)->render()
    ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Reviews'); ?></h3>
<p>
	<?php echo $this->translate('This page lists all the reviews posted by members of your site. Here, you can monitor reviews, delete them if necessary. Entering criteria into the filter fields will help you find specific review entries. Leaving the filter fields blank will show all the review entries on your social network. Here, you can also make reviews featured / un-featured by clicking on the corresponding icons.'); ?>
</p>

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
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected Member reviews ?")) ?>');
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

<br />

<div class="admin_search">
  <div class="search">
    <form method="post" class="global_form_box" action="">
      <div>
        <label>
          <?php echo $this->translate("Review Title") ?>
        </label>
        <input type="text" name="review_title" value="<?php echo $this->review_title; ?>"/>
      </div>
        
      <div>
        <label>
          <?php echo $this->translate("Member Title") ?>
        </label>
        <input type="text" name="user_title" value="<?php echo $this->user_title; ?>"/>
      </div>
      <div class="buttons">
        <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
      </div>
    </form>
  </div>
</div>
<br />
<div class='admin_search'>
    <?php echo $this->formFilter->render($this) ?>
</div>
<br />
<?php $reviewHelpful = Engine_Api::_()->getDbtable('helpful', 'sitemember'); ?>
<?php if (count($this->paginator)): ?>
  <div class='admin_members_results'>
    <div>
  <?php echo $this->translate(array('%s review found.', '%s reviews found.', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
    </div>
  </div>
  <br />
  <div class="admin_table_form">
	  <form id='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'multi-delete')); ?>" onSubmit="return multiDelete()">
	    <table class='admin_table seaocore_admin_table'>
	      <thead>
	        <tr>
	          <th style='width: 1%;' align="left"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
	          <th style='width: 1%;' align="center" title="<?php echo $this->translate('ID'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('review_id', 'DESC');"><?php echo $this->translate('ID'); ?></a></th>
	          <th align="left" title="<?php echo $this->translate('Review Title'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Review Title'); ?></a></th>
	          <th align="left" title="<?php echo $this->translate('User Title'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('user_title', 'ASC');"><?php echo $this->translate('User Title'); ?></a></th>
	          <th align="left" title="<?php echo $this->translate('Overall Rating'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('review_rating', 'ASC');"><?php echo $this->translate('Overall Rating'); ?></a></th>
						<th style='width: 1%;' class='admin_table_centered' title="<?php echo $this->translate('Featured'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'ASC');"><?php echo $this->translate('F'); ?></a></th>
	          <th align="center" title="<?php echo $this->translate('Helpful (%)'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('helpful_count', 'ASC');"><?php echo $this->translate('Helpful (%)'); ?></a></th>
	          <th align="left" title="<?php echo $this->translate('Date'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate('Date'); ?></a></th>
	          <th class='admin_table_options' align="left" title="<?php echo $this->translate('Options'); ?>"><?php echo $this->translate('Options'); ?></th>
	        </tr>
	      </thead>
	      <tbody>
	          <?php if (count($this->paginator)): ?>
	            <?php foreach ($this->paginator as $item): ?>
	              
	            <tr>
	              <td><input name='delete_<?php echo $item->review_id; ?>' type='checkbox' class='checkbox' value="<?php echo $item->review_id ?>"/></td>
	              <td class="admin_table_centered"><?php echo $item->review_id ?></td>
	               <td class='admin_table_bold'><?php echo $this->htmlLink($item->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->title, 13), array('title' => $item->title, 'target' => '_blank')) ?></td>
	
	              <td class='admin_table_bold'><?php echo $this->htmlLink($this->item('user', $item->resource_id)->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->user_title, 10), array('title' => $item->user_title, 'target' => '_blank')) ?></td>

                 <td>
                    <div>
                      <span title="<?php echo $item->review_rating . $this->translate(' rating '); ?>">
                        <?php echo $this->showRatingStarMember($item->review_rating, 'user', 'big-star'); ?>
                      </span>
                    </div>
                  </td>
                  
                <?php if($item->featured == 1):?>
                  <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemember', 'controller' => 'review', 'action' => 'featured', 'review_id' => $item->review_id, 'resource_id' => $item->resource_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/featured.png', '', array('title'=> $this->translate('Make Un-featured')))) ?> 
                  </td>       
                <?php else: ?>  
                  <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemember', 'controller' => 'review', 'action' => 'featured', 'review_id' => $item->review_id, 'resource_id' => $item->resource_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/unfeatured.png', '', array('title'=> $this->translate('Make Featured')))) ?>
                  </td>
                <?php endif; ?>
          
                <?php if ($item->helpful_count > -1): ?>
                  <?php $totalHelpsData = $reviewHelpful->countHelpfulPercentage($item->review_id, 0); ?>
	                <td align="center" class="admin_table_centered"><span title="<?php echo $this->translate("%1s out of %2s marked as helpful.", $totalHelpsData['total_yes'], $totalHelpsData['total_marks']) ?>"><?php echo $item->helpful_count ?></span></td>
	              <?php else: ?>
	                <td align="center" class="admin_table_centered"><span title="<?php echo $this->translate('No member marked this member as helpful !'); ?>">---</span></td>
	              <?php endif; ?>
	
                <td title="<?php echo $this->translate(gmdate('M d,Y g:i A', strtotime($item->creation_date))) ?>"><?php echo $this->translate(gmdate('M d,Y', strtotime($item->creation_date))) ?></td>
                
	              <td class='admin_table_options' align="left">
	                <?php echo $this->htmlLink($item->getHref(), $this->translate('View'), array('target' => '_blank')) ?>	 |
	                
	               <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemember', 'controller' => 'review', 'action' => 'delete', 'review_id' => $item->review_id), $this->translate('Delete'), array('class' => 'smoothbox')) ?> 
	              </td>
	            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
	      </tbody>
	    </table> 
      <br />
      <?php echo $this->paginationControl($this->paginator); ?><br /><br />
      <div class='buttons'>
        <button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
      </div>
    </form>
  </div>
<?php else: ?>
  <div class="tip">
    <span>
  <?php echo $this->translate('No results were found.'); ?>
    </span>
  </div>
<?php endif; ?>