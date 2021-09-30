<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: uploadalbum.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  var updateTextFields = function()
  { 
    var album = $("#album");
    var name = $("#title-wrapper");
    var auth_tag = $("#auth_tag-wrapper");

    if (album.val() == 0)
    {
      name.css('display',"block");
      auth_tag.css('display',"block");
    }
    else
    {
      name.css('display',"none");
      auth_tag.css('display',"none");
    }
  }
  sm4.core.runonce.add(updateTextFields);
  
  var album_id = '<?php echo $this->album_id ?>';
  var page_id = '<?php echo $this->sitepage->page_id; ?>';
</script>

<?php $albumid = Zend_Controller_Front::getInstance()->getRequest()->getParam('album_id', null); ?>
<?php if (!empty($albumid)): ?>
  <?php $albums = Engine_Api::_()->getItem('sitepage_album', $albumid); ?>
<?php endif; ?>

<?php if(!Engine_Api::_()->sitemobile()->isApp()):?>
<?php 
$breadcrumb = array(
    array("href"=>$this->sitepage->getHref(),"title"=>$this->sitepage->getTitle(),"icon"=>"arrow-r"),
    array("href"=>$this->sitepage->getHref(array('tab' => $this->tab_selected_id)),"title"=>"Albums","icon"=>"arrow-d"),
 );
echo $this->breadcrumb($breadcrumb);
?>
<?php endif; ?>

<div class="layout_middle">
  <?php echo $this->form->render($this) ?>
</div>	


<script type="text/javascript">

  sm4.core.runonce.add(function() { 
    if (DetectAllWindowsMobile()) {
      $.mobile.activePage.find('#form-upload').css('display', 'none');
      $.mobile.activePage.find('#show_supported_message').css('display', 'block');
    } else {
      $.mobile.activePage.find('#form-upload').css('display', 'block');
      $.mobile.activePage.find('#show_supported_message').css('display', 'none');
    } 
  });

</script>


<div style="display:none" id="show_supported_message" class='tip'>

  <span><?php echo $this->translate("Sorry, the browser you are using does not support Photo uploading. You can create an album from your Desktop."); ?><span>

</div>