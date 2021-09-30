<div id="<?php echo $this -> id ?>-wrapper" class="yndform_section_break clearfix field_html">
    <div id="<?php echo $this -> id ?>-label" class="form-label">
        <label for="<?php echo $this -> id ?>" class="optional">
            <?php echo $this->translate($this -> params['label'])?>
        </label>
    </div>
    <div id="<?php echo $this -> id ?>-element" class="form-element">
        <?php echo $this -> params['content'] ?>
        <p class="description"><?php echo $this->translate($this -> params['description'])?></p>
    </div>
</div>