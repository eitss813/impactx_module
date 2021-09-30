<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _DashboardNavigation.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $activeItem = $this->activeItem ? $this->activeItem : null; ?> 
<?php
$Sitecrowdfunding_dashboard_profile = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecrowdfunding_dashboard_profile', array(), $activeItem);
$Sitecrowdfunding_dashboard_photovideo = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecrowdfunding_dashboard_photovideo');
$Sitecrowdfunding_dashboard_meta = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecrowdfunding_dashboard_meta');
$Sitecrowdfunding_dashboard_moreinfo = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecrowdfunding_dashboard_moreinfo');
$Sitecrowdfunding_dashboard_settings = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecrowdfunding_dashboard_settings');
$Sitecrowdfunding_dashboard_initiatives = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecrowdfunding_dashboard_initiatives');
$Sitecrowdfunding_dashboard_metrics = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecrowdfunding_dashboard_metrics');
$Sitecrowdfunding_dashboard_form_submit = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecrowdfunding_dashboard_form_submit');
$Sitecrowdfunding_dashboard_admin = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecrowdfunding_dashboard_admin');
$Sitecrowdfunding_dashboard_projects = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecrowdfunding_dashboard_projects');
$Sitecrowdfunding_dashboard_funding = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecrowdfunding_dashboard_funding');

?>

<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css')->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding_dashboard.css');
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js')
?>

<?php
$project = $this->project;
$isEnabledPackage = Engine_Api::_()->sitecrowdfunding()->hasPackageEnable();
$projectEnabledgateway = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->getEnabledGateways($project->project_id);
$isDirectPaymentToAdmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', '0');
$viewer = Engine_Api::_()->user()->getViewer();
$params['project_type_title'] = $this->translate('Projects');
$params['dashboard'] = $this->translate('Dashboard');
//SET META TITLE
Engine_Api::_()->sitecrowdfunding()->setMetaTitles($params);
// if ($this->TabActive != "edit"):
?>
<?php if (!Zend_Controller_Front::getInstance()->getRequest()->getParam('isajax')): ?>
    <?php
    $this->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("sitecrowdfunding_main");
    include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/navigation_header.tpl';
    ?>
<?php endif; ?>
<?php // endif; ?>
<div class="layout_middle <?php if (Engine_Api::_()->hasModuleBootstrap('spectacular')): ?> spectacular_dashboard <?php endif; ?>">
    <div class="generic_layout_container o_hidden"> 
        <div class='seaocore_db_tabs sitecrowdfunding_side_nav'>
            <div class="sitecrowdfunding_dashboard_info clr">
                <div class="sitecrowdfunding_dashboard_info_image">
                    <?php /*
                    <?php if ($this->project->featured == 1): ?>
                    <div class="sitecrowdfunding_featured" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.featuredcolor', '#f72828'); ?>"><?php echo $this->translate('Featured'); ?></div>
                    <?php endif; ?>
                    <?php if ($this->project->sponsored == 1): ?>
                    <div class="sitecrowdfunding_sponsored" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.sponsoredcolor', '#FC0505'); ?>">
                        <?php echo $this->translate('Sponsored'); ?>
                    </div>
                    <?php endif; ?>
                    */ ?>
                    <?php
                    if ($this->project->photo_id) {
                    $url = $this->project->getPhotoUrl('thumb.cover');
                    echo $this->htmlLink($this->project->getHref(), "<img src='" . $url . "'>", array('class' => 'sitecrowdfunding_thumb'));
                    } else {
                    $url = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/nophoto_project_thumb_profile.png";
                    echo $this->htmlLink($this->project->getHref(), "<img src='" . $url . "'>", array('class' => 'sitecrowdfunding_thumb'));
                    }
                    ?>
                </div>
                <div class="sitecrowdfunding_dashboard_info_desc" style="display: none">
                    <div>
                        <h4><?php echo $this->translate('Project Status :') ?></h4>
                        <?php echo $this->project->getProjectState() ?>
                    </div>
                    <div>
                        <h4><?php echo $this->translate('Funding Status :') ?></h4>
                        <?php echo $this->project->getProjectFundingState() ?>
                    </div>
                    <?php if ($isEnabledPackage): ?>
                    <div>
                        <h4><?php echo $this->translate('Package : ') ?></h4>
                        <a href='<?php echo $this->url(array("action" => "detail", 'id' => $this->project->package_id), "sitecrowdfunding_package", true) ?>' onclick="owner(this);
                        return false;" title="<?php echo $this->translate(ucfirst($this->project->getPackage()->title)) ?>"><?php echo $this->translate(ucfirst($this->project->getPackage()->title)); ?></a>
                    </div>
                    <?php if (!$this->project->getPackage()->isFree()): ?>
                    <div>
                        <h4><?php echo $this->translate('Payment: ') ?></h4>
                        <?php
                                if ($this->project->status == "initial"):
                        echo $this->translate("Not made");
                        elseif ($this->project->status == "active"):
                        echo $this->translate("Yes");
                        else:
                        echo $this->translate(ucfirst($this->project->status));
                        endif;
                        ?>
                    </div>
                    <?php endif ?>
                    <div>
                        <h4><?php echo $this->translate('Status :') ?></h4>
                        <?php echo $this->project->getProjectState() ?>
                    </div>
                    <?php if (!empty($this->project->approved_date) && !empty($this->project->approved)): ?>
                    <div>
                        <h4><?php echo $this->translate('Approved :') ?></h4>
                        <?php echo $this->timestamp(strtotime($this->project->approved_date)) ?>
                    </div>
                    <?php if ($isEnabledPackage && $this->project->funding_end_date && $this->project->funding_end_date !== "0000-00-00 00:00:00"): ?>
                    <div>
                        <?php $expiry = $this->project->getExpiryDate(); ?>
                        <?php if ($expiry): ?>
                        <h4><?php echo $this->translate("Expiration Date: "); ?></h4>
                        <?php $expiry = date('M d, Y', strtotime($expiry)); ?>
                        <?php echo $expiry; ?>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($isEnabledPackage): ?>
                    <?php if (Engine_Api::_()->sitecrowdfunding()->canShowPaymentLink($this->project->project_id)): ?>
                    <div class="tip center mtop5">
                                <span class="db_payment_link">
                                    <a href='javascript:void(0);' onclick="submitSession(<?php echo $this->project->project_id ?>)"><?php echo $this->translate('Make Payment'); ?></a>
                                    <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), "sitecrowdfunding_session_payment", true) ?>">
                                        <input type="hidden" name="project_id_session" id="project_id_session" />
                                    </form>
                                </span>
                    </div>
                    <?php endif; ?>
                    <?php if (Engine_Api::_()->sitecrowdfunding()->canShowRenewLink($this->project->project_id)): ?>
                    <div class="tip mtop5">
                                <span style="margin:0px;"> <?php echo $this->translate("Please click "); ?>
                                    <a href='javascript:void(0);' onclick="submitSession(<?php echo $this->project->project_id ?>)"><?php echo $this->translate('here'); ?></a><?php echo $this->translate(" to renew project."); ?>
                                    <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), "sitecrowdfunding_session_payment", true) ?>">
                                        <input type="hidden" name="project_id_session" id="project_id_session" />
                                    </form>
                                </span>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div style="padding:10px;display: flex;justify-content: center;align-items: center">
                <a href="<?php echo $this->layout()->staticBaseUrl.'projects/delete/'.$this->project->project_id.'/format/smoothbox' ?>" class="buttonlink button delete_button_custom smoothbox">Delete Project</a>
            </div>

            <ul>
                <li class="seaocore_db_head" onclick="editProjectProfileExpand()">
                    <h3><?php echo $this->translate("Edit Project Profile"); ?> &nbsp
                        <i  id="edit-project-profile"  class="fa fa-arrow-circle-up" style="float: right;"></i>
                    </h3>
                </li>
                <?php if (count($Sitecrowdfunding_dashboard_profile)): ?>
                    <?php
                    foreach ($Sitecrowdfunding_dashboard_profile as $item):
                        $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
                            'reset_params', 'route', 'module', 'controller', 'action', 'type',
                            'visible', 'label', 'href')));
                        if (!isset($attribs['active'])) {
                            $attribs['active'] = false;
                        }
                        ?>
                    <?php if($this->translate($item->getLabel()) != 'Project Impact' ): ?>
                        <li id="edit-project-profile-sub" style="display: none;" <?php echo($attribs['active'] ? ' class="selected"' : ''); ?> >
                        <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
                        </li>
                    <?php endif; ?>

                    <?php endforeach; ?>
                <?php endif; ?>

                <li class="seaocore_db_head" onclick="editProjectFundingExpand()">
                    <h3><?php echo $this->translate("Funding"); ?> &nbsp
                        <i  id="edit-project-funding"  class="fa fa-arrow-circle-up" style="float: right;"></i>
                    </h3>
                </li>
                <?php if (count($Sitecrowdfunding_dashboard_funding)): ?>
                <?php
                    foreach ($Sitecrowdfunding_dashboard_funding as $item):
                        $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
                'reset_params', 'route', 'module', 'controller', 'action', 'type',
                'visible', 'label', 'href')));
                if (!isset($attribs['active'])) {
                $attribs['active'] = false;
                }
                ?>
                <li id="edit-project-funding-sub" style="display: none;" <?php echo($attribs['active'] ? ' class="selected"' : ''); ?> >
                <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
                </li>


                <?php endforeach; ?>
                <?php endif; ?>



                <li class="seaocore_db_head" onclick="formSubmitExpand()">
                    <h3><?php echo $this->translate("Form Submission"); ?> &nbsp
                        <i  id="form-submit"  class="fa fa-arrow-circle-up" style="float: right;"></i>
                    </h3>
                </li>
                <?php if (count($Sitecrowdfunding_dashboard_form_submit)): ?>
                <?php
                    foreach ($Sitecrowdfunding_dashboard_form_submit as $item):
                        $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
                'reset_params', 'route', 'module', 'controller', 'action', 'type',
                'visible', 'label', 'href')));
                if (!isset($attribs['active'])) {
                $attribs['active'] = false;
                }
                ?>
                <li id="form-submit-sub" style="display: none;" <?php echo($attribs['active'] ? ' class="selected"' : ''); ?> >
                <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
                </li>


                <?php endforeach; ?>
                <?php endif; ?>
                <li class="seaocore_db_head" onclick="managePhotoVideoExpand()">
                    <h3><?php echo $this->translate("Manage Photos & Videos"); ?>
                        <i  id="manage-photo-video" class="fa fa-arrow-circle-up" style="float: right;"></i>
                    </h3>
                </li>
                <?php if (count($Sitecrowdfunding_dashboard_photovideo)): ?>
                <?php
                    foreach ($Sitecrowdfunding_dashboard_photovideo as $item):
                        $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
                'reset_params', 'route', 'module', 'controller', 'action', 'type',
                'visible', 'label', 'href')));
                if (!isset($attribs['active'])) {
                $attribs['active'] = false;
                }
                ?>
                <li id="manage-photo-video-sub"  style="display: none;" <?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
                <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
                </li>
                <?php endforeach; ?>
                <?php endif; ?>

                <li class="seaocore_db_head" onclick="addEditMetaExpand()">
                    <h3><?php echo $this->translate("SDG Goals and Categories"); ?>
                        <i  id="add-edit-meta"  class="fa fa-arrow-circle-up" style="float: right;"></i>
                    </h3>
                </li>
                <?php if (count($Sitecrowdfunding_dashboard_meta)): ?>
                <?php
                    foreach ($Sitecrowdfunding_dashboard_meta as $item):
                        $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
                'reset_params', 'route', 'module', 'controller', 'action', 'type',
                'visible', 'label', 'href')));
                if (!isset($attribs['active'])) {
                $attribs['active'] = false;
                }
                ?>
                <li id="add-edit-meta-sub"   style="display: none;"  <?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
                <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
                </li>
                <?php endforeach; ?>
                <?php endif; ?>

                <li class="seaocore_db_head" onclick="addEditProjectExpand()">
                    <h3><?php echo $this->translate("Add/Edit More Project Info"); ?>
                        <i  id="add-edit-project"  class="fa fa-arrow-circle-up" style="float: right;"></i>
                    </h3>
                </li>
                <?php if (count($Sitecrowdfunding_dashboard_moreinfo)): ?>
                <?php
                    foreach ($Sitecrowdfunding_dashboard_moreinfo as $item):
                        $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
                'reset_params', 'route', 'module', 'controller', 'action', 'type',
                'visible', 'label', 'href')));
                if (!isset($attribs['active'])) {
                $attribs['active'] = false;
                }
                ?>
                <li id="add-edit-project-sub" style="display: none;"  <?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
                <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
                </li>
                <?php endforeach; ?>
                <?php endif; ?>


                <li class="seaocore_db_head"  onclick="settingsExpand()">
                    <h3>
                        <?php echo $this->translate("Settings"); ?>
                        <i  id="settings" class="fa fa-arrow-circle-up" style="float: right;"></i>
                    </h3>
                </li>
                <?php if (count($Sitecrowdfunding_dashboard_settings)): ?>
                <?php
                    foreach ($Sitecrowdfunding_dashboard_settings as $item):
                        $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
                'reset_params', 'route', 'module', 'controller', 'action', 'type',
                'visible', 'label', 'href')));
                if (!isset($attribs['active'])) {
                $attribs['active'] = false;
                }
                ?>
                <li id="settings-sub" style="display: none;" <?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
                <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
                </li>
                <?php endforeach; ?>
                <?php endif; ?>

                <!-- show the initiative menu-->
                <!-- 1. if initiative added -->
                <?php if(!empty($project->initiative_id)):?>
                <li class="seaocore_db_head"  onclick="initiativesExpand()">
                    <h3>
                        <?php echo $this->translate("Initiative"); ?>
                        <i id="initiatives" class="fa fa-arrow-circle-up" style="float: right;"></i>
                    </h3>
                </li>
                <?php if (count($Sitecrowdfunding_dashboard_initiatives)): ?>
                <?php
                    foreach ($Sitecrowdfunding_dashboard_initiatives as $item):
                        $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
                'reset_params', 'route', 'module', 'controller', 'action', 'type',
                'visible', 'label', 'href')));
                if (!isset($attribs['active'])) {
                $attribs['active'] = false;
                }
                ?>
                <li id="initiatives-sub" style="display: none;" <?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
                <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
                </li>
                <?php endforeach; ?>
                <?php endif; ?>
                <?php endif; ?>

                <!-- show the metrics menu-->
                <li class="seaocore_db_head"  onclick="metricsExpand()">
                    <h3>
                        <?php echo $this->translate("Metric"); ?>
                        <i id="metrics" class="fa fa-arrow-circle-up" style="float: right;"></i>
                    </h3>
                </li>
                <?php if (count($Sitecrowdfunding_dashboard_metrics)): ?>
                    <?php foreach ($Sitecrowdfunding_dashboard_metrics as $item):?>
                        <?php
                            $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array( 'reset_params', 'route', 'module', 'controller', 'action', 'type', 'visible', 'label', 'href')));
                            if (!isset($attribs['active'])) {
                                $attribs['active'] = false;
                            }
                        ?>
                        <li id="metrics-sub" style="display: none;" <?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
                            <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
  <!-- show the my blogs menu-->
                <li class="seaocore_db_head"  onclick="myBlogsExpand()">
                    <h3>
                        <?php echo $this->translate("My Blogs"); ?>
                        <i id="myblog" class="fa fa-arrow-circle-up" style="float: right;"></i>
                    </h3>
                </li>
              
                <li id="myblog-sub" style="display: none;" <?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
                   <a class="menu_sitecrowdfunding_dashboard_metrics sitecrowdfunding_dashboard_metricdetails" target="_blank" href='<?php echo $this->url(array( 'action' => 'manage', 'project_id' => $project->project_id ), 'sesblog_general', true) ?>'>
        <span ><?php echo $this->translate('My Blogs') ?></span>
        </a>
                </li>
              

               <!-- <li class="seaocore_db_head">
                    <h3><?php echo $this->translate("Admin"); ?></h3>
                </li>
                <?php if (count($Sitecrowdfunding_dashboard_admin)): ?>
                    <?php
                    foreach ($Sitecrowdfunding_dashboard_admin as $item):
                        $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
                            'reset_params', 'route', 'module', 'controller', 'action', 'type',
                            'visible', 'label', 'href')));
                        if (!isset($attribs['active'])) {
                            $attribs['active'] = false;
                        }
                        ?>
                        <li<?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
                            <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?> -->

               <!-- <li class="seaocore_db_head">
                    <h3><?php echo $this->translate("Funding"); ?></h3>
                </li>
                <?php if (count($Sitecrowdfunding_dashboard_projects)): ?>
                    <?php
                    foreach ($Sitecrowdfunding_dashboard_projects as $item):
                        $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
                            'reset_params', 'route', 'module', 'controller', 'action', 'type',
                            'visible', 'label', 'href')));
                        if (!isset($attribs['active'])) {
                            $attribs['active'] = false;
                        }
                        ?>
                        <li<?php echo($attribs['active'] ? ' class="selected"' : ''); ?>>
                            <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs); ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>-->
            </ul>
        </div>
        <?php if ($isEnabledPackage): ?>
            <?php if (Engine_Api::_()->sitecrowdfunding()->canShowPaymentLink($this->project->project_id)): ?>
                <div class="o_hidden transaction_tip">
                    <div class="tip">
                        <span>
                            <?php echo $this->translate("The package for your project requires payment. You have not fulfilled the payment for this project."); ?>
                            <a href='javascript:void(0);' onclick="submitSession(<?php echo $this->project->project_id ?>)"><?php echo $this->translate('Make payment now!'); ?></a>
                            <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), "sitecrowdfunding_session_payment", true) ?>">
                                <input type="hidden" name="project_id_session" id="project_id_session" />
                            </form>
                        </span>
                    </div>
                </div>
            <?php endif; ?> 

            <?php $tempEnabledgateway = $projectEnabledgateway->toArray(); ?>
            <?php if (empty($tempEnabledgateway) && empty($isDirectPaymentToAdmin)): ?>
                <div class="o_hidden transaction_tip">
                    <div class="tip">
                        <span>
                            <?php echo $this->translate("You have not configured or enabled the payment gateways for this project yet. Please "); ?>
                            <a href="<?php echo $this->url(array('action' => 'payment-info', 'project_id' => $this->project->project_id), 'sitecrowdfunding_specific', true) ?>"><?php echo $this->translate('click here'); ?></a>
                            <?php echo $this->translate('to configure and enable the payment gateways.'); ?>
                        </span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (Engine_Api::_()->sitecrowdfunding()->canShowRenewLink($this->project->project_id)): ?>
                <div class="o_hidden">
                    <div class="tip">
                        <span>
                            <?php if ($this->project->funding_end_date <= date('Y-m-d H:i:s')): ?>
                                <?php echo $this->translate("Your package for this project has expired and needs to be renewed.") ?>
                            <?php else: ?>
                                <?php echo $this->translate("Your package for this project is about to expire and needs to be renewed.") ?>
                            <?php endif; ?>
                            <?php $project_id = $this->project->project_id; ?> 
                            <a href='javascript:void(0);' onclick="submitSession(<?php echo $this->project->project_id ?>)"><?php echo $this->translate('Click here'); ?></a><?php echo $this->translate(" to renew it."); ?> 
                            <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), "sitecrowdfunding_session_payment", true) ?>">
                                <input type="hidden" name="project_id_session" id="project_id_session" />
                            </form>
                        </span>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>



        <script type="text/javascript">
            var globalContentElement = en4.seaocore.getDomElements('content');
            en4.core.runonce.add(function () {
                var element = $(event.target);
                if (element.tagName.toLowerCase() == 'a') {
                    element = element.getParent('li');
                }
            });

            if ($$('.ajax_dashboard_enabled')) {
                en4.core.runonce.add(function () {
                    $$('.ajax_dashboard_enabled').addEvent('click', function (event) {
                        var element = $(event.target);
                        event.stop();
                        var ulel = this.getParent('ul');
                        $(globalContentElement).getElement('.sitecrowdfunding_dashboard_content').innerHTML = '<div class="seaocore_content_loader"></div>';
                        ulel.getElements('li').removeClass('selected');

                        if (element.tagName.toLowerCase() == 'a') {
                            element = element.getParent('li');
                        }

                        element.addClass('selected');
                        showAjaxBasedContent(this.href);
                    });
                });
            }

            function showAjaxBasedContent(url) {

                if (history.pushState) {
                    history.pushState({}, document.title, url);
                } else {
                    window.location.hash = url;
                }

                en4.core.request.send(new Request.HTML({
                    url: url,
                    'method': 'get',
                    data: {
                        format: 'html',
                        'isajax': 1
                    }, onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                        $(globalContentElement).innerHTML = responseHTML;
                        Smoothbox.bind($(globalContentElement));

                        if (SmoothboxSEAO) {
                            SmoothboxSEAO.bind($(globalContentElement));
                        }

                        en4.core.runonce.trigger();
                        if (window.InitiateAction) {
                            InitiateAction();
                        }
                    }
                }));
            }

            var requestActive = false;
            window.addEvent('load', function () {
                InitiateAction();
            });

            var InitiateAction = function () {
                formElement = $$('.global_form')[0];
                if (typeof formElement != 'undefined') {
                    formElement.addEvent('submit', function (event) {
                        if (typeof submitformajax != 'undefined' && submitformajax == 1) {
                            submitformajax = 0;
                            event.stop();
                            Savevalues();
                        }
                    })
                }
            }

            var Savevalues = function () {
                if (requestActive)
                    return;

                requestActive = true;
                var pageurl = $(globalContentElement).getElement('.global_form').action;

                currentValues = formElement.toQueryString();
                $('show_tab_content_child').innerHTML = '<div class="seaocore_content_loader"></div>';
                if (typeof page_url != 'undefined') {
                    var param = (currentValues ? currentValues + '&' : '') + 'isajax=1&format=html&page_url=' + page_url;
                }
                else {
                    var param = (currentValues ? currentValues + '&' : '') + 'isajax=1&format=html';
                }

                var request = new Request.HTML({
                    url: pageurl,
                    onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                        $(globalContentElement).innerHTML = responseHTML;
                        InitiateAction();
                        requestActive = false;
                    }
                });
                request.send(param);
            }

            function submitSession(id) {
                document.getElementById("project_id_session").value = id;
                document.getElementById("setSession_form").submit();
            }

            function owner(thisobj) {
                var Obj_Url = thisobj.href;
                Smoothbox.open(Obj_Url);
            }
            var ShowDashboardProjectContent = function (ProjectUrl, show_url, edit_url, project_id, tab_id, only_list_content) {
                if (typeof only_list_content == 'undefined') {
                    only_list_content = false;
                }
                $('sitecrowdfunding_manage_backer_content').innerHTML = '<center><img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" /></center>';
                var request = new Request.HTML({
                    'url': ProjectUrl,
                    'method': 'POST',
                    'data': {
                        'format': 'html',
                        'ShowDashboardProjectContent': 1,
                        'is_ajax': 1,
                        'only_list_content' : only_list_content
                    },
                    onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {

                        $('sitecrowdfunding_manage_backer_content').innerHTML = responseHTML; 
                        en4.core.runonce.trigger();
                    }

                });

                request.send();
            };

            var manage_project_dashboard = function (id, actionName, controller, tempURL, only_list_content) {
                if (typeof only_list_content == 'undefined') {
                    only_list_content = false;
                }
                var globalWrapperElement = en4.seaocore.getDomElements('contentWrapper');
                new Fx.Scroll(window).start(0, $(globalWrapperElement).getCoordinates().top);
                // IT'S THE VARIABLE WHICH SEND TO SITECROWDFUNDING CONTROLLERS FOR GETTING REQUIRED RESULT ACCORDING TO REQUEST. WHERE 'actionName' and 'controller' IS THE VARIABLE, WHICH HAVE THE INFORMATION OF SITEEVENT PLUGIN CONTROLLERS AND ACTION.
                var tempProjectDeshboardUrl = en4.core.baseUrl + 'sitecrowdfunding/' + controller + '/' + actionName + '/' + 'project_id/<?php echo $this->project_id ?>/menuId/' + id;
                ShowDashboardProjectContent(tempProjectDeshboardUrl, '', '', '<?php echo $this->project_id ?>', id, only_list_content);
            };

            function editProjectProfileExpand(){
                var x = document.querySelectorAll("#edit-project-profile-sub");
                console.log('x',x[0].style.display);
                console.log('y',document.getElementById('edit-project-profile').className);
                var status = x[0].style.display == "block" ? "none" : "block" ;
                if(status == "none")
                    document.getElementById('edit-project-profile').className= "fa fa-arrow-circle-up";
                 else
                    document.getElementById('edit-project-profile').className= "fa fa-arrow-circle-down";

                    for (i = 0; i < x.length; i++) {
                        x[i].style.display = status;
                    }
            }
            function editProjectFundingExpand(){
                var x = document.querySelectorAll("#edit-project-funding-sub");
                console.log('x',x[0].style.display);
                console.log('y',document.getElementById('edit-project-funding').className);
                var status = x[0].style.display == "block" ? "none" : "block" ;
                if(status == "none")
                    document.getElementById('edit-project-funding').className= "fa fa-arrow-circle-up";
                else
                    document.getElementById('edit-project-funding').className= "fa fa-arrow-circle-down";

                for (i = 0; i < x.length; i++) {
                    x[i].style.display = status;
                }
            }
            function formSubmitExpand(){
                var x = document.querySelectorAll("#form-submit-sub");
                console.log('x',x[0].style.display);
                console.log('y',document.getElementById('edit-project-profile').className);
                var status = x[0].style.display == "block" ? "none" : "block" ;
                if(status == "none")
                    document.getElementById('form-submit').className= "fa fa-arrow-circle-up";
                else
                    document.getElementById('form-submit').className= "fa fa-arrow-circle-down";

                for (i = 0; i < x.length; i++) {
                    x[i].style.display = status;
                }
            }
            function managePhotoVideoExpand(){
                var x = document.querySelectorAll("#manage-photo-video-sub");
                var status = x[0].style.display == "block" ? "none" : "block" ;
                if(status == "none")
                    document.getElementById('manage-photo-video').className= "fa fa-arrow-circle-up";
                else
                    document.getElementById('manage-photo-video').className= "fa fa-arrow-circle-down";

                for (i = 0; i < x.length; i++) {
                    x[i].style.display = status;
                }
            }
            function addEditMetaExpand(){
                var x = document.querySelectorAll("#add-edit-meta-sub");
                var status = x[0].style.display == "block" ? "none" : "block" ;
                if(status == "none")
                    document.getElementById('add-edit-meta').className= "fa fa-arrow-circle-up";
                else
                    document.getElementById('add-edit-meta').className= "fa fa-arrow-circle-down";

                for (i = 0; i < x.length; i++) {
                    x[i].style.display = status;
                }
            }
            function addEditProjectExpand(){
                var x = document.querySelectorAll("#add-edit-project-sub");
                var status = x[0].style.display == "block" ? "none" : "block" ;
                if(status == "none")
                    document.getElementById('add-edit-project').className= "fa fa-arrow-circle-up";
                else
                    document.getElementById('add-edit-project').className= "fa fa-arrow-circle-down";

                for (i = 0; i < x.length; i++) {
                    x[i].style.display = status;
                }
            }
            function settingsExpand(){
                var x = document.querySelectorAll("#settings-sub");
                var status = x[0].style.display == "block" ? "none" : "block" ;
                if(status == "none")
                    document.getElementById('settings').className= "fa fa-arrow-circle-up";
                else
                    document.getElementById('settings').className= "fa fa-arrow-circle-down";

                for (i = 0; i < x.length; i++) {
                    x[i].style.display = status;
                }
            }
            function initiativesExpand(){
                var x = document.querySelectorAll("#initiatives-sub");
                var status = x[0].style.display == "block" ? "none" : "block" ;
                if(status == "none")
                    document.getElementById('initiatives').className= "fa fa-arrow-circle-up";
                else
                    document.getElementById('initiatives').className= "fa fa-arrow-circle-down";

                for (i = 0; i < x.length; i++) {
                    x[i].style.display = status;
                }
            }
            function metricsExpand(){
                var x = document.querySelectorAll("#metrics-sub");
                var status = x[0].style.display == "block" ? "none" : "block" ;
                if(status == "none")
                    document.getElementById('metrics').className= "fa fa-arrow-circle-up";
                else
                    document.getElementById('metrics').className= "fa fa-arrow-circle-down";

                for (i = 0; i < x.length; i++) {
                    x[i].style.display = status;
                }
            }
            function myBlogsExpand(){
                var x = document.querySelectorAll("#myblog-sub");
                var status = x[0].style.display == "block" ? "none" : "block" ;
                if(status == "none")
                    document.getElementById('myblog').className= "fa fa-arrow-circle-up";
                else
                    document.getElementById('myblog').className= "fa fa-arrow-circle-down";

                for (i = 0; i < x.length; i++) {
                    x[i].style.display = status;
                }
            }
        </script>

    <style>
        .delete_button_custom{
            background-color: red !important;
            border: none !important;
        }
        .delete_button_custom:hover{
            background: none;
        }
    </style>