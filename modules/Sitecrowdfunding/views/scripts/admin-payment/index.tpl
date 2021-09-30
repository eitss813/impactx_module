<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2017-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">

  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function (order, default_direction) {
    if (order == currentOrder) {
      $('order_direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC');
    }
    else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }



</script>

<h2>
    <?php echo 'Crowdfunding / Fundraising / Donations Plugin'; ?>
</h2>


<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<h3><?php echo "Payment Requests from Project Owners"; ?></h3>
<p class="description"><?php echo 'Below, you can manage payment requests made by the project owners on your site for the amount backed for their projects. You can approve payment requested by the project owners by using the "approve" link and while approving, you can add response message and choose the amount to approve. You can also use the filters below to filter the requests.'; ?></p>

<div class="admin_search sitecrowdfunding_admin_search">
  <div class="search">
    <form method="post" class="global_form" action="">
      <input type="hidden" name="post_search" /> 
      <div>
        <label>
          <?php echo "Title" ?>
        </label>
        <?php if (empty($this->title)): ?>
          <input type="text" name="title" /> 
        <?php else: ?>
          <input type="text" name="title" value="<?php echo $this->title ?>"/>
        <?php endif; ?>
      </div>


      <div>
        <label>
          <?php echo "Request Date: ex (2000-12-25)" ?>
        </label>
        <?php if (empty($this->request_date)): ?>
          <input type="text" name="request_date" /> 
        <?php else: ?>
          <input type="text" name="request_date" value="<?php echo $this->request_date ?>"/>
        <?php endif; ?>
      </div>

      <div>
        <label>
          <?php echo "Response Date: ex (2000-12-25)" ?>
        </label>
        <?php if (empty($this->response_date)): ?>
          <input type="text" name="response_date" /> 
        <?php else: ?>
          <input type="text" name="response_date" value="<?php echo $this->response_date ?>"/>
        <?php endif; ?>
      </div>

      <div>
        <label>
          <?php echo "Requested Amount" ?>
        </label>
        <div>
          <?php if ($this->request_min_amount == ''): ?>
            <input type="text" name="request_min_amount" placeholder="min" class="input_field_small" /> 
          <?php else: ?>
            <input type="text" name="request_min_amount" placeholder="min" value="<?php echo $this->request_min_amount ?>" class="input_field_small" />
          <?php endif; ?>

          <?php if ($this->request_max_amount == ''): ?>
            <input type="text" name="request_max_amount" placeholder="max" class="input_field_small" /> 
          <?php else: ?>
            <input type="text" name="request_max_amount" placeholder="max" value="<?php echo $this->request_max_amount ?>" class="input_field_small" />
          <?php endif; ?>
        </div>

      </div>

      <div>
        <label>
          <?php echo "Response Amount" ?>
        </label>
        <div>
          <?php if ($this->response_min_amount == ''): ?>
            <input type="text" name="response_min_amount" placeholder="min" class="input_field_small" /> 
          <?php else: ?>
            <input type="text" name="response_min_amount" placeholder="min" value="<?php echo $this->response_min_amount ?>" class="input_field_small" />
          <?php endif; ?>

          <?php if ($this->response_max_amount == ''): ?>
            <input type="text" name="response_max_amount" placeholder="max" class="input_field_small" /> 
          <?php else: ?>
            <input type="text" name="response_max_amount" placeholder="max" value="<?php echo $this->response_max_amount ?>" class="input_field_small" />
          <?php endif; ?>
        </div>

      </div>

      <div>
        <label>
          <?php echo "Request Status" ?>	
        </label>
        <select id="" name="request_status">
          <option value="0" ><?php echo "Select" ?></option>
          <option value="1" <?php if ($this->status == 1) echo "selected"; ?> ><?php echo "Requested" ?></option>
          <option value="3" <?php if ($this->status == 3) echo "selected"; ?> ><?php echo "Completed" ?></option>
          <option value="2" <?php if ($this->status == 2) echo "selected"; ?> ><?php echo "Canceled" ?></option>
        </select>
      </div>

      <div class="clear mtop10">
        <button type="submit" name="search" ><?php echo "Search" ?></button>
      </div>

    </form>
  </div>
</div>
<br />

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>
<br />

<div class='admin_members_results'>
  <?php
  if (!empty($this->paginator)) {
    $counter = $this->paginator->getTotalItemCount();
  }
  if (!empty($counter)):
    ?>
    <div class="">
      <?php echo $this->translate(array('%s payment request found.', '%s payment requests found.', $counter), $this->locale()->toNumber($counter)) ?>
    </div>
  <?php else: ?>
    <div class="tip"><span>
        <?php echo "No results were found." ?></span>
    </div>
  <?php endif; ?> 
</div>
<br />


<?php if (!empty($counter)): ?>
  <div class="manageproject_table_scroll">
    <table class='admin_table seaocore_admin_table' width="100%">
      <thead>
        <tr>
          <?php $class = ( $this->order == 'request_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('request_id', 'DESC');"><?php echo 'ID'; ?></a></th>

          <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo 'Project'; ?></a></th>

          <?php $class = ( $this->order == 'request_amount' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('request_amount', 'DESC');"><?php echo 'Requested Amount'; ?></a></th>

          <th class='admin_table_short'><?php echo "Request Message" ?></th>
          <th class='admin_table_short'><?php echo "Request Date" ?></th>

          <?php $class = ( $this->order == 'response_amount' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('response_amount', 'DESC');"><?php echo 'Response Amount'; ?></a></th>
          <th class=''><?php echo "Response Message" ?></th>
          <th class=''><?php echo "Response Date" ?></th>
          <th class=''><?php echo "Status" ?></th>
          <th class=''><?php echo "Payment" ?></th>
          <th><?php echo "Options" ?></th>
        </tr>
      </thead>
      <?php foreach ($this->paginator as $payment): ?>
        <tr>
          <?php
          if (empty($payment->request_message) || $payment->request_message == '')
            $request_message = '-';
          else
            $request_message = $payment->request_message;

          if (empty($payment->response_message) || $payment->response_message == '')
            $response_message = '-';
          else
            $response_message = $payment->response_message;

//      $request_message = empty($payment->request_message) ? '-' : $payment->request_message; 
//      $response_message = empty($payment->response_message) ? '-' : $payment->response_message;
          $response_amount = empty($payment->response_amount) ? '-' : Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($payment->response_amount);
          if ($payment->response_date == '0000-00-00 00:00:00')
            $response_date = '-';
          else
            $response_date = $payment->response_date;
          if ($payment->request_status == 0):
            $request_status = 'Requested';
          elseif ($payment->request_status == 1):
            $request_status = '<i><font color="red">Deleted</font></i>';
          elseif ($payment->request_status == 2):
            $request_status = '<i><font color="green">Completed</font></i>';
          endif;

          if ($payment->payment_status != 'active'):
            $payment_status = 'No';
          else:
            $payment_status = 'Yes';
          endif;
          ?>
          <td class='admin_table_short'><?php echo $payment->request_id ?></td>
          <td class='admin_table_short admin_table_bold'>
            <?php $proejctItem = $this->item('sitecrowdfunding_project', $payment->project_id); ?>
            <?php if (empty($proejctItem)): ?>
              <i>Project Deleted</i>
            <?php else: ?>
              <?php echo $this->htmlLink($proejctItem->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($proejctItem->getTitle(), 10), array('title' => $proejctItem->getTitle(), 'target' => '_blank')) ?>
            <?php endif; ?>
          </td>
          <td class='admin_table_short'><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($payment->request_amount) ?></td>
          <td class='admin_table_short'><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($request_message, 30) ?></td>
          <td class='admin_table_short'><?php echo $payment->request_date ?></td>
          <td class='admin_table_short'><?php echo $response_amount ?></td>
          <td class='admin_table_short'><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($response_message, 30) ?></td>
          <td class='admin_table_short'><?php echo $response_date ?></td>
          <td class='admin_table_short'><?php echo $request_status ?></td>
          <td class='admin_table_short'><?php echo $payment_status ?></td>
          <td>
            <?php if (empty($proejctItem)): ?>
              <?php echo '-'; ?>
            <?php else: ?>
              <?php
              $delete_url = $this->url(array(
               'module' => 'sitecrowdfunding',
               'controller' => 'payment',
               'action' => 'delete-payment-request',
               'request_id' => $payment->request_id,
               'project_id' => $payment->project_id
                  ), 'admin_default', true);
              $view_url = $this->url(array(
               'module' => 'sitecrowdfunding',
               'controller' => 'payment',
               'action' => 'view-payment-request',
               'request_id' => $payment->request_id
                  ), 'admin_default', true);
              $make_payment_url = $this->url(array(
               'module' => 'sitecrowdfunding',
               'controller' => 'payment',
               'action' => 'process-payment',
               'request_id' => $payment->request_id
                  ), 'admin_default', true);
              ?>
              <?php
              echo '<a href="javascript:void(0)" onclick="Smoothbox.open(\'' . $view_url . '\')"> details </a>';
              if (empty($payment->request_status)):
                if ($payment->payment_status !== 'active'):
                  if ($payment->payment_status == 'initial'):
                    echo ' | ' . $this->htmlLink($this->url(array(
                         'module' => 'sitecrowdfunding',
                         'controller' => 'payment',
                         'action' => 'process-payment',
                         'request_id' => $payment->request_id,
                            ), 'admin_default', true), "approve");
                  else :
                    echo ' | <a href="javascript:void(0)" onclick="Smoothbox.open(\'' . $make_payment_url . '\')">approve payment</a>';
                  endif;
                endif;
                if ($payment->request_status == 0) :
                  echo ' | <a href="javascript:void(0)" onclick="Smoothbox.open(\'' . $delete_url . '\')"> delete </a>';
                endif;
              endif;
              echo '|' . $this->htmlLink($this->url(array('action' => 'payment-to-me', 'project_id' => $payment->project_id), 'sitecrowdfunding_backer', true), "project payment details", array('target' => '_blank'));
              ?>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>  
  <div class="clr mtop10">
    <?php
    echo $this->paginationControl($this->paginator, null, null, array(
     'pageAsQuery' => true,
     'query' => $this->formValues,
    ));
    ?>
  </div>
  <br />
<?php endif; ?>