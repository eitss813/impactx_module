<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: day.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">

  function multiDelete()
  {
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected page ?")) ?>');
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

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<h2 class="fleft"><?php echo $this->translate('Directory / Pages Plugin'); ?></h2>
<?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/manageExtensions.tpl'; ?>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate("Widget Settings") ?></h3>

<?php echo $this->translate("Configure the settings for various widgets available with this plugin.") ?><br /><br />
<div class='tabs'>
  <ul class="navigation">
    <li>
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'widgets', 'action' => 'index'), $this->translate('General Settings'), array()) ?>
    </li>
    <li class="active">
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'items', 'action' => 'day'), $this->translate('Page of the Day'), array()) ?>
    </li>
  </ul>
</div>
<div class='clear'>
  <div class='settings'>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'items', 'action' => 'multi-delete'), 'admin_default'); ?>" onSubmit="return multiDelete()" class="global_form">
      <div>
        <h3><?php echo $this->translate("Page of the Day widget") ?> </h3>
        <p class="description">
          <?php echo $this->translate("Add and Manage the directory items / pages on your site to be shown in the Page of the Day widget. You can also mark these items for future dates such that the marked directory item / page automatically shows up as Page of the Day on the desired date. Note that for this page of the day to be shown, you must first place the Page of the Day widget at the desired location.") ?>
        </p>
        <?php
        echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'items', 'action' => 'add-item'), $this->translate('Add a Page of the Day'), array(
            'class' => 'smoothbox buttonlink',
            'style' => 'background-image: url('.$this->layout()->staticBaseUrl.'application/modules/Core/externals/images/admin/new_category.png);'))
        ?>	<br/>	<br/>
        <?php if ($this->paginator->getTotalItemCount() > 0): ?>
          <table class='admin_table' width="80%">
            <thead>
              <tr>
								<?php $class = ( $this->order == 'engine4_sitepage_pages.title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                <th style='width: 1%;' align="left"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
                <th width="550" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_sitepage_pages.title', 'DESC');"><?php echo $this->translate("Page Title") ?></a></th>

                <?php $class = ( $this->order == 'start_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                <th width="70" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('start_date', 'DESC');"><?php echo $this->translate("Start Date") ?></a></th>
                <?php //Start End date work  ?>
                <?php $class = ( $this->order == 'end_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                <th width="70" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('end_date', 'DESC');"><?php echo $this->translate("End Date") ?></a></th>
                <?php //End End date work  ?>
                <th width="70" align="left"><?php echo $this->translate("Option") ?></th>
              </tr>
            </thead>
            <tbody>
							<?php foreach ($this->paginator as $item): ?>
                <tr>
                  <td><input name='delete_<?php echo $item->itemoftheday_id; ?>' type='checkbox' class='checkbox' value="<?php echo $item->itemoftheday_id ?>"/></td>
                  <td class='admin_table_bold admin-txt-normal' title="<?php echo $this->translate($item->getTitle()) ?>">
                    <a href="<?php echo $this->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($item->resource_id)), 'sitepage_entry_view') ?>"  target='_blank'>
                    <?php echo $this->translate(Engine_Api::_()->sitepage()->truncation($item->getTitle(), 100)) ?></a>
                  </td>
                  <td align="left"><?php echo $item->start_date ?></td>
                  <?php //Start End date work ?>
                  <td align="left"><?php echo $item->end_date ?></td>
                  <?php //End End date work  ?>
                  <td align="left">
										<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'items', 'action' => 'delete-item', 'id' => $item->itemoftheday_id), $this->translate('delete'), array('class' => 'smoothbox',)) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table><br />
          <div class='buttons'>
            <button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
          </div>
        <?php else: ?>
          <div class="tip"><span><?php echo $this->translate("No pages have been marked as Page of the Day."); ?></span> </div><?php endif; ?>
      </div>
    </form>
  </div>
</div>
<?php echo $this->paginationControl($this->paginator); ?>
