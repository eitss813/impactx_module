<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: form-list.tpl 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="staticpage_embedded_code_form">
  <form method="post" class="global_form_popup" id='profile_form'>
    <div>
      <h3><?php echo "Get Embed Code" ?></h3>
      <p>
        <?php echo "Select a Form below and get its Embed Code to insert in your static page. Copy & paste it in the Editor at desired location." ?>
      </p>
      <?php if (!empty($this->multioptions)): ?>
        <h3><span id ="error_message" style="color:red"></span></h3>
        <div id='profile_type'>
          <?php foreach ($this->multioptions as $option_id => $option) { ?>
            <div class="clr form_options">
              <input type = 'Radio' name='type' value = "<?php echo $option_id; ?>" onclick="javascript:GetFormData()"> <?php echo $option; ?> <br>
              <span id='form_id_<?php echo $option_id; ?>' style='display: none;'></span>
            </div>
          <?php } ?>
        </div>
        <br />
        <button type='button'onclick="javascript:parent.Smoothbox.close()"><?php echo "Ok" ?></button>
        <?php echo " or " ?> 
        <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
          <?php echo "cancel" ?></a>

      <?php else : ?>
        <div class="tip">
          <span><?php echo 'No forms have been created yet. Create them from the "Manage Forms" section.'; ?></span>
          <br/>
        </div>
        <button type='button' onclick="javascript:parent.Smoothbox.close()"><?php echo "Ok" ?></button>
      <?php endif; ?>
    </div>
  </form>

  <?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
          TB_close();
    </script>
  <?php endif; ?>
</div>
<script type="text/javascript">
      function GetFormData()
      {
        var option_id;
        var radiobuttons = document.getElementsByName("type");
        for (var i = 0, iLen = radiobuttons.length; i < iLen; i++) {
          if (radiobuttons[i].checked) {
            option_id = radiobuttons[i].value;
          }
        }
        if (option_id == null)
        {
          $('error_message').innerHTML = 'Please select atleast one radio button';
          return false;
        }
        else
        {
          $('error_message').style.display = 'none';
        }

        var url = "<?php echo $this->url(array('action' => 'form-data'), 'sitestaticpage_manageadmins', true); ?>";
        en4.core.request.send(new Request.HTML({
          url: url,
          method: 'post',
          data: {
            option_id: option_id,
            format: 'html'
          },
          onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('form_id_'+ option_id).style.display = 'block';
            $('form_id_'+ option_id).innerHTML = 'Embed code for this Form is:'+ ' ' + '<input type="text" value="[' + 'static' + '_' + 'form' + '_' + responseHTML + ']" readonly="readonly"/>';

          }
        }));
      }
</script>



