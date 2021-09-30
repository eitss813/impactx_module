<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="sitecrowdfunding_dashboard_content">
    <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'sectionTitle'=> 'Initiative Details','sectionDescription' => '')); ?>
    <?php if($this->showContent == true):?>
        <?php echo $this->form->render($this); ?>
        <button name="execute" id="execute"  type="button" onclick="saveChanges()">Save</button>
    <?php else:?>
        <div class="tip">
            <span>
                No questions posted in this initiative.
            </span>
        </div>
    <?php endif; ?>
</div>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js');?>

<script type="text/javascript">
    var $j = jQuery.noConflict();

    function saveChanges(){
        $j('#sitecrowdfunding_create_project_step_initiative_question').submit()
    }
</script>