<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: print-invoice.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$reward = $this->reward;
$backer = $this->backer;
?> 
<?php if (!empty($this->sitecrowdfunding_print_invoice_no_permission)) : ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("You don't have permission to print the invoice of this order.") ?>
        </span>
    </div>
    <?php
    return;
endif;
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding_print.css'); ?>
<link href="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding_print.css' ?>" type="text/css" rel="stylesheet" media="print">

<div class="invoice_wrap">
    <div class="invoice_head_wrap">
        <div class="invoice_head">
            <div class="logo fleft">
                <strong><?php echo ($this->logo) ? $this->htmlImage($this->logo) : $this->site_title; ?></strong>
            </div>
            <div class="name fright">
                <strong><?php echo $this->translate('INVOICE') ?></strong>
            </div>
        </div>
    </div>
    <div class="invoice_project_name">
       <strong><?php echo $this->translate("Project Name") ?></strong> : <?php echo $this->project->title; ?>
    </div>
    <div class="invoice_details_wrap">
         <div class="invoice_order_details_wrap">
            <ul>
                <?php if (!empty($backer->backer_id)): ?>
                    <li>
                        <div><strong><?php echo $this->translate("Name"); ?> : &nbsp;</strong></div>
                        <div><?php echo $this->translate("%s", $this->user_detail->displayname); ?></div>
                    </li>
                <?php endif; ?>
                <li>
                    <div><strong><?php echo $this->translate("Payment Id") ?> : &nbsp;</strong></div> 
                    <div><?php echo $this->translate("#%s", $backer->backer_id); ?></div>
                </li> 
                <li>
                    <div><strong><?php echo $this->translate('Backed on'); ?></strong> : &nbsp;</div>
                    <div class="o_hidden"><?php echo $this->locale()->toDateTime($backer->creation_date) . '<br/>'; ?> </div>
                </li> 
                <li>
                    <div><strong><?php echo $this->translate('Payment Gateway'); ?> : &nbsp;</strong></div>
                    <div><?php echo Engine_Api::_()->sitecrowdfunding()->getGatwayName($backer->gateway_id) . '<br/>'; ?> </div>
                </li>
                <?php if($backer->payment_status == 'authorised'): ?>
                    <li>
                        <div><strong><?php echo $this->translate('Payment Status'); ?> : &nbsp;</strong></div>
                        <div><?php echo $this->translate('Pre-approved'); ?></div>
                    </li>
                <?php endif; ?>  
            </ul>
        </div>
        <div class="invoice_add_details_wrap">
            <?php if (!empty($reward)): ?>
                <div class="invoice_add_details">
                    <strong><?php echo $this->translate("Reward Selected : "); ?>&nbsp;&nbsp;</strong>
                    <?php echo $this->translate($reward->getTitle()) ?>
                    <!--<p><?php echo $this->translate($reward->getDescription()); ?></p>-->
                </div>
                <div class="invoice_add_details"> 
                    <?php if (!empty($backer->shipping_address1) || !empty($backer->shipping_address2) || !empty($backer->shipping_city) || !empty($backer->shipping_zip)): ?>
                        <strong><?php echo $this->translate("Shipping Location :"); ?>&nbsp;&nbsp;</strong>
                        <?php echo ($backer->shipping_address1) ? $this->translate($backer->shipping_address1) : ''; ?>
                        <?php echo ($backer->shipping_address2) ?  $this->translate($backer->shipping_address2): ''; ?>
                        <?php echo ($backer->shipping_city) ?  $this->translate($backer->shipping_city): ''; ?>
                        <?php echo ($backer->shipping_zip) ?  $this->translate($backer->shipping_zip): ''; ?>
                    <?php endif; ?>

                    <?php if ($backer->shipping_country): ?>
                        <strong>
                            <?php echo $this->translate("Shipping Country :"); ?>&nbsp;&nbsp;
                        </strong>
                        <?php $region = Engine_Api::_()->getItem('sitecrowdfunding_region', $backer->shipping_country); ?>
                        <?php echo $this->translate($region->country_name); ?>
                    <?php endif; ?>
                </div>
                <div class="invoice_add_details"> 
                    <?php if (isset($reward->reward_status) && $reward->reward_status): ?>
                        <?php echo $this->translate("Reward Sent: "); ?> 
                    <?php else: ?>
                        <strong><?php echo $this->translate("Estimated Delivery : "); ?>&nbsp;&nbsp;</strong>
                        <?php echo date('F Y', strtotime($reward->delivery_date)); ?>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div>
                <?php echo $this->translate("No Reward Selected"); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
   

    <div class="invoice_amount_heading"><strong><?php echo $this->translate("Amount Details") ?></strong></div>
    <div id="manage_order_tab" class="dblock">
        <div class="backer_detail_table sitecrowdfunding_data_table">
            <table style="width: 100%;">
                <tr>
                    <td width="90%" class="txt_right"><?php echo $this->translate("Backed Amount"); ?></td> 
                    <td width="10%" class="txt_right"><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($backer->amount); ?></td> 
                </tr> 
            </table>
        </div>
    </div>
    <div class="invoice_amount_heading"><strong><?php echo $this->translate("Summary") ?></strong></div>
    <div class="invoice_ttlamt_box_wrap">
        <?php if ($this->shipping_included): ?>
            <div class="invoice_ttlamt_box">  
                <div class="clr"> 
                    <div class="invoice_order_info fleft"><?php echo $this->translate('Shipping Cost'); ?> : </div>
                    <div class="fright"><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($backer->shipping_price); ?><br /></div>
                </div> 
            </div>
        <?php endif; ?>
        <table style="width: 100%;" bgcolor="#eaeaea">
        <tr>
          <td width="90%" style="text-align: right;"><strong><?php echo $this->translate('Grand Total'); ?> : </strong>&nbsp;&nbsp;</td>
          <td width="10%" style="text-align: right;"><strong><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($backer->amount); ?></strong></td> 
        </tr>
        </table>
    </div>  
</div>
<script type="text/javascript">
    window.print();
</script>