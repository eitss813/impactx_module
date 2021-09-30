<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headTranslate(array(
  'Remove', 'Click to remove this entry.',
  'Upload failed', 'Upload Progress ({size})',
  '{name} already added.', 'An error occurred.',
  'FAILED ( {name} ) : {error}', 'Reached Maximum File Uploads',
  'Minimum Files Size Deceeded - {filename} ( {filesize} )',
  'Maximum Files Size Exceeded - {filename} ( {filesize} )',
  'Invalid File Type - %s (%s)',
));
?>
<?php $viewClass = 'uploader-' . $this->data['view'] . '-view';?>
<?php $extraVars = is_array($this->data['vars']) ? $this->data['vars'] : array(); ?>
<?php $elementName = $this->element->getName(); ?>
<ul>
  <li><ul id="demo-list" class="demo-list <?php echo $viewClass ?>"></ul></li>
  <li class="sitealbum_addphotos_btn upload-link" id="sitealbum_addphotos_btn">
    <a class="buttonlink" href="javascript:void(0);" id="demo-browse"><?php echo $this->translate('Add Photos') ?></a>
    <a class="buttonlink icon_clearlist clear-list" href="javascript:void(0);" id="demo-clear" style='display: none;'><?php echo $this->translate('Clear List') ?></a>
  </li>
</ul>
<div class="progress-bar"></div>
<input type="hidden" name="<?php echo $elementName; ?>" class="file-ids" id="file" value="" />
<script type="text/javascript">
  en4.core.runonce.add(function() {
    wrapper = $('<?php echo $elementName; ?>' + '-wrapper');
    callbacks = {
      ui_button: wrapper.getElement('.upload-link'),
      ui_list: wrapper.getElement('.demo-list'),
      ui_drop_area: wrapper.getElement('.upload-link'), 
      clear_list: $('demo1-clear'),
      dropAreaClick: false,
      onActivateCustom: function() {
        $('demo1-browse').addEvent('click', function(e) {
          e.stop();
          if(this.options.multiple || (!this.options.multiple && !this.isUploading)) 
            this.lastInput.click();
        }.bind(this));
        this.totalWidth = 0;
      },
      onItemProgress: function(el, perc) {
        el.getElement('.file-progress').setStyle('opacity', perc / 100);
      },
      onItemAdded: function(el, file, imagedata) {
        self = this;
        el.addClass('file');
        el.adopt(new Element('a', {'class': 'file-remove','html': 'x', title : this.language.translate('Click to remove this entry.') })
          .addEvent('click', function(e){e.stop(); self.cancel(file.id, el)}))
        .adopt(new Element('span', {'class': 'file-size', 'html': file.size}))
        .adopt(new Element('span', {'class': 'file-info'}))
        .adopt(new Element('span', {'class': 'file-name', 'html': file.name}))
        .adopt(new Element('div', {'class': 'file-progress'}).set('tween', {duration: 200}))

        if(!file.type) return;
        if(file.type.match('image') && imagedata){
          el.addClass('image');
          el.getElement('.file-info').adopt(new Element('img', {'src': imagedata}));
        } else if (file.type.match('audio') || file.type.match('flac')){
          el.addClass('audio');
        }
      },
      onItemComplete: function(el, file, response) {
        el.removeClass('file-uploading').addClass('file-success');
        el.getElement('.file-progress').set('html', '100%').tween('width', 140);
        el.set('data-file_id', response[this.paramId]);
        value = this.fileIdsElement.get('value') + response[this.paramId] + ' ';
        this.fileIdsElement.set('value', value);
        // custom function defined in index/upload-album.tpl
        en4.sitealbum.onPhotoUpload(el, response);
      },
    };
    en4.seaocore.initSeaoFancyUploader(<?php echo Zend_Json::encode($this->data) ?>, callbacks);
  });
</script>