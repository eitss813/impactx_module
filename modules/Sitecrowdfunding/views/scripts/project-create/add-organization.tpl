<?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project-create/common.tpl'; ?>
<?php echo $this->form->render(); ?>
<?php
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js');
?>
<script type="text/javascript">

    var $j = jQuery.noConflict();
    function checkIsInternal(value) {

        if (!value || value == '0') {
            $("title-wrapper").style.display = "block";
            $("description-wrapper").style.display = "block";
            $("link-wrapper").style.display='block';
            $("photo-wrapper").style.display='block';
            $("organization_id-wrapper").style.display='none';
        } else {
            $("title-wrapper").style.display = "none";
            $("description-wrapper").style.display = "none";
            $("link-wrapper").style.display='none';
            $("photo-wrapper").style.display='none';
            $("organization_id-wrapper").style.display='block';
        }
    }
    $j(document).ready(function() {
        checkIsInternal($j("input[name='is_internal']:checked").val());
        $("others-wrapper").style.display='none';
        $j('#organization_type').on('change', function() {
            if(this.value == 'others'){
                $("others-wrapper").style.display='block';
            }else{
                $("others-wrapper").style.display='none';
            }
        });

    });
</script>