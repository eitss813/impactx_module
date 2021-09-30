<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: package-transactions.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2>
  <?php echo 'Crowdfunding / Fundraising / Donations Plugin'; ?>
</h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>  
 
  <div class='tabs'>
    <ul class="navigation">
    <?php if(Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()): ?> 
        <li class="active">
          <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'packages', 'action' => 'package-transactions'), 'Projects- Package Related Transactions', array());
          ?>
        </li> 
    <?php endif; ?>
      <?php if ($this->paymentToSiteadmin) : ?>
        <li>
          <?php
          echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'transaction', 'action' => 'index'), 'Backers - Backer Related Transactions', array())
          ?>
        </li>   
        <li>
          <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'transaction', 'action' => 'admin-transaction'), 'Backers - Payments to Project Owners') ?>
        </li>
      <?php else: ?>
       <li>
          <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'transaction', 'action' => 'backer-commission-transaction'), 'Backers - Backer Commission Related Transactions') ?>
        </li>
      <?php endif; ?>
    </ul>
  </div>
 
<h3><?php echo "Package Related Transactions" ?></h3>
<p>
  <?php echo "Browse through the transactions made by users for a particular package to start a project. You can also use the filters below to see the details of particular transactions."; ?>
</p>
<br />

<?php if (!empty($this->error)): ?>
  <ul class="form-errors">
    <li>
      <?php echo $this->error ?>
    </li>
  </ul>
  <br />
  <?php return;
endif;
?>
<?php  if (Engine_Api::_()->hasModuleBootstrap('payment')): ?>
  <?php  if (!Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()): ?>
    <div class="tip">
      <span >     
    <?php echo "These transaction are only for packages."; ?>
      </span>
    </div>
  <?php  endif; ?> 
    
  <div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
  </div> 
  <br />
  <script type="text/javascript">
    var currentOrder = '<?php echo $this->filterValues['order'] ?>';
    var currentOrderDirection = '<?php echo $this->filterValues['direction'] ?>';
    var changeOrder = function (order, default_direction) {
      // Just change direction
      if (order == currentOrder) {
        $('direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC');
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
      <?php
      echo $this->paginationControl($this->paginator, null, null, array(
       'query' => $this->filterValues,
       'pageAsQuery' => true,
      ));
      ?>
    </div>
  </div>
  <br />
  <?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <table class='admin_table'>
      <thead>
        <tr>
    <?php $class = ( $this->order == 'transaction_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
          <th style="width: 5%;" class="<?php echo $class ?>">
            <a href="javascript:void(0);" onclick="javascript:changeOrder('transaction_id', 'DESC');">
    <?php echo "Transaction ID"; ?>
            </a>
          </th> 
    <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
          <th class="<?php echo $class ?>" style="width: 15%;">
            <a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');">
    <?php echo "Project Title"; ?>
            </a>
          </th>
    <?php $class = ( $this->order == 'username' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
          <th class="<?php echo $class ?>" style="width: 15%;">
            <a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');">
    <?php echo "User Name"; ?>
            </a>
          </th>
    <?php $class = ( $this->order == 'gateway_title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
          <th style="width: 10%;" class='admin_table_centered <?php echo $class ?>'>
            <a href="javascript:void(0);" onclick="javascript:changeOrder('gateway_title', 'ASC');">
    <?php echo "Gateway"; ?>
            </a>
          </th> 
    <?php $class = ( $this->order == 'state' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
          <th style="width: 10%;" class='admin_table_centered <?php echo $class ?>'>
            <a href="javascript:void(0);" onclick="javascript:changeOrder('state', 'DESC');">
    <?php echo "Payment Status"; ?>
            </a>
          </th>
    <?php $class = ( $this->order == 'amount' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
          <th style="width: 10%;" class='admin_table_centered <?php echo $class ?>'>
            <a href="javascript:void(0);" onclick="javascript:changeOrder('amount', 'DESC');">
    <?php echo "Amount"; ?>
            </a>
          </th>
    <?php $class = ( $this->order == 'timestamp' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
          <th style="width: 15%;" class='admin_table_centered <?php echo $class ?>'>
            <a href="javascript:void(0);" onclick="javascript:changeOrder('timestamp', 'DESC');">
    <?php echo "Date"; ?>
            </a>
          </th>
          <th style="width: 10%;" class='admin_table_options'>
    <?php echo "Options"; ?>
          </th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($this->paginator as $item): ?> 

        <?php $project = $this->item('sitecrowdfunding_project', $item->project_id); ?>
        

          <tr>
            <td><?php echo $item->transaction_id ?></td>  
              <td><?php echo $this->htmlLink($project->getHref(), $this->string()->truncate($this->string()->stripTags($project->getTitle()), 15), array('title' => $project->getTitle(), 'target' => '_blank')) ?> 
            </td> 

             <td>
            <?php echo $this->htmlLink($item->getOwner(), $this->string()->truncate($this->string()->stripTags($item->getOwner()), 15), array('title' => $item->username, 'target' => '_blank')) ?>
            </td>  

          <td class='admin_table_centered'>
      <?php echo $item->gateway_title ?>
            </td> 
            <td class='admin_table_centered'>
      <?php echo ucfirst($item->state) ?>
            </td>

            <td class='admin_table_centered'>
              <?php echo $this->locale()->toCurrency($item->amount,$item->currency) ?> 
            </td>
            <td class='admin_table_centered'>
      <?php echo $this->locale()->toDateTime($item->timestamp) ?>
            </td>
            <td class='admin_table_options'>  
            <a class="smoothbox" href='<?php echo $this->url(array('action' => 'detail', 'transaction_id' => $item->transaction_id));?>'>
            <?php echo $this->translate('details'); ?>
            </a> 
            </td>
          </tr>
    <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
<?php endif; ?>