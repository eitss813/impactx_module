<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-source.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css'); ?>

<div class="global_form_popup">
    <h3><?php echo $this->translate("Your Videos Badge code"); ?></h3>
    <div>
        <ul>
            <li class="mtop10">
                <?php echo $this->translate("Copy the below HTML code and paste it into the source code for your web page."); ?>
            </li>
            <li class="text-box">
                <?php echo $this->code; ?>
            </li>
            <br /><br />
            <li>
                <button onclick="parent.Smoothbox.close();" ><?php echo $this->translate('Okay') ?></button>
            </li>
        </ul>
    </div>
</div>
<style type="text/css">
    .text-box{
        border: 2px solid #ccc;
        padding:5px;
        width:600px;
        overflow:hidden;
        margin-top:10px;
    }
</style>