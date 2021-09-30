<?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project-create/common.tpl'; ?>
<div class="sitecrowdfunding_project_new_steps">
    <?php echo $this->form->render(); ?>
    <div class="common_star_info"> <span>* </span> Means required information</div>
</div>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js');?>

<script type="text/javascript">
    var $j = jQuery.noConflict();

    $j(document).ready(function() {
        $j("#reason-element > .description").prependTo("#reason-element");
    });

</script>
