<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _verticalPackageInfo.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
$request = Zend_Controller_Front::getInstance()->getRequest();
$controller = $request->getControllerName();
$action = $request->getActionName();
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooHorizontalScrollBar.js'); ?>
<li class="seaocore_package_vertical">
    <div class="fleft">
        <?php if (in_array('price', $packageInfoArray)): ?>
            <div class="contentblock_left_text highlightleft"><b><?php echo $this->translate("Price"); ?></b></div>
        <?php endif; ?>
        <?php if (in_array('billing_cycle', $packageInfoArray)): ?>
            <div class="contentblock_left_text"><b><?php echo $this->translate("Billing Cycle"); ?></b></div>
        <?php endif; ?>
        <?php if (in_array('duration', $packageInfoArray)): ?>
            <div class="contentblock_left_text"><b><?php echo $this->translate("Duration"); ?></b></div>
        <?php endif; ?>
        <?php if (in_array('featured', $packageInfoArray)): ?>
            <div class="contentblock_left_text"><b><?php echo $this->translate("Featured"); ?></b></div>
        <?php endif; ?>
        <?php if (in_array('sponsored', $packageInfoArray)): ?>
            <div class="contentblock_left_text"><b><?php echo $this->translate("Sponsored"); ?></b></div>
        <?php endif; ?>
        <?php if (in_array('ads', $packageInfoArray)): ?>
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')): ?>
                <div class="contentblock_left_text"><b><?php echo $this->translate("Ads Display"); ?></b></div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (in_array('tellafriend', $packageInfoArray)): ?>
            <div class="contentblock_left_text"><b><?php echo $this->translate("Tell a friend"); ?></b></div><?php endif; ?>
        <?php if (in_array('print', $packageInfoArray)): ?>
            <div class="contentblock_left_text"><b><?php echo $this->translate("Print"); ?></b></div>
        <?php endif; ?>
        <?php if (in_array('overview', $packageInfoArray)): ?>
            <div class="contentblock_left_text"><b><?php echo $this->translate("Rich Overview"); ?></b></div>
        <?php endif; ?>
        <?php if (in_array('map', $packageInfoArray)): ?>
            <div class="contentblock_left_text"><b><?php echo $this->translate("Map"); ?></b></div>
        <?php endif; ?>
        <?php if (in_array('insights', $packageInfoArray)): ?>
            <div class="contentblock_left_text"><b><?php echo $this->translate("Insights"); ?></b></div>
        <?php endif; ?>
        <?php if (in_array('contactdetails', $packageInfoArray)): ?>
            <div class="contentblock_left_text"><b><?php echo $this->translate("Contact Details"); ?></b></div>
        <?php endif; ?>
        <?php if (in_array('sendanupdate', $packageInfoArray)): ?>
            <div class="contentblock_left_text"><b><?php echo $this->translate("Send an Update"); ?></b></div>
        <?php endif; ?>
        <?php if (false && in_array('foursquarebutton', $packageInfoArray)): ?>
            <div class="contentblock_left_text"><b><?php echo $this->translate("Foursquare Button"); ?></b></div>
        <?php endif; ?>
        <?php if (in_array('twitterupdates', $packageInfoArray)): ?>
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter')) : ?>
                <div class="contentblock_left_text"><b><?php echo $this->translate("Display Twitter Updates"); ?></b></div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (in_array('apps', $packageInfoArray)): ?>
            <div class="contentblock_left_text"><b><?php echo $this->translate("Apps available"); ?></b></div>
        <?php endif; ?>
        <?php if (in_array('description', $packageInfoArray)): ?>
            <div class="contentblock_left_text"><b><?php echo $this->translate("Description"); ?></b></div>
        <?php endif; ?>
        <div class="contentblock_left_text"><b><?php echo $this->translate("Package"); ?></b></div>
        <?php if (in_array('price', $packageInfoArray)): ?>
            <div class="contentblock_left_text highlightleft"><b><?php echo $this->translate("Price"); ?></b></div>
        <?php endif; ?>
    </div>
    <div class="paidEvents scroll-pane" id="paidSeaocorePanel" style="overflow-x: hidden; overflow-y: hidden; ">
        <div class=" " id ="scrollbar_before"></div>
        <div id="scroll-areas-main" >
            <div id="list-scroll-areas" style=" float:left;overflow:hidden;"> 
                <div class="scroll-content" id="scroll-content" style="margin-left: 0px;width:100%; display:table;">
                    <?php foreach ($this->paginator as $item): ?>
                        <div class="contentblock_right_inner">
                            <div class="contentblock_right_inner_heading o_hidden">
                                <a href='<?php echo $this->url(array("action" => "detail", 'id' => $item->package_id), 'sitepage_packages', true) ?>' onclick="owner(this);
                                        return false;" title="<?php echo $this->translate(ucfirst($item->title)) ?>"><?php echo $this->translate(ucfirst($item->title)); ?></a>
                            </div>
                            <div class="contentblock_right_text">
                                <div class="contentblock_right_inner_btn">
                                    <?php if ($controller == 'package' && $action == 'update-package'): ?>
                                        <?php
                                        echo $this->htmlLink(
                                                array('route' => 'sitepage_packages', 'action' => 'update-confirmation', "page_id" => $this->page_id, "package_id" => $item->package_id), $this->translate('Change Package'), array('onclick' => 'owner(this);return false', 'class' => 'siteevent_buttonlink', 'title' => $this->translate('Change Package')));
                                        ?>
                                    <?php else: ?>
                                        <?php if (!empty($this->parent_id)): ?>
                                            <?php
                                            $url = $this->url(array("action" => "create", 'id' => $item->package_id, 'parent_id' => $this->parent_id), 'sitepage_general', true);
                                            ?>
                                            <a class="seaocore_buttonlink" href='<?php echo $url; ?>' ><?php echo $this->translate('Create a Page'); ?> &raquo;</a>
                                        <?php elseif (!empty($this->business_id)) : ?>
                                            <?php
                                            $url = $this->url(array("action" => "create", 'id' => $item->package_id, 'business_id' => $this->business_id), 'sitepage_general', true);
                                            ?>
                                            <a class="seaocore_buttonlink" href='<?php echo $url; ?>' ><?php echo $this->translate('Create a Page'); ?> &raquo;</a>
                                        <?php elseif (!empty($this->group_id)) : ?>
                                            <?php
                                            $url = $this->url(array("action" => "create", 'id' => $item->package_id, 'group_id' => $this->group_id), 'sitepage_general', true);
                                            ?>
                                            <a class="seaocore_buttonlink" href='<?php echo $url; ?>' ><?php echo $this->translate('Create a Page'); ?> &raquo;</a>
                                        <?php elseif (!empty($this->store_id)) : ?>
                                            <?php
                                            $url = $this->url(array("action" => "create", 'id' => $item->package_id, 'store_id' => $this->store_id), 'sitepage_general', true);
                                            ?>
                                            <a class="seaocore_buttonlink" href='<?php echo $url; ?>' ><?php echo $this->translate('Create a Page'); ?> &raquo;</a>
                                        <?php else: ?>
                                            <?php
                                            $url = $this->url(array("action" => "create", 'id' => $item->package_id), 'sitepage_general', true);
                                            ?>
                                            <a class="seaocore_buttonlink" href='<?php echo $url; ?>' ><?php echo $this->translate('Create a Page'); ?> &raquo;</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (in_array('price', $packageInfoArray)): ?>
                                <div class="contentblock_right_text highlightright"><b><?php
                                        if ($item->price > 0):echo Engine_Api::_()->sitepage()->getPriceWithCurrency($item->price);
                                        else: echo $this->translate('FREE');
                                        endif;
                                        ?></b> 
                                </div>
                            <?php endif; ?>
                            <?php if (in_array('billing_cycle', $packageInfoArray)): ?>
                                <div class="contentblock_right_text"><?php echo $item->getBillingCycle() ?></div>
                            <?php endif; ?>
                            <?php if (in_array('duration', $packageInfoArray)): ?>
                                <div class="contentblock_right_text"><?php echo $item->getPackageQuantity(); ?></div>
                            <?php endif; ?>
                            <?php if (in_array('featured', $packageInfoArray)): ?>
                                <div class="contentblock_right_text">     
                                    <?php if ($item->featured == 1): ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/tick.png">
                                    <?php else: ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/cross.png">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (in_array('sponsored', $packageInfoArray)): ?>
                                <div class="contentblock_right_text">     
                                    <?php if ($item->sponsored == 1): ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/tick.png">
                                    <?php else: ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/cross.png">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (in_array('ads', $packageInfoArray)): ?>    
                                <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')): ?>
                                    <div class="contentblock_right_text">
                                        <?php if ($item->ads == 1 && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1)): ?>
                                            <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/tick.png">
                                        <?php else: ?>
                                            <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/cross.png">
                                        <?php endif; ?>

                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if (in_array('tellafriend', $packageInfoArray)): ?>     
                                <div class="contentblock_right_text">     
                                    <?php if ($item->tellafriend == 1): ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/tick.png">
                                    <?php else: ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/cross.png">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (in_array('print', $packageInfoArray)): ?>     
                                <div class="contentblock_right_text">     
                                    <?php if ($item->print == 1): ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/tick.png">
                                    <?php else: ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/cross.png">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (in_array('overview', $packageInfoArray)): ?>          
                                <div class="contentblock_right_text">     
                                    <?php if ($item->overview == 1): ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/tick.png">
                                    <?php else: ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/cross.png">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (in_array('map', $packageInfoArray)): ?>     
                                <div class="contentblock_right_text">     
                                    <?php if ($item->map == 1): ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/tick.png">
                                    <?php else: ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/cross.png">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (in_array('insights', $packageInfoArray)): ?>
                                <div class="contentblock_right_text">     
                                    <?php if ($item->insights == 1): ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/tick.png">
                                    <?php else: ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/cross.png">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (in_array('contactdetails', $packageInfoArray)): ?>    
                                <div class="contentblock_right_text">     
                                    <?php if ($item->contact_details == 1): ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/tick.png">
                                    <?php else: ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/cross.png">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (in_array('sendanupdate', $packageInfoArray)): ?>    
                                <div class="contentblock_right_text">     
                                    <?php if ($item->sendupdate == 1): ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/tick.png">
                                    <?php else: ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/cross.png">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (false && in_array('foursquarebutton', $packageInfoArray)): ?>       
                                <div class="contentblock_right_text">     
                                    <?php if ($item->foursquare == 1): ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/tick.png">
                                    <?php else: ?>
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/cross.png">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (in_array('twitterupdates', $packageInfoArray)): ?>      
                                <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter')) : ?>
                                    <div class="contentblock_right_text">     
                                        <?php if ($item->twitter == 1): ?>
                                            <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/tick.png">
                                        <?php else: ?>
                                            <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/cross.png">
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>     

                            <?php if (in_array('apps', $packageInfoArray)): ?>      
                                <?php
                                $module = unserialize($item->modules);
                                if (!empty($module)):
                                    $subModuleStr = $item->getSubModulesString();
                                    if (!empty($item->modules) && !empty($subModuleStr)):
                                        ?>
                                        <div class="contentblock_right_text" style="overflow: auto;">     
                                            <?php echo $subModuleStr; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="contentblock_right_text">   
                                        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/cross.png">
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>     

                            <?php if (in_array('description', $packageInfoArray)): ?>      
                                <div class="contentblock_right_text contentblock_description">
                                    <?php if (empty($this->detailPackage)): ?>
                                        <?php echo $this->viewMore($this->translate($item->description), 425); ?>
                                    <?php else: ?>
                                        <?php echo $this->translate($item->description); ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (in_array('price', $packageInfoArray)): ?>    
                                <div class="contentblock_right_text highlightright">
                                    <b>
                                        <?php
                                        if ($item->price > 0):
                                            echo Engine_Api::_()->sitepage()->getPriceWithCurrency($item->price); 
                                        else:
                                            echo $this->translate('FREE');
                                        endif;
                                        ?>
                                    </b> 
                                </div>
                            <?php endif; ?>

                            <div class="contentblock_right_text">
                                <div class="contentblock_right_inner_btn">
                                    <?php if ($controller == 'package' && $action == 'update-package'): ?>
                                        <?php
                                        echo $this->htmlLink(
                                                array('route' => 'sitepage_packages', 'action' => 'update-confirmation', "page_id" => $this->page_id, "package_id" => $item->package_id), $this->translate('Change Package'), array('onclick' => 'owner(this);return false', 'class' => 'siteevent_buttonlink', 'title' => $this->translate('Change Package')));
                                        ?>
                                    <?php else: ?>
                                        <?php if (!empty($this->parent_id)): ?>
                                            <?php
                                            $url = $this->url(array("action" => "create", 'id' => $item->package_id, 'parent_id' => $this->parent_id), 'sitepage_general', true);
                                            ?>
                                            <a class="seaocore_buttonlink" href='<?php echo $url; ?>' ><?php echo $this->translate('Create a Page'); ?> &raquo;</a>
                                        <?php elseif (!empty($this->business_id)) : ?>
                                            <?php
                                            $url = $this->url(array("action" => "create", 'id' => $item->package_id, 'business_id' => $this->business_id), 'sitepage_general', true);
                                            ?>
                                            <a class="seaocore_buttonlink" href='<?php echo $url; ?>' ><?php echo $this->translate('Create a Page'); ?> &raquo;</a>
                                        <?php elseif (!empty($this->group_id)) : ?>
                                            <?php
                                            $url = $this->url(array("action" => "create", 'id' => $item->package_id, 'group_id' => $this->group_id), 'sitepage_general', true);
                                            ?>
                                            <a class="seaocore_buttonlink" href='<?php echo $url; ?>' ><?php echo $this->translate('Create a Page'); ?> &raquo;</a>
                                        <?php elseif (!empty($this->store_id)) : ?>
                                            <?php
                                            $url = $this->url(array("action" => "create", 'id' => $item->package_id, 'store_id' => $this->store_id), 'sitepage_general', true);
                                            ?>
                                            <a class="seaocore_buttonlink" href='<?php echo $url; ?>' ><?php echo $this->translate('Create a Page'); ?> &raquo;</a>
                                        <?php else: ?>
                                            <?php
                                            $url = $this->url(array("action" => "create", 'id' => $item->package_id), 'sitepage_general', true);
                                            ?>
                                            <a class="seaocore_buttonlink" href='<?php echo $url; ?>' ><?php echo $this->translate('Create a Page'); ?> &raquo;</a>
                                        <?php endif; ?>  
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="scrollbarArea" id ="scrollbar_after"></div>
    </div>
</li>

<script type="text/javascript" >

    var totalLsit = <?php echo $this->paginator->getTotalItemCount(); ?>;
    en4.core.runonce.add(function () { 
        
            //var check = typeof SEAOMooHorizontalScrollBar;
            
            //if(check === "undefined") { 
             //   Asset.javascript('<?php echo ($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooHorizontalScrollBar.js");?>');    
           // }
    
        resetContent();
        (function () {             
            $('list-scroll-areas').setStyle('height', $('scroll-content').offsetHeight + 'px');
            $('list-scroll-areas').setStyle('width', $('paidSeaocorePanel').offsetWidth + 'px');
            scrollBarContentArea = new SEAOMooHorizontalScrollBar('scroll-areas-main', 'list-scroll-areas', {
                'arrows': false,
                'horizontalScroll': true,
                'horizontalScrollElement': 'scrollbar_after',
                'horizontalScrollBefore': true,
                'horizontalScrollBeforeElement': 'scrollbar_before'
            });
        }).delay(700);
    });

    var resetContent = function () {
        var width = ($('paidSeaocorePanel').offsetWidth / totalLsit);
        width = width - 2;
        if (width < 200)
            width = 200;
        width++;
        var numberOfItem = ($('paidSeaocorePanel').offsetWidth / width);
        var numberOfItemFloor = Math.floor(numberOfItem);
        var extra = (width * (numberOfItem - numberOfItemFloor) / numberOfItemFloor);
        width = width + extra;
        $('scroll-content').setStyle('width', (width * totalLsit) + 'px');
        $('scroll-content').getElements('.contentblock_right_inner').each(function (el) {
            el.setStyle('width', width - 1.5 + 'px');

        });
    };
</script>