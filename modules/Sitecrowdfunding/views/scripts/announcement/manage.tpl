<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript" >
    var submitformajax = 1;
</script>
<script type="text/javascript">
    var viewer_id = '<?php echo $this->viewer_id; ?>';
    var url = '<?php echo $this->url(array(), 'sitecrowdfunding_general', true) ?>';

    var manageinfo = function (announcement_id, url, project_id) {

        if (confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure that you want to delete this announcement ? This will not be recoverable after being deleted.")) ?>')) {
            var childnode = $(announcement_id + '_project_main');
            childnode.destroy();
            en4.core.request.send(new Request.JSON({
                url: url,
                data: {
                    announcement_id: announcement_id,
                    project_id: project_id
                },
                onSuccess: function (responseJSON) {
                }
            }))
        }
    };
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="sitecrowdfunding_dashboard_content">
    <?php if (empty($this->is_ajax)) : ?>
        <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'sectionTitle'=> 'Manage Announcements', 'sectionDescription' => 'Below, you can manage the announcements for your project. Announcements are shown on this project profile page.')); ?>
        <div class="layout_middle">
            <div class="sitecrowdfunding_edit_content">
                <div id="show_tab_content">
                <?php endif; ?>
                <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
                <div class="sitecrowdfunding_form">
                    <div>
                        <div class="sitecrowdfunding_dashboard_innerbox">
                            <div class="sitecrowdfunding_manage_announcements">
                               <!-- <h3> <?php echo $this->translate('Manage Announcements'); ?> </h3>
                                <p class="form-description"><?php echo $this->translate("Below, you can manage the announcements for your project. Announcements are shown on this project profile page.") ?></p>
                                <br />-->
                                <div class="">
                                    <a href='<?php echo $this->url(array('controller' => 'announcement', 'action' => 'create', 'project_id' => $this->project_id), 'sitecrowdfunding_extended', true) ?>' class="icon seaocore_icon_add"><?php echo $this->translate("Post New Announcement"); ?></a>
                                </div>
                                <?php if (count($this->announcements) > 0) : ?>
                                    <?php foreach ($this->announcements as $item): ?>
                                        <div id='<?php echo $item->announcement_id ?>_project_main'  class='sitecrowdfunding_manage_announcements_list'>
                                            <div id='<?php echo $item->announcement_id ?>_project'>
                                                <div class="sitecrowdfunding_manage_announcements_title">
                                                    <span><?php echo $this->translate($item->title); ?></span>
                                                    <div class="sitecrowdfunding_manage_announcements_option">
                                                        <?php if ($item->status == 1): ?>
                                                            <a title="<?php echo $this->translate('Enabled'); ?>" class="seaocore_icon_enable seaocore_txt_green" ><?php echo $this->translate('Enabled'); ?></a>
                                                        <?php else: ?>
                                                            <a title="<?php echo $this->translate('Disabled'); ?>" class="seaocore_icon_disable seaocore_txt_red" ><?php echo $this->translate('Disabled'); ?></a>
                                                        <?php endif; ?>
                                                        <?php $url = $this->url(array('controller' => 'announcement', 'action' => 'delete'), 'sitecrowdfunding_extended', true); ?>
                                                        <a href='<?php echo $this->url(array('controller' => 'announcement', 'action' => 'edit', 'announcement_id' => $item->announcement_id, 'project_id' => $this->project_id), 'sitecrowdfunding_extended', true) ?>' class="icon seaocore_icon_edit_sqaure"><?php echo $this->translate("Edit"); ?></a>
                                                        <?php echo $this->htmlLink(array('route' => 'sitecrowdfunding_extended', 'module' => 'sitecrowdfunding', 'controller' => 'announcement', 'action' => 'delete', 'announcement_id' => $item->announcement_id, 'project_id' => $this->project_id), $this->translate('Remove'), array('class' => 'smoothbox seaocore_txt_red seaocore_icon_remove_square')); ?> 
                                                    </div>
                                                </div> 
                                                <div class="sitecrowdfunding_manage_announcements_body show_content_body"> 
                                                    <div class="sitecrowdfunding_manage_announcements_dates">
                                                        <strong><?php echo $this->translate("Start Date : ") ?></strong>&nbsp;<?php echo date('M d Y', strtotime($item->startdate)); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <strong><?php echo $this->translate("End Date : ") ?></strong>&nbsp;
                                                        <?php echo date('M d Y', strtotime($item->expirydate)); ?>
                                                    </div>
                                                    <?php echo $item->body ?>
                                                </div> 
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <br />
                                    <div class="tip">
                                        <span><?php echo $this->translate('No announcements have been posted for this project yet.'); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php $item = count($this->paginator) ?>
                            <input type="hidden" id='count_div' value='<?php echo $item ?>' />
                        </div>
                    </div>
                </div>
                <br />	
                <div id="show_tab_content_child">
                </div>
                <?php if (empty($this->is_ajax)) : ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
</div>
</div>