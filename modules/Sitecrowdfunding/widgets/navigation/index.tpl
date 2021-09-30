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
<?php

$maxSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.navigationtabs', 7);
$this->max = $maxSetting;
if (Engine_Api::_()->seaocore()->isMobile()) {
    $max = $maxSetting;
    if ($maxSetting > 3)
        $max = 3;
    $this->max = $max;
}

$headding = "Projects";
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<div class="headline sitecrowdfunding_inner_menu">
    <h2>
        <?php echo $this->translate($headding); ?>
    </h2>
    <?php if (count($this->navigation)) { ?>
        <div class='tabs sitecrowdfunding_nav'>
            <ul class='navigation'>
                <?php $key = 0; ?>
                <?php foreach ($this->navigation as $nav): ?>
                    <?php $data_smoothboxValue = ''; ?>
                    <?php if ($key < $this->max): ?>
                        <li <?php
                        if ($nav->active): echo "class='active'";
                        endif;
                        ?>>
                            <?php if ($nav->action): ?>
                                <a class="<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array('action' => $nav->action), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
                            <?php else : ?>
                                <a class= "<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array(), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
                            <?php endif; ?>
                        </li>
                    <?php else: ?>
                        <?php break; ?>
                    <?php endif; ?>
                    <?php $key++ ?>
                <?php endforeach; ?>

                <?php if (count($this->navigation) > $this->max): ?>
                    <li class="tab_closed more_tab" onclick="moreTabSwitchSitecrowdfunding($(this));">
                        <div class="tab_pulldown_contents_wrapper">
                            <div class="tab_pulldown_contents">          
                                <ul>
                                    <?php $key = 0; ?>
                                    <?php foreach ($this->navigation as $nav): ?>
                                        <?php if ($key >= $this->max): ?>
                                            <li <?php
                                            if ($nav->active): echo "class='active'";
                                            endif;
                                            ?> >
                                                    <?php if ($nav->action): ?>
                                                    <a class= "<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array('action' => $nav->action), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
                                                <?php else : ?>
                                                    <a class= "<?php echo $nav->class ?>" href='<?php echo empty($nav->uri) ? $this->url(array(), $nav->route, true) : $nav->uri ?>'><?php echo $this->translate($nav->label); ?></a>
                                                <?php endif; ?>
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
        </div>
        <a class="custom_button_nav_custom" href='<?php echo $this->url(array('controller' => 'project-create', 'action' => 'step-zero'), 'sitecrowdfunding_create', true) ?>'><?php echo $this->translate('Create A Project'); ?></a>
    <?php } ?>
</div>
<script type="text/javascript">
    en4.core.runonce.add(function () {

        var moreTabSwitchSitecrowdfunding = window.moreTabSwitchSitecrowdfunding = function (el) {
            el.toggleClass('seaocore_tab_open active');
            el.toggleClass('tab_closed');
        }
    });
</script>
<style>
    .custom_button_nav_custom{
        float: right;
        padding: 5px 14px;
        font-size: 14px;
        background-color: #44AEC1;
        color: #ffffff !important;
        border: 2px solid #44AEC1;
        cursor: pointer;
        outline: none;
        position: relative;
        overflow: hidden;
        -webkit-transition: all 500ms ease 0s;
        -moz-transition: all 500ms ease 0s;
        -o-transition: all 500ms ease 0s;
        transition: all 500ms ease 0s;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        border-radius: 3px;
        -webkit-box-sizing: border-box;
        -mox-box-sizing: border-box;
        box-sizing: border-box;
    }
    @media screen and (max-width: 767px) {
        .custom_button_nav_custom{
            margin-top: 10px;
            margin-bottom: 10px;
        }
    }
</style>