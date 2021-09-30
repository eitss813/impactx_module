<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: monthly-bill-detail.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript"> 
  Asset.css('<?php echo $this->layout()->staticBaseUrl
	    . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'?>');
</script>

<?php $countPagination = count($this->paginator); ?>
<?php if (empty($this->search)) : ?>
  <div class="sitecrowdfunding_payment_to_me">
    <h3>
      <?php echo $this->translate("%1sYour Bill%2s &raquo %s %s", '<a href="javascript:void(0)" onclick="manage_project_dashboard(56, \'your-bill\', \'backer\');">', '</a>', $this->monthName, $this->year, true) ?>
    </h3>
    <p class="mbot10 mtop5">
      <?php echo $this->translate('Below, you can view the details of your commissions bill for the month of %s %s.', $this->monthName, $this->year); ?>
    </p>

    <div id="payment_request_table">
    <?php endif; ?>
    <?php if ($countPagination): ?>
      <div class="mbot10"><span><?php echo $this->translate('%s backer(s) found.', $this->total_item) ?></span></div>
    <?php endif; ?>
    <div id="monthly_bill_detail">
      <?php if ($countPagination): ?>
        <div class="sitecrowdfunding_detail_table">
          <table>
            <tr class="sitecrowdfunding_detail_table_head">
              <th><?php echo $this->translate("Backer Id") ?></th>
              <th><?php echo $this->translate("Backing Amount") ?></th>
              <th><?php echo $this->translate("Commission") ?></th>
              <th><?php echo $this->translate("Payment") ?></th>
              <th><?php echo $this->translate("Backing Date") ?></th>
              <th class="txt_center"><?php echo $this->translate("Options") ?></th>
            </tr>
            <?php foreach ($this->paginator as $payment) : ?>        
              <tr>
                <td>
                  <a href="javascript:void(0)" onclick="manage_project_dashboard(55, 'view/backer_id/<?php echo $payment->backer_id; ?>', 'backer')">
                    <?php echo '#' . $payment->backer_id ?>
                  </a>
                </td>
                <td><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($payment->grand_total) ?></td>
                <td><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($payment->commission_value) ?></td>
                <td>
                  <?php if ($payment->payment_status == 'not_paid') : ?>
                    <i class="seaocore_txt_red"><?php echo $this->translate("marked as non-payment") ?></i>
                  <?php elseif ($payment->payment_status == 'active') : ?>
                    <?php echo $this->translate("Yes") ?>
                  <?php elseif ($payment->payment_status != 'active') : ?>
                    <?php echo $this->translate("No") ?>
                  <?php endif; ?>
                </td>
                <td><?php echo gmdate('M d,Y, g:i A', strtotime($payment->creation_date)) ?></td>
                <td class="project_actlinks txt_center">
                <?php
                  $view_url = $this->url(array(
                 'module' => 'sitecrowdfunding',
                 'controller' => 'backer', 
                 'action' => 'view',
                 'project_id' => $this->project_id, 
                 'backer_id' => $payment->backer_id, 
                  ), 'default', true);
                ?>
                <?php echo '<a href="javascript:void(0)" onClick="Smoothbox.open(\'' . $view_url . '\')">View</a>' ?>  
                </td>
              </tr>
            <?php endforeach; ?>  
          </table>
        </div>
      </div>

      <div>
        <div id="project_monthly_bill_detail_previous" class="paginator_previous">
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
           'onclick' => '',
           'class' => 'buttonlink icon_previous'
          ));
          ?>
          <span id="bill_detail_spinner_prev"></span>
        </div>

        <div id="project_monthly_bill_detail_next" class="paginator_next">
          <span id="bill_detail_spinner_next"></span>
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
           'onclick' => '',
           'class' => 'buttonlink_right icon_next'
          ));
          ?>
        </div>

        <?php
      else:
        echo '<div class="tip">
          <span>
            ' . $this->translate("You have not any bill payment in this month.") . '
          </span>
        </div>';
      endif;
      ?>
    </div>
    <?php if (empty($this->search)) : ?>
    </div>
  </div>
<?php endif; ?>
<script type="text/javascript">

  en4.core.runonce.add(function () {

    var anchor = document.getElementById('monthly_bill_detail').getParent();
<?php if ($countPagination): ?>
      document.getElementById('project_monthly_bill_detail_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
      $('project_monthly_bill_detail_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

      $('project_monthly_bill_detail_previous').removeEvents('click').addEvent('click', function () {
        $('bill_detail_spinner_prev').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
        
        en4.core.request.send(new Request.HTML({
          url: en4.core.baseUrl + 'sitecrowdfunding/backer/monthly-bill-detail/project_id/' + <?php echo sprintf('%d', $this->project_id) ?>,
          data: {
            format: 'html',
            search: 1,
            month: '<?php echo $this->month ?>',
            year: '<?php echo $this->year ?>',
            page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
          },
          onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
            $('bill_detail_spinner_prev').innerHTML = '';
          }
        }), {
          'element': anchor
        })
      });

      $('project_monthly_bill_detail_next').removeEvents('click').addEvent('click', function () {
        $('bill_detail_spinner_next').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';

        en4.core.request.send(new Request.HTML({
          url: en4.core.baseUrl + 'sitecrowdfunding/backer/monthly-bill-detail/project_id/' + <?php echo sprintf('%d', $this->project_id) ?>,
          data: {
            format: 'html',
            search: 1,
            month: '<?php echo $this->month ?>',
            year: '<?php echo $this->year ?>',
            page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
          },
          onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
            $('bill_detail_spinner_next').innerHTML = '';
          }
        }), {
          'element': anchor
        });
      });

<?php endif; ?>

  });
</script>