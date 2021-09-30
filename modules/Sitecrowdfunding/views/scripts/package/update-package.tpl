<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: update-package.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (empty($this->is_ajax)) : ?>
    <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
    <?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>
    <div class="sitecrowdfunding_dashboard_content">
        <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project)); ?>
        <div id="show_tab_content" class="global_form">
        <?php endif; ?> 
        <div class="sitecrowdfunding_package">
            <ul>        
                <li>
                    <div class="sitecrowdfunding_package_title">
                        <div class="sitecrowdfunding_package_link">
                            <?php if (Engine_Api::_()->sitecrowdfunding()->canShowPaymentLink($this->project_id)): ?>
                                <div class="fleft mright10">  
                                    <a href='javascript:void(0);' onclick="submitSession(<?php echo $this->project_id ?>);"><?php echo $this->translate('Make Payment'); ?></a>
                                    <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), "sitecrowdfunding_session_payment", true) ?>">
                                        <input type="hidden" name="project_id_session" id="project_id_session" />
                                    </form>
                                </div>
                            <?php endif; ?>
                            <?php if (Engine_Api::_()->sitecrowdfunding()->canShowRenewLink($this->project_id)): ?>
                                <div class="fleft mright10">  
                                    <a href='javascript:void(0);' onclick="submitSession(<?php echo $this->project_id ?>);"><?php echo $this->translate('Renew'); ?></a>
                                    <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), "sitecrowdfunding_session_payment", true) ?>">
                                        <input type="hidden" name="project_id_session" id="project_id_session" />
                                    </form>
                                </div>
                            <?php endif; ?>
                            <!--Start Cancel Plan-->
                            <?php if (Engine_Api::_()->sitecrowdfunding()->canShowCancelLink($this->project_id)): ?>
                                <div class="fleft mright10">  
                                    <a href='<?php echo $this->url(array('action' => 'cancel', 'package_id' => $this->package->package_id, 'project_id' => $this->project_id), "sitecrowdfunding_package", true); ?>' class="smoothbox" >
                                        <?php echo $this->translate('Cancel Package') ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <!--End Cancel Plan-->
                        </div>
                        <h3><?php echo $this->translate("Current Package: ") . $this->translate(ucfirst($this->package->title)); ?></h3>
                    </div>
                    <?php $item = $this->package; ?>
                    <?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/package/_packageInfo.tpl'; ?>
                </li>
            </ul>
        </div>
        <div class='sitecrowdfunding_package mtop15'>
            <?php if (count($this->paginator)): ?>
                <ul class="sitecrowdfunding_packages_list sitecrowdfunding_packages_list_horizontal o_hidden mbot10">
                    <li>
                        <h3><?php echo $this->translate('Available Packages') ?></h3>
                        <span>  <?php echo $this->translate("If you want to change the package for your project, please select one package from the below list."); ?></span>
                    </li>
                    <li>
                        <div class="tip o_hidden mbot10">
                            <span>
                                <?php echo $this->translate("Note: Once you change package for your project, all the settings of the project will be applied according to the new package, including features available, price, etc."); ?>
                            </span>
                        </div>
                    </li>
                    <?php foreach ($this->paginator as $item): ?>
                        <li>
                            <?php if (empty($this->package_view)): ?>
                                <div class="sitecrowdfunding_package_list_title">
                                    <div class="sitecrowdfunding_package_link">
                                        <?php
                                        echo $this->htmlLink(
                                                array('route' => "sitecrowdfunding_package", 'action' => 'update-confirmation', "project_id" => $this->project_id, "package_id" => $item->package_id), $this->translate('Change Package'), array('onclick' => 'owner(this);return false', 'title' => $this->translate('Change Package'), 'class' => 'common_btn'));
                                        ?>
                                    </div>
                                    <h3>             
                                        <a href='<?php echo $this->url(array("action" => "detail", 'id' => $item->package_id), "sitecrowdfunding_package", true) ?>' onclick="owner(this); return false;" title="<?php echo $this->translate(ucfirst($item->title)) ?>"><?php echo $this->translate(ucfirst($item->title)); ?></a>
                                    </h3>                 
                                </div>
                                <?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/package/_packageInfo.tpl'; ?>
                            <?php else: ?>
                                <?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/package/_verticalPackageInfo.tpl'; ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                    <br />
                    <div>
                        <?php echo $this->paginationControl($this->paginator); ?>
                    </div>
                </ul>
            <?php else: ?>
                <div class="tip">
                    <span>
                        <?php echo $this->translate("There are no other packages yet.") ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
        <?php if (empty($this->is_ajax)) : ?>		
        </div>
    </div>
    </div>
    </div>

<?php endif; ?>
<script type="text/javascript">

    function submitSession(id) {
        document.getElementById("project_id_session").value = id;
        document.getElementById("setSession_form").submit();
    }

    function owner(thisobj) {
        var Obj_Url = thisobj.href;
        Smoothbox.open(Obj_Url);
    }

</script>