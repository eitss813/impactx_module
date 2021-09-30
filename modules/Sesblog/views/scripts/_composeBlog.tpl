<?php
/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: _composeBlog.tpl 2016-07-26 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
?>
<?php 
$request = Zend_Controller_Front::getInstance()->getRequest();
$requestParams = $request->getParams();
 
if($requestParams['action'] == 'home' && $requestParams['module'] == 'user' && $requestParams['controller'] == 'index' && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesadvancedactivity')) {
?>
<style>
#compose-sesblog-activator{display:inline-block !important;}
</style>
<?php 
  $baseUrl = $this->layout()->staticBaseUrl;
  $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sesblog/externals/styles/styles.css'); 
  $this->headScript()->appendFile($baseUrl . 'externals/tinymce/tinymce.min.js'); 
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    if (Composer.Plugin.Sesblog)
      return;
    Asset.javascript('<?php echo $baseUrl ?>application/modules/Sesblog/externals/scripts/composer_blog.js', {
      onLoad:  function() {
        var type = 'wall';
        if (composeInstance.options.type) type = composeInstance.options.type;
        composeInstance.addPlugin(new Composer.Plugin.Sesblog({
          title : '<?php echo $this->string()->escapeJavascript($this->translate("Write New Blog")) ?>',
          lang : {
            'Write New Blog' : '<?php echo $this->string()->escapeJavascript($this->translate("Write New Blog")) ?>'
          },
          requestOptions : {
						url:"<?php echo $this->url(array('action'=>'create'), 'sesblog_general'); ?>",	
					},
        }));
      }});
  });
</script>
<?php } ?>
