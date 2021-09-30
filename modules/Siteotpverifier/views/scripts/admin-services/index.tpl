<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    statistics.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2>
  <?php echo 'One Time Password (OTP) Plugin'; ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='siteotpverifier_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<div>
  <p> Here, you can configure the SMS services which you want to use for sending OTP to your users. </p>
</div>
<br/>

<table class='admin_table' style='width: 100%;'>
  <thead>
    <tr>
      <th align="left" style="width: 50%;"><?php echo "Title" ?></th>
      <th align="center" class='center' style="width: 25%;"><?php echo "Enabled" ?></th>
      <th align="left" style="width: 25%;"><?php echo"Options" ?></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td align="left" style="width: 50%;">Virtual SMS Client</td>
      <td class="admin_table_centered" style="width: 25%;">
        <?php
        echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.integration') == "testmode" ?
          $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Siteotpverifier/externals/images/approved.gif', '', array()) :
          $this->htmlLink(array('reset' => false, 'action' => 'enable', 'enable_service' => 'testmode'), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Siteotpverifier/externals/images/disapproved.gif', '', array()))
        ?>
      </td>
      <td align="left" style="width: 25%;">
        <a href="<?php echo $this->url(array('module' => 'siteotpverifier', 'controller' => 'mobile', 'action' => 'index'), 'siteotpverifier_extended', true) ?>" target="_blank">
          <?php echo "View SMS" ?>
        </a>
      </td>
    </tr>
    <tr>
      <td align="left" style="width: 50%;">Amazon</td>
      <?php if( in_array('amazon', $this->notFoundServices) ): ?>
        <td><div class="tip"><span>Service library not found!</span></div></td>
        <td><a onclick="showDownloadProgressBox()" href="<?php echo $this->url(array('module' => 'siteotpverifier', 'controller' => 'services', 'action' => 'download', 'service' => 'amazon'), 'admin_default', true) ?>">
            <?php echo "Download" ?>
          </a></td>
      <?php else: ?>
        <td class="admin_table_centered" style="width: 25%;">
          <?php
          echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.integration') == "amazon" ?
            $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Siteotpverifier/externals/images/approved.gif', '', array()) :
            $this->htmlLink(array('reset' => false, 'action' => 'enable', 'enable_service' => 'amazon'), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Siteotpverifier/externals/images/disapproved.gif', '', array()))
          ?>
        </td>
        <td align="left" style="width: 25%;"><a href="<?php echo $this->url(array('module' => 'siteotpverifier', 'controller' => 'settings', 'action' => 'amazon'), 'admin_default', true) ?>">
            <?php echo "Edit" ?>
          </a></td>
      <?php endif ?>
    </tr>
    <tr>
      <td align="left" style="width: 50%;">Twilio</td>
      <?php if( in_array('twilio', $this->notFoundServices) ): ?>
        <td><div class="tip"><span>Service library not found!</span></div></td>
        <td><a onclick="showDownloadProgressBox()" href="<?php echo $this->url(array('module' => 'siteotpverifier', 'controller' => 'services', 'action' => 'download', 'service' => 'twilio'), 'admin_default', true) ?>">
            <?php echo "Download" ?>
          </a></td>
      <?php else: ?>
        <td class="admin_table_centered" style="width: 25%;"><?php
          echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.integration') == "twilio" ?
            $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Siteotpverifier/externals/images/approved.gif', '', array()) :
            $this->htmlLink(array('reset' => false, 'action' => 'enable', 'enable_service' => 'twilio'), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Siteotpverifier/externals/images/disapproved.gif', '', array()))
          ?>
        </td>
        <td align="left" style="width: 25%;"><a href="<?php echo $this->url(array('module' => 'siteotpverifier', 'controller' => 'settings', 'action' => 'twilio'), 'admin_default', true) ?>">
            <?php echo "Edit" ?>
          </a></td>
      <?php endif; ?>
    </tr>

  </tbody>
</table>
<div id="download_progress" style="display: none">
  <div>
    <div>
      <h2>Downloading Service Library</h2>
      <p>Please wait. It may take few minutes to download the service library.</p>
      <div style="text-align: center"><img src="application/modules/Core/externals/images/loading.gif" /> Downloading...</div>
    </div>
  </div>
</div>
<script type="text/javascript">
 var showDownloadProgressBox = function() {
  Smoothbox.open($('download_progress').get('html'));
  }
</script>

