<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div id="usertextboxcontent" style="display:none;">
  <?php echo nl2br($this->userPagestext); ?>
</div>

<script language="JavaScript">
  var usertextboxcontent = $('usertextboxcontent').innerHTML;
  function function1() {
    $("write").style.display="none";
    $("write1").style.display="block";
    $('fname').focus();
  }
 
  function upperCase()
  {
    var sub_id = '<?php echo $this->sitepage->page_id; ?>';
    var str =document.getElementById('fname').value.replace(/\n/g,'<br />');
    var str_temp =document.getElementById('fname').value;
    $('write1').style.display="none";
    $('id_saveimage').style.display="block"; 
    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl + 'sitepage/profile/display/',
      data : {
        format : 'html',
        text_string : str_temp,
        page_id : sub_id
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if (str == '') {

          str = "<div class='write_link'><a href='javascript:void(0);' onclick='function1()'>" + '<?php echo $this->string()->escapeJavascript($this->translate("Write something about")) ?> '+ '<?php echo $this->string()->escapeJavascript($this->sitepage->title); ?>' + "</a></div>";
          $('write').innerHTML = str;
        }
        else {
          $('write').innerHTML = '<div class="des_edit"><div class="edit_icon"><a href="javascript:void(0);" onclick="function1()"></a></div><div class="des">' + str +' </div></div>';
        }
        usertextboxcontent = str; 
        $('write').style.display="block";
        $('id_saveimage').style.display="none"; 
      }
    }));
  }

</script>
<?php if (Engine_Api::_()->sitepage()->isPageOwner($this->sitepage)) { ?>
  <div class="sitepage_write_overview">
    <div id="write">
        <?php echo '<div class="des_edit"><div class="edit_icon"><a href="javascript:void(0);" onclick="function1()"></a></div><div class="des">' . nl2br($this->userPagestext) . '</div></div>' ?>
    </div>
    <div style='display:none;' id="write1" >
      <div class="textarea">
        <textarea rows="2" cols="10" onblur="upperCase()" id="fname" style='display:block;'><?php echo $this->userPagestext; ?></textarea>
      </div>
      <div class="edit_icon">
        <a href="javascript:void(0);" onclick="function1()"></a>
      </div>	
    </div>
    <div class="des_edit" style='display:none;' id="id_saveimage">
      <center>
        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/spinner.gif" alt="" />
      </center>
    </div>
  </div>
<?php } elseif (!empty($this->userPagestext)) { ?>
  <div class="sitepage_write_overview">
    <div class="details" id="write">
      <?php echo nl2br($this->userPagestext) ?>
    </div>
  </div>
<?php } ?>

<script language="JavaScript">
window.addEvent('load',function() {
        
   <?php if(empty($this->userPagestext)): ?>
			
      var content = "<div class='write_link'><a href='javascript:void(0);' onclick='function1()'>" + '<?php echo $this->string()->escapeJavascript($this->translate("Write something about")) ?> '+ '<?php echo $this->string()->escapeJavascript($this->sitepage->title); ?>' + "</a></div>";
      if($('write'))
      $('write').innerHTML = content;
   <?php endif; ?>
        
});
    
</script>
