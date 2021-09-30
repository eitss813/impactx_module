<div id="<?php echo $this -> id ?>-wrapper" class="form-wrapper">
    <div id="<?php echo $this -> id ?>-label" class="form-label">
        <label for="<?php echo $this -> id ?>" class="optional">
            <?php echo $this->translate($this -> params['label'])?>
        </label>
    </div>
    <div id="<?php echo $this -> id ?>-element" class="form-element">
        <?php $spamSettings = Engine_Api::_()->getApi('settings', 'core')->core_spam ?>
        <div class="text_editor" id="<?php echo $this -> id ?>"><?php echo $this -> params['body'] ?></div>
        <p class="description"><?php echo $this->translate($this -> params['description'])?></p>
    </div>
</div>