<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _verticalPackageInfo.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooHorizontalScrollBar.js'); ?>
<?php
$request = Zend_Controller_Front::getInstance()->getRequest();
$controller = $request->getControllerName();
$action = $request->getActionName();
?>
<?php if (!empty($this->viewer->level_id)): ?>
    <?php $level_id = $this->viewer->level_id; ?>
<?php else: ?>
    <?php $level_id = 0; ?>
<?php endif; ?>

<li class="seaocore_package_vertical sitecrowdfunding_packages_vertical">
    <div class="fleft sitecrowdfunding_packages_vertical_left"> 
        <?php if (in_array('price', $this->packageInfoArray)): ?>
            <div class="sitecrowdfunding_packages_vertical_left_text highlightleft"><b><?php echo $this->translate('Price'); ?></b></div>
        <?php endif; ?>
        <?php if (in_array('billing_cycle', $this->packageInfoArray)): ?>
            <div class="sitecrowdfunding_packages_vertical_left_text"><b><?php echo $this->translate('Billing Cycle'); ?></b></div>
        <?php endif; ?>
        <?php if (in_array('duration', $this->packageInfoArray)): ?>
            <div class="sitecrowdfunding_packages_vertical_left_text"><b><?php echo $this->translate("Duration") . " "; ?></b></div>
        <?php endif; ?>
        <?php if (in_array('featured', $this->packageInfoArray)): ?>
            <div class="sitecrowdfunding_packages_vertical_left_text"><b><?php echo $this->translate('Featured'); ?></b></div>
        <?php endif; ?>
        <?php if (in_array('sponsored', $this->packageInfoArray)): ?>
            <div class="sitecrowdfunding_packages_vertical_left_text"><b><?php echo $this->translate('Sponsored'); ?></b></div>
        <?php endif; ?>
        <?php if (in_array('rich_overview', $this->packageInfoArray) && ($this->overview && (!empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'Sitecrowdfunding_project', "overview")))): ?>
            <div class="sitecrowdfunding_packages_vertical_left_text"><b><?php echo $this->translate('Rich Overview'); ?></b></div>
        <?php endif; ?>        
        <?php if (in_array('videos', $this->packageInfoArray)): ?>
            <div class="sitecrowdfunding_packages_vertical_left_text"><b><?php echo $this->translate('Videos'); ?></b></div>
        <?php endif; ?>
        <?php if (in_array('photos', $this->packageInfoArray)): ?>
            <div class="sitecrowdfunding_packages_vertical_left_text"><b><?php echo $this->translate('Photos'); ?></b></div>
        <?php endif; ?>
        <?php if (in_array('commission', $this->packageInfoArray) && (!empty($level_id))): ?>
            <div class="sitecrowdfunding_packages_vertical_left_text"><b><?php echo $this->translate('Commission'); ?><img class="mleft5" style="margin-bottom: -3px;" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/help.png" title="<?php echo $this->translate("Commission charged on the amount backed for a Project of this package."); ?>" >	</b></div>
        <?php endif; ?>
        <?php if (in_array('description', $this->packageInfoArray)): ?>
            <div class="sitecrowdfunding_packages_vertical_left_text">
                <b><?php echo $this->translate("Description"); ?> </b>
            </div>
        <?php endif; ?> 
    </div>
    <div class="paidProjects scroll-pane" id="sitecrowdfunding_packages_panel" style="overflow-x: hidden; overflow-y: hidden; ">
        <div class="dnone" id ="scrollbar_before"></div>
        <div id="scroll-areas-main" >
            <div id="list-scroll-areas" style=" float:left;overflow:hidden;"> 
                <div class="scroll-content" id="scroll-content" style="margin-left: 0px;width:100%; display:table;">
                    <?php foreach ($this->paginator as $item): ?>
                        <div class="sitecrowdfunding_packages_vertical_right">
                            <div class="sitecrowdfunding_packages_vertical_right_heading o_hidden">
                                <b><a href='<?php echo $this->url(array("action" => "detail", 'id' => $item->package_id), "sitecrowdfunding_package", true) ?>' onclick="owner(this);
                                            return false;" title="<?php echo $this->translate(ucfirst($item->title)) ?>"><?php echo $this->translate(ucfirst($item->title)); ?></a></b>
                            </div>
                            <div class="change_package_column">
                                <div class="change_package_btn">
                                    <?php if ($controller == 'package' && $action == 'update-package'): ?>
                                        <?php
                                        echo $this->htmlLink(
                                                array('route' => "sitecrowdfunding_package", 'action' => 'update-confirmation', "project_id" => $this->project_id, "package_id" => $item->package_id), $this->translate('Change Package'), array('onclick' => 'owner(this);return false', 'class' => 'sitecrowdfunding_buttonlink', 'title' => $this->translate('Change Package')));
                                        ?>
                                    <?php else: ?>
                                        <?php if (!empty($this->parent_id) && !empty($this->parent_type)): ?>
                                            <?php $url = $this->url(array("action" => "create", 'id' => $item->package_id, 'parent_id' => $this->parent_id, 'parent_type' => $this->parent_type), 'sitecrowdfunding_general', true); ?>
                                            <a class="sitecrowdfunding_buttonlink" href='<?php echo $url; ?>' ><?php echo $this->translate("Create a Project"); ?> &raquo;</a>
                                        <?php else: ?>
                                            <?php $url = $this->url(array("action" => "create", 'id' => $item->package_id), "sitecrowdfunding_general", true); ?>
                                            <a class="sitecrowdfunding_buttonlink" href='<?php echo $url; ?>' ><?php echo $this->translate("Create a Project"); ?> &raquo;</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if (in_array('price', $this->packageInfoArray)): ?>
                                <div class="sitecrowdfunding_packages_vertical_right_text"><b><?php
                                        if ($item->price > 0):echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($item->price);
                                        else: echo $this->translate('FREE');
                                        endif;
                                        ?></b> </div>
                            <?php endif; ?>
                            <?php if (in_array('billing_cycle', $this->packageInfoArray)): ?>
                                <div class="sitecrowdfunding_packages_vertical_right_text"><?php echo $item->getBillingCycle() ?></div>
                            <?php endif; ?>
                            <?php if (in_array('duration', $this->packageInfoArray)): ?>
                                <div class="sitecrowdfunding_packages_vertical_right_text"><?php echo $item->getPackageQuantity(); ?></div>
                            <?php endif; ?>
                            <?php if (in_array('featured', $this->packageInfoArray)): ?>
                                <div class="sitecrowdfunding_packages_vertical_right_text">     
                                    <?php if ($item->featured == 1): ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/tick.png">
                                    <?php else: ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/cross.png">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (in_array('sponsored', $this->packageInfoArray)): ?>
                                <div class="sitecrowdfunding_packages_vertical_right_text">     
                                    <?php if ($item->sponsored == 1): ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/tick.png">
                                    <?php else: ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/cross.png">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (in_array('rich_overview', $this->packageInfoArray) && ($this->overview && (!empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'Sitecrowdfunding_project', "overview")))): ?>
                                <div class="sitecrowdfunding_packages_vertical_right_text">     
                                    <?php if ($item->overview == 1): ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/tick.png">
                                    <?php else: ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/cross.png">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (in_array('videos', $this->packageInfoArray) && (!empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'Sitecrowdfunding_project', "video"))): ?>
                                <div class="sitecrowdfunding_packages_vertical_right_text">     
                                    <?php if ($item->video == 1): ?>
                                        <?php if ($item->video_count): ?>
                                            <?php echo $item->video_count; ?>
                                        <?php else: ?>
                                            <?php echo $this->translate("Unlimited"); ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/cross.png">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (in_array('photos', $this->packageInfoArray) && (!empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'Sitecrowdfunding_project', "photo"))): ?>
                                <div class="sitecrowdfunding_packages_vertical_right_text">     
                                    <?php if ($item->photo == 1): ?>
                                        <?php if ($item->photo_count): ?>
                                            <?php echo $item->photo_count; ?>
                                        <?php else: ?>
                                            <?php echo $this->translate("Unlimited"); ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/cross.png">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (in_array('commission', $this->packageInfoArray)): ?>
                                <?php
                                if (!empty($item->commission_settings)):
                                    $commissionInfo = @unserialize($item->commission_settings);
                                    $commissionType = $commissionInfo['commission_handling'];
                                    $commissionFee = $commissionInfo['commission_fee'];
                                    $commissionRate = $commissionInfo['commission_rate'];
                                endif;
                                ?> 
                                <div class="sitecrowdfunding_packages_vertical_right_text">
                                    <?php if (!empty($item->commission_settings) && isset($commissionType)): ?>                
                                        <?php
                                        if (empty($commissionType)):
                                            echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency((int) $commissionFee);
                                        else:
                                            echo $commissionRate . '%';
                                        endif;
                                        ?>
                                    <?php else: ?>
                                        <?php echo $this->translate("N/A"); ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (in_array('description', $this->packageInfoArray)): ?>
                                <div class="sitecrowdfunding_packages_vertical_right_text sitecrowdfunding_packages_vertical_right_description">
                                    <?php echo $this->translate($item->description); ?>
                                </div>
                            <?php endif; ?> 
                            <div class="change_package_column">
                                <div class="change_package_btn">
                                    <?php if ($controller == 'package' && $action == 'update-package'): ?>
                                        <?php
                                        echo $this->htmlLink(
                                                array('route' => "sitecrowdfunding_package", 'action' => 'update-confirmation', "project_id" => $this->project_id, "package_id" => $item->package_id), $this->translate('Change Package'), array('onclick' => 'owner(this);return false', 'class' => '', 'title' => $this->translate('Change Package')));
                                        ?>
                                    <?php else: ?>
                                        <?php if (!empty($this->parent_id) && !empty($this->parent_type)): ?>
                                            <?php $url = $this->url(array("action" => "create", 'id' => $item->package_id, 'parent_id' => $this->parent_id, 'parent_type' => $this->parent_type), 'sitecrowdfunding_general', true); ?>
                                            <a class="sitecrowdfunding_buttonlink" href='<?php echo $url; ?>' ><?php echo $this->translate("Create a Project"); ?> &raquo;</a>
                                        <?php else: ?>
                                            <?php $url = $this->url(array("action" => "create", 'id' => $item->package_id), "sitecrowdfunding_general", true); ?>
                                            <a class="sitecrowdfunding_buttonlink" href='<?php echo $url; ?>' ><?php echo $this->translate("Create a Project"); ?> &raquo;</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="scrollbarArea" id ="scrollbar_after">		</div>
    </div>
</li>

<script type="text/javascript" >

    var totalLsit = <?php echo $this->paginator->getTotalItemCount(); ?>;
    en4.core.runonce.add(function () {
        resetContent();
        (function () {
            $('list-scroll-areas').setStyle('height', $('scroll-content').offsetHeight + 'px');
            $('list-scroll-areas').setStyle('width', $('sitecrowdfunding_packages_panel').offsetWidth + 'px');
            scrollBarContentArea = new SEAOMooHorizontalScrollBar('scroll-areas-main', 'list-scroll-areas', {
                'arrows': false,
                'horizontalScroll': true,
                'horizontalScrollElement': 'scrollbar_after',
                'horizontalScrollBefore': true,
                'horizontalScrollBeforeElement': 'scrollbar_before'
            });
            if ($('scrollbar_after') && ($('scrollbar_after').getChildren().length == 0 || $('scrollbar_after').getChildren().length > 0 && $('scrollbar_after').getChildren()[0].style.display == 'none')) {
                $('scrollbar_after').hide();
            } 
        }).delay(700);
    });

    var resetContent = function () {
        var width = ($('sitecrowdfunding_packages_panel').offsetWidth / totalLsit);
        width = width - 2;
        if (width < 200)
            width = 200;
        width++;
        var numberOfItem = ($('sitecrowdfunding_packages_panel').offsetWidth / width);
        var numberOfItemFloor = Math.floor(numberOfItem);
        var extra = (width * (numberOfItem - numberOfItemFloor) / numberOfItemFloor);
        width = width + extra;
        $('scroll-content').setStyle('width', (width * totalLsit) + 'px');
        $('scroll-content').getElements('.sitecrowdfunding_packages_vertical_right').each(function (el) {
            el.setStyle('width', width - 1 + 'px');

        });
    };
</script>