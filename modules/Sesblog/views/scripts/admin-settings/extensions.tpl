<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesblog
 * @package    Sesblog
 * @copyright  Copyright 2017-2018 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: statistics.tpl  2018-04-23 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php include APPLICATION_PATH .  '/application/modules/Sesblog/views/scripts/dismiss_message.tpl';?>
<?php $moduleApi = Engine_Api::_()->getDbTable('modules', 'core'); ?>
<div class='settings'>
  <form class="global_form">
    <div>
      <h3><?php echo $this->translate("Blog Extensions") ?> </h3>
      <p class="description">
        <?php echo $this->translate("Below are blog extensions."); ?>
      </p>
        <table class='admin_table' style="width: 100%;">
          <thead>
            <tr>
               <th>Extension</th>
               <th align="center">Enabled</th>
            </tr>
          </thead>   
          <tbody>
              <tr>
                <td class="extname">
                  <a href="admin/sesblogpackage/package/settings">Packages for Allowing Blog Creation Extension</a>
                </td>
                <td class="text-center">
                  <?php if($moduleApi->isModuleEnabled('sesblogpackage')): ?>
                    <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesblog', 'controller' => 'admin-settings', 'action' => 'moduleenable', 'modulename' => 'sesblogpackage', 'enabled' => 0), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Disable')))) ?>
                  <?php else: ?>
                    <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesblog', 'controller' => 'admin-settings', 'action' => 'moduleenable', 'modulename' => 'sesblogpackage', 'enabled' => 1), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Enable')))) ?>
                  <?php endif; ?> 
                </td>
              </tr>
          </tbody>
        </table>
    </div>
  </form>
</div>
<style type="text/css">
.extname a{font-weight:bold;}
</style>
