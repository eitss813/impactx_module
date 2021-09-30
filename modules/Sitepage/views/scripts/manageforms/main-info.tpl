
<div class="generic_layout_container layout_middle">
    <div class="generic_layout_container layout_core_content">
        <div class="layout_middle">
            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
            <div style="display: flex;">
                <div style="float:right;">
                    <?php echo $this->htmlLink(array(
                    'controller' => 'manageforms',
                    'action' => 'manage',
                    'page_id'=>$this->page_id),
                    '<span class="ynicon yn-arr-left">'.$this->translate('Back').' &nbsp; </span>',array(
                    'class' => 'yndform_backform'
                    ))
                    ?>
                </div>
                <?php echo $this->
                partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array(
                'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> ''.$this->form->getTitle(), 'sectionDescription' => '')); ?>

            </div>

            <div class="yndform_title_parent" style="#44AEC1 !important;">


                <div class="yndform_edit_form clearfix">
                    <?php echo $this->editform->render($this) ?>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
    window.addEvent('load', function() {

        document.getElementsByTagName('h3')[11].innerHTML = 'Edit Form';
    });

</script>
<style>
    .ynicon {
        color: #0087c3 !important;
        font-size: 19px;
    }
</style>