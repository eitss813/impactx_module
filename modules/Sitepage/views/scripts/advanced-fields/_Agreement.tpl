<div id="<?php echo $this -> id ?>-wrapper" class="yndform_section_break clearfix field_form">
    <div id="<?php echo $this -> id ?>-label" class="form-label">
        <label for="<?php echo $this -> id ?>" class="optional">
            <?php echo $this->translate($this -> params['label'])?>
        </label>
    </div>
    <div id="<?php echo $this -> id ?>-element" class="form-element">
        <div class="yndform_agreement" style="<?php echo $this -> params['style'] ?>"><?php echo $this -> params['content'] ?></div>
        <div class="yndform_agreement_checkbox">
            <input type="hidden" name="<?php echo $this -> id ?>" value="">
            <input type="checkbox" name="<?php echo $this -> id ?>" id="<?php echo $this -> id ?>" class="yndform_agreement_checkbox-element">
            <label for="<?php echo $this -> id ?>" class="optional">I agree to the terms of service</label>
        </div>
    </div>
</div>