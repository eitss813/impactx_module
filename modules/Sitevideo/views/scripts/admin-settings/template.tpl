<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: template.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

    
<div class="global_form_1">
  <div>
    <div>
      <div><ul><li>
      <?php echo $this->translate("This plugin is activated successfully. Now to set up this plugin properly and perform, a few basic & required actions, please visit the <b>'Support'</b> tab available in this plugin to see the videos & read the helpful content for How you can set up this plugin and how you can integrate other plugins to have added features on your site ? Also, if your site has already some videos uploaded, then please go to <b>'Synchronize'</b> tab first and follow the process to create big thumbnail images for your already uploaded videos to show them attractively in different layouts & widgets.");?><br /><br />

      <br/><br/>
      <?php echo '<button onclick="window.open(\'admin/sitevideo/support\',\'_blank\');parent.Smoothbox.close () ;"> '. $this->translate("Go to Support") . ' </button>'; ?>&nbsp;&nbsp;&nbsp;&nbsp;
      <?php echo '<button onclick="window.open(\'admin/sitevideo/settings/synchronize-video\',\'_blank\');parent.Smoothbox.close();" target="_blank">  '. $this->translate("Go to Synchronize") . ' </button>'; ?>&nbsp;&nbsp;&nbsp;&nbsp;
      <?php echo '<button onclick="parent.Smoothbox.close () ;"> '. $this->translate("Cancel") . ' </button>'; ?>
    </li></ul></div></div>
  </div>
</div>

<style type="text/css">
  .global_form_1 {
    clear:both;
    overflow:hidden;
    margin:15px 0 0 15px;
  }
  .global_form_1 > div {
    -moz-border-radius:7px 7px 7px 7px;
    background-color:#E9F4FA;
    float:left;
    width:600px;
    overflow:hidden;
    padding:10px;
  }
  .global_form_1 > div > div {
    background:none repeat scroll 0 0 #FFFFFF;
    border:1px solid #D7E8F1;
    overflow:hidden;
    padding:20px;
  }
  .global_form_1 .form-sucess {
    margin-bottom:10px;
  }
  .global_form_1 .form-sucess li {
    -moz-border-radius:4px 4px 4px 4px;
    background:#C8E4B6;
    border:2px solid #95b780;
    color:#666666;
    font-weight:bold;
    padding:0.5em 0.8em;
  }
  table td
  {
    border-bottom:1px solid #f1f1f1; 
    padding:5px;
    vertical-align:top;
  }
</style>
