<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/style_sitepageverify.css');
?>
                    
<script type="text/javascript">

  function proceedToVerify(resource_id) {
    var comments = '';
    if ($('comments'))
      comments = $('comments').value;
    document.getElementById('verify_pops_loding_image').style.display = '';
    var request = new Request.HTML({
      url: en4.core.baseUrl + 'sitepage/verify/proceed-to-verify',
      data: {
        format: 'html',
        resource_id: resource_id,
        comments: comments
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        window.parent.$('sitepage').innerHTML = responseHTML;
        parent.Smoothbox.close();
      }
    });
    request.send();
  }

  window.addEvent('domready', function() {
    textCounter($('comments'), 'counter', 300)
  });

  function textCounter(field, field2, maxlimit)
  {
    var countfield = document.getElementById(field2);
    if (field) {
      if (field.value.length > maxlimit) {
        field.value = field.value.substring(0, maxlimit);
        return false;
      } else {
        countfield.innerHTML = maxlimit - field.value.length;
      }
    }
  }
</script>
<div class="seaocore_members_popup global_form_popup" id="verify_members_popup">

  <h3 class="mbot10">
    <?php echo $this->translate("Verify %s ?", ucfirst($this->resource_title)) ?>
  </h3>
  <div class="" id="verify_popup_content" >
    <div class="o_hidden">
      <div class="">
        <?php echo $this->translate("Are you sure you want to verify %s?", ucfirst($this->resource_title)); ?>
        <?php if (!empty($this->is_comment)): ?>
          <?php echo $this->translate(" You can also add your comment below for this."); ?>
          <div id="siteverify_comment" class="clr mtop10" style="display:block;">
            <textarea id="comments" maxlength="300" placeholder="<?php echo $this->translate("Why are you verifying ") . ucfirst($this->resource_title) . '?'; ?>" onkeyup="textCounter(this, 'counter', 300);"></textarea>
            </br>
            <div class="seaocore_browse_list_info_date">
              <span value="300" id="counter"> </span>
              <?php echo $this->translate("characters left."); ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<div class="verify_members_popup_btns">
  <div id="verify_pops_loding_image" style="display: none;">
    <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' />
  </div>
  <button class="mleft5" id="verifybutton" onclick='proceedToVerify("<?php echo $this->resource_id ?>");'><?php echo $this->translate("Verify"); ?></button>
  <?php echo $this->translate(" or "); ?>
  <a href="javascript:void(0);" onclick='javascript:parent.Smoothbox.close();'><?php echo $this->translate("cancel") ?></a>
</div>
