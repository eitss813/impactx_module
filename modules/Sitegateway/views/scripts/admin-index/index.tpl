<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    index.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2>
  <?php echo 'Advanced Payment Gateways / Stripe Connect Plugin'; ?>
</h2>

<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
<?php endif; ?>

<p>
  Below, you can browse the list of transactions that have been made using various payment gateways. You can search the transactions through: usernames, emails and transaction ids. You can also use the filters below to filter the transactions based on payment gateways, type, payment state and resource type.
</p>

<br />

<?php if( !empty($this->error) ): ?>
  <ul class="form-errors">
    <li>
      <?php echo $this->error ?>
    </li>
  </ul>

  <br />
<?php return; endif; ?>

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<br />

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

<div class='admin_results'>
  <div>
    <?php $count = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s transaction found", "%s transactions found", $count), $count) ?>
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
  <table class='admin_table'>
    <thead>
      <tr>
        <?php $class = ( $this->order == 'transaction_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th style='width: 1%;' class="<?php echo $class ?>">
          <a href="javascript:void(0);" onclick="javascript:changeOrder('transaction_id', 'DESC');">
            <?php echo "ID" ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'user_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th class="<?php echo $class ?>">
          <a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'ASC');">
            <?php echo "Member" ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'gateway_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th style='width: 1%;' class='admin_table_centered <?php echo $class ?>'>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('gateway_id', 'ASC');">
            <?php echo "Gateway" ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'type' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th style='width: 1%;' class='admin_table_centered <?php echo $class ?>'>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('type', 'DESC');">
            <?php echo "Type" ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'state' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th style='width: 1%;' class='admin_table_centered <?php echo $class ?>'>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('state', 'DESC');">
            <?php echo "State" ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'amount' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th style='width: 1%;' class='admin_table_centered <?php echo $class ?>'>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('amount', 'DESC');">
            <?php echo "Amount" ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'timestamp' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th style='width: 1%;' class='admin_table_centered <?php echo $class ?>'>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('timestamp', 'DESC');">
            <?php echo "Date" ?>
          </a>
        </th>
        <th style='width: 1%;' class='admin_table_options'>
          <?php echo "Options" ?>
        </th>
      </tr>
    </thead>
    <tbody>
      <?php foreach( $this->paginator as $item ):
        $user = @$this->users[$item->user_id];
        $order = @$this->orders[$item->order_id];
        $gateway = @$this->gateways[$item->gateway_id];
        
        if($item->resource_type == 'siteeventticket_order') {
            $gatewayName = (Engine_Api::_()->hasModuleBootstrap('siteeventticket') ? Engine_Api::_()->siteeventticket()->getGatwayName($item->gateway_id) : ucfirst($item->type));
        }
        elseif($item->resource_type == 'sitestoreproduct_order') {
            $gatewayName = (Engine_Api::_()->hasModuleBootstrap('sitestoreproduct') ? Engine_Api::_()->sitestoreproduct()->getGatwayName($item->gateway_id) : ucfirst($item->type));
        } 
        else {
            $gatewayName = ( $gateway ? $gateway->title : '<i>' . 'Unknown Gateway' . '</i>' );
        }        
     ?>
        <tr>
          <td><?php echo $item->transaction_id ?></td>
          <td class='admin_table_bold'>
            <?php echo ( $user ? $user->__toString() : '<i>' . 'Deleted or Unknown Member' . '</i>' ) ?>
          </td>
          <td class='admin_table_centered'>
            <?php echo $gatewayName; ?>
          </td>
          <td class='admin_table_centered'>
            <?php echo ucfirst($item->type) ?>
          </td>
          <td class='admin_table_centered'>
            <?php echo ucfirst($item->state) ?>
          </td>
          <td class='admin_table_centered'>
            <?php echo $this->locale()->toCurrency($item->amount, $item->currency) ?>
            <?php echo $this->translate('(%s)', $item->currency) ?>
          </td>
          <td class='admin_table_centered'>
            <?php echo $this->locale()->toDateTime($item->timestamp) ?>
          </td>
          <td class='admin_table_options'>
            <a class="smoothbox" href='<?php echo $this->url(array('action' => 'detail', 'transaction_id' => $item->transaction_id));?>'>
              <?php echo "details" ?>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>