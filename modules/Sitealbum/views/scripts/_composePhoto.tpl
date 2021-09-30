<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _composePhoto.tpl 10109 2013-10-31 01:53:50Z andres $
 * @author     Sami
 */
?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css');

 $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/scripts/composer_photo.js');
 
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/seao-fancy-uploader/Uploader.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/seao-fancy-uploader/Request.Blob.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/seao-fancy-uploader/Uploader.HTML5.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/scrollbars/scrollbars.min.js');
  $this->headLink()
    ->appendStylesheet($this->layout()->staticBaseUrl . 'externals/seao-fancy-uploader/uploader.css');
  $this->headTranslate(array(
    'Remove', 'Click to remove this entry.',
    'Upload failed', 'Upload Progress ({size})',
    '{name} already added.', 'An error occurred.',
    'FAILED ( {name} ) : {error}', 'Reached Maximum File Uploads',
    'Minimum Files Size Deceeded - {filename} ( {filesize} )',
    'Maximum Files Size Exceeded - {filename} ( {filesize} )',
    'Invalid File Type - %s (%s)',
    'Add Photos'
  ));
?>

<script type="text/javascript">
    var sitealbumInstalled = true;
  en4.core.runonce.add(function() {
        var type = 'wall';
        if (composeInstance.options.type) type = composeInstance.options.type;
        composeInstance.addPlugin(new Composer.Plugin.Photo({
          title : '<?php echo $this->string()->escapeJavascript($this->translate('Add Photo')) ?>',
          albumTitle : '<?php echo $this->string()->escapeJavascript($this->translate('Create Photo Album')) ?>',
          lang : {
            'Add Photo' : '<?php echo $this->string()->escapeJavascript($this->translate('Add Photo')) ?>',
            'Select File' : '<?php echo $this->string()->escapeJavascript($this->translate('Select File')) ?>',
            'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
            'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
            'Unable to upload photo. Please click cancel and try again': '<?php echo $this->string()->escapeJavascript($this->translate('Unable to upload photo. Please click cancel and try again')) ?>'
          },
          requestOptions : {
            'url'  : en4.core.baseUrl + 'sitealbum/album/compose-upload/type/'+type,
          },
          fancyUploadOptions : {
            'url'  : en4.core.baseUrl + 'sitealbum/album/compose-upload/format/json/type/'+type,
          }
        }));
     
  });
</script>