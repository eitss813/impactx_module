<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>

<div class="generic_layout_container layout_middle">
    <div class="generic_layout_container layout_core_content">
        <div class="layout_middle">
            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
            <?php echo $this->partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array( 'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Settings', 'sectionDescription' => '')); ?>
            <div class="sitepage_edit_content">
                <?php echo $this->paymentSettingsForm->render($this); ?>
            </div>
        </div>
    </div>
</div>
<style>
  .sitepage_edit_content{
      color: #222 !important;
      font-size: 24px !important;
      padding: 10px 10px;
      margin: -10px -10px 10px -10px;
  }
</style>
<script type="text/javascript">
    let paymentIsTaxDeductibleValue = "<?php echo $this->paymentSettingsForm->getValues()['payment_is_tax_deductible']; ?>"
    if (!paymentIsTaxDeductibleValue || paymentIsTaxDeductibleValue == '0') {
        $("payment_tax_deductible_label-wrapper").style.display = "none";
    }else{
        $("payment_tax_deductible_label-wrapper").style.display = "block";
    }

    function onChangeIsTaxDeductible(value){
        if (!value || value == '0') {
            $("payment_tax_deductible_label-wrapper").style.display = "none";
        }else{
            $("payment_tax_deductible_label-wrapper").style.display = "block";
        }
    }
</script>