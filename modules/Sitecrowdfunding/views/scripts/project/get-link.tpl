<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-link.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'); ?>

<div class="global_form_popup">

    <div style="margin-bottom: 38px;" >
        <div style="float: left">   <h3><?php echo $this->translate("Share Project"); ?></h3>  </div>
        <div style="float: right">
            <p onclick="parent.Smoothbox.close();" style="font-size: 20px;"><i class="fa fa-close" id="close_search_icon" ></i></p>
        </div>
    </div>
    <div class="mtop10">
        <?php echo $this->translate("You can use this link to share this  project with anyone, even if they don't have an account on this website. Anyone with the link will be able to see your project."); ?>
    </div>
    <div class="mtop10">
        <textarea style="height:65px;width:450px" id="text-box" class="text-box" onclick="select_all();"> <?php echo $this->url; ?> </textarea>
    </div>

    <button  class="fright" onclick="copyLink()" ><?php echo $this->translate('Copy Link') ?></button>
    <p id="link_toast" style="display:none;">URL link displayed is copied.</p>
   <!-- <?php if (empty($this->noSendMessege)): ?>
        <div>
            <a href= "<?php echo $this->url(array('controller' => 'project', 'action' => 'compose', 'subject' => $this->subject->getGuid(),), 'sitecrowdfunding_project_general', true); ?>" class="buttonlink fleft sitecrowdfunding_icon_message"><?php echo $this->translate('Send in message'); ?></a>
        </div>
    <?php endif; ?>
    -->
</div>
<script>
    function select_all()
    {
        var text_val = document.getElementById('text-box');
        text_val.select();
    }
    function copyLink() {
        var copyText = document.getElementById("text-box");
        /* Select the text field */
        copyText.select();
        copyText.setSelectionRange(0, 99999); /*For mobile devices*/

        /* Copy the text inside the text field */
        document.execCommand("copy");
         document.getElementById('link_toast').style.display="block";
    }
</script>
<style>
    #link_toast{
        display: block;
        color: darkgreen;
        border: 1px solid;
        justify-content: center;
        align-items: center;
        text-align: center;
        width: 253px;
        padding: 5px;
        font-size: 16px;
        margin-top: 7px;
    }
</style>