<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: manage.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php $baseURL = $this->layout()->staticBaseUrl; ?>

<script type="text/javascript">
var currentOrder = '<?php echo $this->order ?>';
var currentOrderDirection = '<?php echo $this->order_direction ?>';
var changeOrder = function(order, default_direction){
  // Just change direction
  if( order == currentOrder ) {
    scriptJquery('#order_direction').val( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
  } else {
    scriptJquery('#order').val(order);
    scriptJquery('#order_direction').val(default_direction);
  }
  scriptJquery('#filter_form').submit();
}
</script>

<script type="text/javascript">

  function multiDelete() {
    return confirm("<?php echo $this->translate('Are you sure you want to delete selected reviews?');?>");
  }

  function selectAll() {
    var i;
    var multidelete_form = $('multidelete_form');
    var inputs = multidelete_form.elements;
    for (i = 1; i < inputs.length; i++) {
      if (!inputs[i].disabled) {
        inputs[i].checked = inputs[0].checked;
      }
    }
  }

</script>

<?php include APPLICATION_PATH .  '/application/modules/Sesblog/views/scripts/dismiss_message.tpl';?>

<div class='clear sesbasic-form'>
  <div>
    <?php if( count($this->subnavigation) ): ?>
      <div class='sesbasic-admin-sub-tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->subnavigation)->render(); ?>
      </div>
    <?php endif; ?>
    <div class="sesbasic-form-cont">
      <div class='settings sesbasic_admin_form'>
				<h3><?php echo $this->translate("Manage Reviews"); ?></h3>
				<p><?php echo $this->translate('This page lists all of the reviews your users have created. You can use this page to monitor these reviews and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific review. Leaving the filter fields blank will show all the reviews on your social network.'); ?></p>
				<br />

				<div class='admin_search sesbasic_search_form'>
				  <?php echo $this->formFilter->render($this) ?>
				</div>
				<br />

				<?php $counter = $this->paginator->getTotalItemCount(); ?> 
				<?php if( count($this->paginator) ): ?>
				  <div class="sesbasic_search_reasult">
				    <?php echo $this->translate(array('%s review found.', '%s reviews found.', $counter), $this->locale()->toNumber($counter)) ?>
				  </div>
				  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
				    <table class='admin_table'>
				      <thead>
				        <tr>
				          <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
				          <th class='admin_table_short'><a href="javascript:void(0);" onclick="javascript:changeOrder('review_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
				          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate("Review Title") ?></a></th>
				          <th><?php echo $this->translate("Content Title") ?></th>
				          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('owner_id', 'ASC');"><?php echo $this->translate("Owner") ?></a></th>
				          <th><?php echo $this->translate("Options") ?></th>
				        </tr>
				      </thead>
				      <tbody>
				        <?php foreach ($this->paginator as $item): ?>
				        <tr>
				          <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->review_id;?>' value="<?php echo $item->review_id; ?>" /></td>
				          <td><?php echo $item->review_id ?></td>
				          <td><?php echo $this->htmlLink($item->getHref(), $this->translate(Engine_Api::_()->sesbasic()->textTruncation($item->getTitle(),16)), array('title' => $item->getTitle(), 'target' => '_blank')) ?></td>
				          
				          <td>
				            <?php $contentItem = Engine_Api::_()->getItem($item->content_type, $item->content_id); ?>
				            <?php echo $this->htmlLink($contentItem->getHref(), $this->translate(Engine_Api::_()->sesbasic()->textTruncation($contentItem->getTitle(),16)), array('title' => $contentItem->getTitle(), 'target' => '_blank')) ?></td>
				          
				          <td><?php echo $this->htmlLink($item->getOwner()->getHref(), $this->translate(Engine_Api::_()->sesbasic()->textTruncation($item->getOwner()->getTitle(),16)), array('title' => $this->translate($item->getOwner()->getTitle()), 'target' => '_blank')) ?></td>
				          <td>
				            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesblog', 'controller' => 'admin-manage-review', 'action' => 'view', 'id' => $item->review_id), $this->translate("View Details"), array('class' => 'smoothbox')) ?>
				            |
				            <?php echo $this->htmlLink($item->getHref(), $this->translate("View"), array('class' => '')); ?>
				            |
				            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesblog', 'controller' => 'admin-manage-review', 'action' => 'delete-review', 'id' => $item->review_id), $this->translate("Delete"), array('class' => 'smoothbox')) ?>
				          </td>
				        </tr>
				        <?php endforeach; ?>
				      </tbody>
				    </table>
				    <br />
				    <div class='buttons'>
				      <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
				    </div>
				  </form>
				  <br/>
				  <div>
				    <?php echo $this->paginationControl($this->paginator); ?>
				  </div>
				<?php else:?>
				  <div class="tip">
				    <span>
				      <?php echo $this->translate("There are no reviews created by your members yet.") ?>
				    </span>
				  </div>
				<?php endif; ?>
      </div>
    </div>
  </div>
</div>











