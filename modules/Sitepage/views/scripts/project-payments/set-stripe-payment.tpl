<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>

<div class="generic_layout_container layout_middle">
    <div class="generic_layout_container layout_core_content">
        <div class="layout_middle">
            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
            <?php echo $this->partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array( 'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Payment Methods', 'sectionDescription' => '')); ?>
            <div class="sitepage_edit_content">
                <?php echo $this->projectPaymentForm->render($this); ?>
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