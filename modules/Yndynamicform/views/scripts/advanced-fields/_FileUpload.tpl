<div id="<?php echo $this -> id ?>-wrapper" class="form-wrapper">
    <div id="<?php echo $this -> id ?>-label" class="form-label">
        <label for="<?php echo $this -> id ?>" class="<?php echo $this -> params['required'] ? 'required':'optional' ?>">
            <?php echo $this->translate($this -> params['label'])?>
        </label>
    </div>
    <div id="<?php echo $this -> id ?>-element" class="form-element">
        <?php $spamSettings = Engine_Api::_()->getApi('settings', 'core')->core_spam ?>
        <?php if (!empty($this -> params['value'])): ?>
            <!--     Get all files and show       -->
            <?php $file_ids = json_decode($this -> params['value']) -> file_ids;
             $file_names = json_decode($this -> params['value']) -> name;
             $file_sizes = json_decode($this -> params['value']) -> size;
             $storage = Engine_Api::_() -> storage(); ?>
            <span>
            <?php foreach ($file_ids as $key => $item): ?>
                <?php $file = $storage -> get($item);?>
                <?php if(!is_null($file) && $file instanceof Storage_Model_File ): ?>
                    <div class="file-element">
                        <a href="<?php echo $this->url(array('action' => 'download')) ?><?php echo '?file_id=' . $item ?>" target="downloadframe"><span class="ynicon yn-paperclip-o"></span><?php echo $file_names[$key] ?><span>(<?php echo number_format($file_sizes[$key] / 1024).' KB';?>)</span></a>
                        <a href="javascript:void(0);" onclick="this.parentNode.destroy();removeFromFileInput(<?php echo $item ?>)"><span class="ynicon yn-del cal"></span></a>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            </span>
        <?php endif; ?>
        <input class="file_upload" <?php echo $this -> params['required'] ? 'required':'' ?> name="<?php echo $this -> id ?>[]" id="<?php echo $this -> id ?>" type="file"  multiple="multiple" accept="<?php echo $this -> params['allowed_extensions'] ?>" >
        <p class="description"><?php echo $this->translate($this -> params['description'])?></p>
    </div>
</div>
<script type="text/javascript">
    $('<?php echo $this -> id ?>').removeEvents('change').addEvent('change', function () {
        var max_file_size = <?php echo $this -> params['max_file_size'] ? $this -> params['max_file_size'] : 0 ?> ;
        var max_file = <?php echo $this -> params['max_file'] ? $this -> params['max_file'] : 0 ?>;

        // Check max file size
        if (max_file_size > 0) {
            for (var i=0; i < this.files.length; i++)
            {
                if (this.files[i].size > max_file_size * 1024) {
                    var errorMessage = '<span>File ' + this.files[i].name + ' is reached maximum max_file_size.</span>';
                    if ($('error-element-<?php echo $this -> id ?>')) {
                        $('error-element-<?php echo $this -> id ?>').innerHTML = errorMessage;
                    } else {
                        addErrorElement(errorMessage)
                    }
                    $('<?php echo $this -> id ?>').value = '';
                    return;
                }
            }
        }
        if (max_file > 0) {
            // Check max file
            if(this.files.length > max_file) {
                var errorMessage = '<span>You have input reached maximum allowed files.</span>';
                if ($('error-element-<?php echo $this -> id ?>')) {
                    $('error-element-<?php echo $this -> id ?>').innerHTML = errorMessage;
                } else {
                    addErrorElement(errorMessage)
                }
                $('<?php echo $this -> id ?>').value = '';
                return;
            }

            // If dont't have error max_file_size
            if ($('error-element-<?php echo $this -> id ?>')) {
                $('error-element-<?php echo $this -> id ?>').setStyle('display', 'none');
            }
        }
    });
    
    function removeFromFileInput(id) {
        if (!$('removed_file')) {
            var removed_file_element = new Element('input',{
                id: 'removed_file',
                name: 'removed_file',
                type: 'hidden',
            });
            removed_file_element.inject($('<?php echo $this -> id ?>-element'), 'bottom');
        }
        var val = $('removed_file').value;
        if (val == '') {
            val += id;
        } else {
            val += ',' + id;
        }
        $('removed_file').value = val;
    }

    function addErrorElement(errorMessage) {
        var errElement = new Element('div.tip');
        errElement.id = 'error-element-<?php echo $this -> id ?>';
        errElement.innerHTML = errorMessage;
        errElement.inject($("<?php echo $this -> id ?>-element"), 'bottom');
    }
</script>