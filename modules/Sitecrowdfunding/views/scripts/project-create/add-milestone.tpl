<?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project-create/common.tpl'; ?>
<?php
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/_commonFunctions.js');
?>
<?php echo $this->form->render(); ?>
<script type="text/javascript">
    window.addEvent('domready', function () {
        if ($('starttime-minute')) {
            $('starttime-minute').value = 0
            $('starttime-minute').style.display = 'none';
        }
        if ($('starttime-ampm')) {
            $('starttime-ampm').value = 'AM'
            $('starttime-ampm').style.display = 'none';
        }
        if ($('starttime-hour')) {
            $('starttime-hour').value = 12
            $('starttime-hour').style.display = 'none';
        }
        if ($('endtime-minute')) {
            $('endtime-minute').value = 50
            $('endtime-minute').style.display = 'none';
        }
        if ($('endtime-ampm')) {
            $('endtime-ampm').value = 'PM'
            $('endtime-ampm').style.display = 'none';
        }
        if ($('endtime-hour')) {
            $('endtime-hour').value = 11
            $('endtime-hour').style.display = 'none';
        }

    });
</script>