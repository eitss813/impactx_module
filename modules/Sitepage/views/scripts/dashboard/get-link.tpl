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
    <h3><?php echo $this->translate("Share Page"); ?></h3>
    <div class="mtop10">
        <?php echo $this->translate("You can use this link to share this  project with anyone, even if they don't have an account on this website. Anyone with the link will be able to see your project."); ?>
    </div>
    <div class="mtop10">
        <textarea style="height:65px;width:450px" id="text-box" class="text-box" onclick="select_all();"> <?php echo $this->url; ?> </textarea>
    </div>

    <button  class="fright" onclick="parent.Smoothbox.close();" ><?php echo $this->translate('Okay') ?></button>
</div>
<script>
    function select_all()
    {
        var text_val = document.getElementById('text-box');
        text_val.select();
    }
</script>