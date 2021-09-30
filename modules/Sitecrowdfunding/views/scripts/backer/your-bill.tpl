<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: your-bill.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
    Asset.css('<?php
echo $this->layout()->staticBaseUrl
 . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'
?>');
</script>

<?php if (!$this->only_list_content): ?>
    <?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>
    <div class="sitecrowdfunding_dashboard_content">
        <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project)); ?>
        <div class="sitecrowdfunding_project_form sitecrowdfunding_dashboard_innerbox">
            <div id="sitecrowdfunding_manage_backer_content"> 
            <?php endif; ?> 
            <div class="sitecrowdfunding_payment_to_me">
                <h3><?php echo $this->translate('Your Bill of Commissions') ?></h3>
                <p class="mbot10 mtop5">
                    <?php if (Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0)): ?>
                        <?php echo $this->translate("Below, you can view the history of commissions paid by you so far for your project's backing and can also pay the unpaid commissions. [Note: The payments made using 'Stripe Connect' gateway are not added here because those commissions are already paid at the time of payment.]"); ?>
                    <?php else: ?>
                        <?php echo $this->translate("Below, you can view the history of commissions paid by you so far for your project's backing and can also pay the unpaid commissions."); ?>
                    <?php endif; ?>
                </p>

                <?php if (Engine_Api::_()->sitecrowdfunding()->isAllowThresholdNotifications(array('project_id' => $this->project_id))): ?>
                    <div class="tip">
                        <span class="seaocore_txt_red">
                            <?php echo $this->translate("Threshold amount for admin commission bill has been exceeded. Please pay commission for availing uninterrupted services."); ?>
                        </span>
                    </div>
                <?php endif; ?>

                <table class="sitecrowdfunding_dashboard_table sitecrowdfunding_detail_table">
                    <tr class="highlight">
                        <td class="txt_center">
                            <span><?php echo $this->translate('Total Bill Amount [A = B+C]') ?></span>
                            <span class="txt_center bold f_small dblock"><?php echo $this->translate('(Amount to be paid)') ?></span>
                            <div class="txt_center bold"><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($this->totalBillAmount); ?></div>
                        </td>
                        <td class="txt_center">
                            <span><?php echo $this->translate('New Bill Amount [B]') ?></span>
                            <span class="txt_center bold f_small dblock"><?php echo $this->translate('(Since last bill)'); ?></span>
                            <div class="txt_center bold"><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($this->newBillAmount); ?></div>
                        </td>
                        <td class="txt_center">
                            <span><?php echo $this->translate('Remaining Bill Amount [C]') ?></span>
                            <div class="txt_center bold"><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($this->remainingBillAmount) ?></div>
                        </td>
                        <td class="txt_center">
                            <span><?php echo $this->translate('Paid Bill Amount') ?></span>
                            <div class="txt_center bold"><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($this->paidBillAmount) ?></div>
                        </td>
                    </tr> 
                </table>

                <?php if (!empty($this->totalBillAmount)) : ?>
                    <div class="clr mtop10 mbot10 sitecrowdfunding_paybill_link">
                        <i class="fa fa-money"></i>
                        <a class="icon" href="javascript:void(0)" onClick="Smoothbox.open('sitecrowdfunding/backer/bill-payment/project_id/<?php echo $this->project_id ?>');"><?php echo $this->translate("Pay Commission") ?></a>
                    </div>
                <?php endif; ?>

                <div id="project_bill_details" class="commission_monthly_statement">
                    <h4><?php echo $this->translate('Monthly Statements') ?></h4>
                    <?php if (count($this->paginator)): ?>
                        <div class="sitecrowdfunding_detail_table">
                            <table>
                                <tr class="sitecrowdfunding_detail_table_head">
                                    <th><?php echo $this->translate("Month") ?></th>
                                    <th><?php echo $this->translate("Backer Count") ?></th>
                                    <th><?php echo $this->translate("Backer Amount") ?></th>
                                    <th><?php echo $this->translate("Commission Amount") ?></th>
                                    <th class="txt_center"><?php echo $this->translate("Options") ?></th>
                                </tr>
                                <?php foreach ($this->paginator as $payment) : ?>        
                                    <tr>
                                        <td>
                                            <a href="javascript:void(0)" onclick="manage_project_dashboard(56, 'monthly-bill-detail/month/<?php echo $payment->month_no; ?>/year/<?php echo $payment->year ?>', 'backer')">
                                                <?php echo $this->translate("%s %s", $payment->month, $payment->year) ?>
                                            </a>
                                        </td>
                                        <td><?php echo $payment->backer_count ?></td>
                                        <td><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($payment->grand_total) ?></td>
                                        <td><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($payment->commission) ?></td>
                                        <td class="project_actlinks txt_center"> 
                                            <a href="javascript:void(0)" onclick="manage_project_dashboard(56, 'monthly-bill-detail/month/<?php echo $payment->month_no; ?>/year/<?php echo $payment->year ?>', 'backer')" title="<?php echo $this->translate("details") ?>" class="sitecrowdfunding_icon_detail">View</a>
                                        </td>   
                                    </tr>
                                <?php endforeach; ?>  
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="tip">
                        <span>
                            <?php echo $this->translate("You have not paid any commission for this project yet."); ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (!$this->only_list_content): ?>
            </div>
        </div>	
    </div>	
<?php endif; ?>
</div>