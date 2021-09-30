<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Yndynamicform/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css');

$this->headScript()
    ->appendFile($baseUrl . 'application/modules/Yndynamicform/externals/scripts/jquery-1.10.2.min.js')
    ->appendFile($baseUrl . 'application/modules/Yndynamicform/externals/scripts/jquery-ui-1.11.4.min.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Yndynamicform/externals/scripts/dynamic.js');
?>
<div id="<?php echo $this -> id ?>-wrapper" class="form-wrapper yndform_color_wrapper">
    <div id="<?php echo $this -> id ?>-label" class="form-label">
        <label for="<?php echo $this -> id ?>" class="<?php echo $this -> params['required'] ? 'required':'optional' ?>">
            <?php echo $this->translate($this -> params['label'])?>
        </label>
    </div>
    <div id="<?php echo $this -> id ?>-element" class="form-element">
        <input type="text" value="<?php echo $this -> params['value'] ?>" onchange="this.fireEvent('change')" name="<?php echo $this -> id ?>" id="<?php echo $this -> id ?>" value="">
        <p class="description"><?php echo $this->translate($this -> params['description'])?></p>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function () {
        var current = new Date();
        var yearRange = (current.getFullYear() - 100) +':' + (current.getFullYear() + 10);
        jQuery('#<?php echo $this -> id ?>').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage: '',
            changeMonth: true,
            changeYear: true,
            yearRange: yearRange,
            buttonImageOnly: true,
            buttonText: '',
        });
    });
</script>