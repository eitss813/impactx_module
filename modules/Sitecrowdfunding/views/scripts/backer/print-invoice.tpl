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
               <!-- <strong><?php echo ($this->logo) ? $this->htmlImage($this->logo) : $this->site_title; ?></strong> -->
                <img style="width: 40%;" src="public/admin/Impact-Network-transparent-logo.png">
            </div>
            <div class="name fright">
                <strong><?php echo $this->translate('RECEIPT') ?></strong>
            </div>
        </div>
    </div>
    <div class="invoice_details_wrap">
        <div class="invoice_order_details_wrap">
          <ul>
            <li>
                <div class="center title">
                    <strong ><?php echo $this->translate("Donation Receipt") ?></strong>
                </div>
            </li>
            <li>
                <div class="logo_image">
                    <div class="center">
                            <a href="<?php echo $this->sitepage->getHref(); ?>" target="_blank" style="width: 100%;" class="center"><img style="width: 54%;" src="<?php echo $this->donate_receipt_logo;?>"></a>

                    </div>
                </div>
            </li>
            <li>
                <div class="center-border">
                    <p><?php echo $this->sitepage->donate_receipt_location; ?> </p>
                </div>
            </li>
              <li>
                  <div class="center-border" style="margin-top: 4px;">
                      <p class="o_hidden"><?php echo $this->locale()->toDateTime($backer->creation_date) . '<br/>'; ?></p>
                  </div>
              </li>
            <li>
                <div class="invoice_project_name">
                    <p>Name : <?php echo $this->translate("%s", $this->user_detail->displayname); ?></p>
                </div>
            </li>
            <li>
                <div class="invoice_project_name">
                    <p>Contact Information : <?php echo $this->translate("%s", $this->user_detail->email); ?></p>
                </div>
            </li>
             <li>
                 <div class="donation_amount invoice_project_name">
                     <p>Amount : <?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($backer->amount); ?></p>
                 </div>
             </li>
              <li>
                  <div  class="donation_amount invoice_project_name" style="display: flex;">
                      Thank you for contributing to &nbsp; <a href="<?php echo $this->project->getHref();?>" target="_blank"> <?php echo $this->project->title; ?></a>
                  </div>
              </li>
              <?php if($this->project->photo_id) : ?>
                   <li>
                      <div class="logo_image">
                      <div class="center">
                          <div class="sitecrowdfunding_grid_thumb center">
                              <?php $fsContent = ""; ?>
                              <?php
                      if ($this->project->photo_id) {
                              $url = $this->project->getPhotoUrl('thumb.cover');
                              echo $this->htmlLink($this->project->getHref(), "<img class='project_img' src='" . $url . "'>", array('class' => 'sitecrowdfunding_thumb'));
                              } else {
                              $url = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/nophoto_project_thumb_profile.png";
                              echo $this->htmlLink($this->project->getHref(), "<img class='project_img' src='" . $url . "'>", array('class' => 'sitecrowdfunding_thumb'));
                              }
                              ?>
                          </div>
                      </div>
                  </div>
                  </li>
                   <li>
                      <div class="project_title center">
                          <a href="<?php echo $this->project->getHref();?>" target="_blank"><?php echo $this->project->title; ?> </a>
                      </div>
                  </li>
                  <li>
                      <div class="donate_receipt_desc center">
                          <p><?php echo $this->string()->truncate($this->string()->stripTags($this->project->description), 250) ?></p>
                      </div>
                  </li>
              <?php endif ?>
              <br>
              <li>
                  <div class="donate_receipt_desc">
                      <p><?php echo $this->sitepage->donate_receipt_desc; ?> </p>
                  </div>
              </li>
        </ul>
        </div>

    </div>

</div>
<script type="text/javascript">
    window.print();
</script>
<style>

    .invoice_head_wrap{
        padding-top: 3px;
    }
    .project_title{
        color: #44AEC1;
        font-size: 16px;
        cursor: pointer;
        font-weight: bold;
    }
    .logo_image{
        display: flex;
        justify-content: center;
        width: 99.7%;
        margin-bottom: 18px;
        margin-top: 8px;
    }
    .center , a.sitecrowdfunding_thumb {
        display: flex;
        justify-content: center;
    }
    .center-border{
        font-size: 14px;
        font-weight: bold;
        display: flex !important;
        justify-content: center;
    }
    .donation_amount{
        font-size: 18px;
        font-weight: bold;
    }
    img.project_img {
        margin-bottom: 10px;
        width: 51%;
        height: 179px;
        margin-top: 8px;
    }
    a:link, a:visited {
        color: #5fb9c9 !important;
        text-decoration: none;
    }
    .invoice_project_name {
        padding: 8px;
        text-align: unset !important;
        font-size: 15px !important;
        border-left:  unset !important;
        border-right:  unset !important;
    }
    .invoice_add_details, .invoice_order_details_wrap li, .invoice_ttlamt_box_wrap, .invoice_note_box {
        padding: unset !important;
    }
    .invoice_order_details_wrap li > div {
        display: flex !important;
        vertical-align: middle;
    }
    .donate_receipt_desc{
        padding: 8px;
        text-align: unset !important;
        font-size: 15px !important;
        line-height: 29px !important;
    }
    .sitecrowdfunding_grid_thumb{
       width: 100%;
    }
    .title{
        font-size: 19px;
    }
    .invoice_order_details_wrap {
        border: unset !important;
    }
    .invoice_order_details_wrap li {
        border-bottom:unset !important;
    }
     /* mozilaa css */
    @-moz-document url-prefix() {
        img.project_img {
            margin-bottom: 10px;
            width: 100%;
            height: 179px;
            margin-top: 8px;
        }
    }
</style>