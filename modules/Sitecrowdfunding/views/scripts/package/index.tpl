<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>
<?php $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'); ?>
<?php if (!empty($this->viewer->level_id)): ?>
    <?php $level_id = $this->viewer->level_id; ?>
<?php else: ?>
    <?php $level_id = 0; ?>
<?php endif; ?>
<div class="layout_middle sitecrowdfunding_create_wrapper clr">
    <?php if (!empty($this->quota) && $this->current_count >= $this->quota):?>
        <div class="tip"> 
            <span>
                <?php $msg = 'You have already started the maximum number of projects allowed i.e. '; ?>
                <?php echo $this->translate(array("$msg%s project.", "$msg%s projects.",$this->quota),$this->quota ); ?> 
            </span>
        </div>
    <?php else :?>
        <h3><?php echo $this->translate("Create a Project") ?></h3>
        <p><?php echo $this->translate("Create a Project using these quick, easy steps and get going."); ?></p>
        <h4 class="sitecrowdfunding_create_step fleft"><?php echo $this->translate("Choose a cowdfunding projects Package"); ?></h4>
        <div class='sitecrowdfunding_package_page sitecrowdfunding_package_page_horizontal'>
            <?php if ($this->paginator->getTotalItemCount()): ?>
                <ul class="sitecrowdfunding_package_list" id="packages">
                    <li>
                        <span><?php echo $this->translate("Select a package that best matches your requirements. Packages differ in terms of prices and features available to projects started under them. You can change your package anytime later."); ?></span>
                    </li> 
                    <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
                    <?php if (empty($this->package_view) || Engine_Api::_()->sitecrowdfunding()->isSiteMobileMode()): ?>
                        <?php foreach ($this->paginator as $item): ?>
                            <li>
                                <div class="sitecrowdfunding_package_list_title">
                                    <div class="sitecrowdfunding_package_link">
                                        <?php if (!empty($this->parent_id) && !empty($this->parent_type)): ?>
                                            <?php $url = $this->url(array("action" => "create", 'id' => $item->package_id, 'parent_id' => $this->parent_id, 'parent_type' => $this->parent_type), 'sitecrowdfunding_project_general', true); ?> 
                                            <a class="common_btn" href='<?php echo $url; ?>' ><?php echo $this->translate("Create a Project"); ?></a>
                                        <?php else: ?>
                                            <?php $url = $this->url(array("action" => "create", 'id' => $item->package_id), "sitecrowdfunding_project_general", true); ?>
                                            <a class="common_btn " href='<?php echo $url; ?>' ><?php echo $this->translate("Create a Project"); ?></a>
 
                                        <?php endif; ?>    
                                    </div>   
                                    <h3>        
                                        <a href='<?php echo $this->url(array("action" => "detail", 'id' => $item->package_id), "sitecrowdfunding_package", true) ?>' onclick="owner(this);
                                                                return false;" title="<?php echo $this->translate(ucfirst($item->title)) ?>"><?php echo $this->translate(ucfirst($item->title)); ?></a>
                                    </h3>
                                </div> 
                                <?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/package/_packageInfo.tpl'; ?>
                            </li> 
                        <?php endforeach; ?>
                        <br />
                        <div><?php echo $this->paginationControl($this->paginator); ?></div>
                    <?php else: ?>
                        <?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/package/_verticalPackageInfo.tpl'; ?>
                    <?php endif; ?>
                </ul> 
            <?php else: ?>
                <div class="tip">
                    <span>
                        <?php echo $this->translate("There are no packages yet.") ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>


<!--vertcal design code end-->
<script type="text/javascript" >
    function owner(thisobj) {
        var Obj_Url = thisobj.href;
        Smoothbox.open(Obj_Url);
    }
</script>
