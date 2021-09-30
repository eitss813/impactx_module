<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: navigation_views.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>

<script type="text/javascript">
    en4.core.runonce.add(function () {

        var moreTabSwitchNavigation = window.moreTabSwitchNavigation = function (el) {
            el.toggleClass('seaocore_tab_open active');
            el.toggleClass('tab_closed');
        }
    });
</script>

<div class="headline">
    <h2>
        <?php if (!empty($this->navigationTabTitle)) : ?>
            <?php echo $this->translate($this->navigationTabTitle); ?>
        <?php else: ?>
            <?php echo $this->translate("Projects"); ?>
        <?php endif; ?>
    </h2>
    <div class="tabs">

        <?php if (count($this->navigation)): ?>
            <?php $this->max = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.navigationtabs', 6); ?>
            <?php
            if (count($this->navigation) == ($this->max + 1)): $this->max++;
            endif;
            ?>
            <ul class='navigation sitecrowdfunding_navigation_common'>
                <?php $key = 0; ?>
                <?php foreach ($this->navigation as $item): ?>			
                    <?php
                    if ($key < $this->max)
                        :
                        ?>
                        <li <?php if ($item->isActive()) : ?> class="active" <?php endif; ?>>
                            <a href="<?php echo $item->getHref(); ?>" class="<?php echo $item->getClass() ?>" <?php if ($item->target): ?> target="_blank" <?php endif; ?> >
                                <?php echo $this->translate($item->getLabel()); ?>
                            </a>
                        </li>
                    <?php else: ?>
                        <?php break; ?>
                    <?php endif; ?>
                    <?php $key++ ?>
                <?php endforeach;  ?>

                <?php if (count($this->navigation) > $this->max): ?>
                    <li class="tab_closed more_tab" onclick="moreTabSwitchNavigation($(this));">
                        <div class="tab_pulldown_contents_wrapper">
                            <div class="tab_pulldown_contents">          
                                <ul>
                                    <?php $key = 0; ?>
                                    <?php foreach ($this->navigation as $item): ?>
                                        <?php if ($key >= $this->max): ?>
                                            <li <?php if ($item->isActive()) : ?> class="active" <?php endif; ?>>
                                                <?php $name = trim(str_replace('menu_core_main ', '', $item->getClass())); ?>
                                                <a href="<?php echo $item->getHref(); ?>" class="<?php echo $item->getClass() ?>" <?php if ($item->target): ?> target="_blank" <?php endif; ?> >
                                                    <?php echo $this->translate($item->getLabel()); ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php $key++ ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <a href="javascript:void(0);"><?php echo $this->translate('More +') ?><span></span></a>
                    </li>
                <?php endif; ?>
            </ul>

        <?php endif; ?>
    </div>
</div>