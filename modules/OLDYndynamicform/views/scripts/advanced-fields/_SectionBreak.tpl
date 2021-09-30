<div id="<?php echo $this -> id ?>-wrapper" class="yndform_section_break clearfix">
    <div id="<?php echo $this -> id ?>-label" class="form-label">
        <label for="<?php echo $this -> id ?>" class="<?php echo $this -> params['required'] ? 'required':'optional' ?>">
            <?php echo $this->translate($this -> params['label'])?>
        </label>
    </div>
    <div id="<?php echo $this -> id ?>-element" class="form-element">
        <div id="<?php echo $this -> id ?>" class="section_break"></div>
        <p class="description"><?php echo $this->translate($this -> params['description'])?></p>
    </div>
</div>