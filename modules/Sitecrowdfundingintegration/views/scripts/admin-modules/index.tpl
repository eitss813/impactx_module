<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfundingintegration
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
    <?php echo $this->translate('Crowdfunding / Fundraising / Donations Plugin'); ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
<?php endif; ?>

<h3 style="margin-bottom:6px;"><?php echo $this->translate("Manage Modules"); ?></h3>

<p>
    <?php echo $this->translate('Here, you can manage various modules to enable users to create, edit, view and perform various actions on projects for that module using this plugin. If you do not want users to create, edit, view and perform various actions on projects for a particular module, then simply disable that module from here.'); ?>
</p>
<br />

<?php $integratedTableName = Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding'); ?>

<?php if (!empty($this->integrated)): ?>
    <table class='admin_table'>
        <thead>
            <tr>
                <th align="left">
                    <?php echo $this->translate("Module Name"); ?>
                </th>
                <th class="admin_table_centered">
                    <?php echo $this->translate("Title"); ?>
                </th>
                <th class="admin_table_centered">
                    <?php echo $this->translate("Item Type"); ?>
                </th>
                <th class="admin_table_centered">
                    <?php echo $this->translate("Enabled"); ?>
                </th>
                <th class="admin_table_centered">
                    <?php echo $this->translate("Options"); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->integrated as $item): ?>
                <tr> 
                    <td>
                        <?php
                        $moduleTitle = '';
                        if (Engine_Api::_()->hasModuleBootstrap('sitereview') && Engine_Api::_()->hasModuleBootstrap('sitereviewlistingtype')) {
                            $moduleTitle = 'Reviews & Ratings - Multiple Listing Types';
                        } elseif (Engine_Api::_()->hasModuleBootstrap('sitereview')) {
                            $moduleTitle = 'Multiple Listing Types Plugin Core (Reviews & Ratings Plugin)';
                        }
                        $explodedResourceType = explode('_', $item['item_type']);
                        if (isset($explodedResourceType[2]) && $moduleTitle && $item['item_module'] == 'sitereview') {
                            $listingtypesTitle = Engine_Api::_()->getDbtable('listingtypes', 'sitereview')->getListingRow($explodedResourceType[2])->title_plural;
                            $listingtypesTitle = $listingtypesTitle . ' ( ' . $moduleTitle . ' ) ';
                            $moduleTitle = $listingtypesTitle;
                        } elseif ($item['item_module'] != 'sitereview') {
                            $moduleTitle = Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getModuleTitle($item['item_module']);
                        }
                        ?>
                        <?php echo $moduleTitle ?>
                    </td>
                    <td class="admin_table_centered"><?php echo $item['item_title'] ? $item['item_title'] : $moduleTitle; ?></td>
                    <td class="admin_table_centered"><?php echo $item['item_type']; ?></td>
                    <td class="admin_table_centered">
                        <?php if ($item['enabled']) : ?>
                            <a title="<?php echo $this->translate('Disable Module'); ?>" href='<?php echo $this->url(array('module' => 'sitecrowdfundingintegration', 'controller' => 'modules', 'action' => 'enabled-disabled', 'id' => $item['module_id']), 'admin_default', true) ?>'>
                                <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/approved.gif" />
                            </a>
                        <?php else: ?>
                            <a title="<?php echo $this->translate('Enable Module'); ?>" href='<?php echo $this->url(array('module' => 'sitecrowdfundingintegration', 'controller' => 'modules', 'action' => 'enabled-disabled', 'id' => $item['module_id']), 'admin_default', true) ?>'>
                                <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/disapproved.gif" />
                            </a>
                        <?php endif; ?>
                    </td>
                    <td class="admin_table_centered">
                        <?php
                        echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfundingintegration', 'controller' => 'modules', 'action' => 'edit', 'id' => $item['module_id'], 'item_module' => $item['item_module']), $this->translate("edit"));
                        echo ' | ' . $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfundingintegration', 'controller' => 'modules', 'action' => 'delete', 'id' => $item['module_id']), $this->translate("delete"), array('class' => 'smoothbox'));
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="tip">
        <span><?php echo $this->translate("You have not added any module yet."); ?></span>
    </div>
<?php endif; ?>