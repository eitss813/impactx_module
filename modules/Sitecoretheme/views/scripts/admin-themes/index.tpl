<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<h2><?php echo $this->translate(SITECORETHEME_PLUGIN_NAME) ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<div class='seaocore_sub_tabs tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->subNavigation)->render() ?>
</div>

<br />

<script type="text/javascript">
    var modifications = [];
    window.onbeforeunload = function() {
        if( modifications.length > 0 ) {
            return '<?php echo $this->translate("If you leave the page now, your changes will be lost. Are you sure you want to continue?") ?>';
        }
    }
    var pushModification = function(type) {
        modifications.push(type);
    }
    var removeModification = function(type) {
        modifications.erase(type);
    }
    var changeThemeFile = function(file) {
        var url = '<?php echo $this->url() ?>?file=' + file;
        window.location.href = url;
    }
</script>


<div class="admin_theme_editor_wrapper">
    <form method="post">
        <div class="admin_theme_edit">

            <div class="admin_theme_header_controls">
                <h3>
                    <?php echo $this->translate('Active Color Scheme') ?>
                </h3>
                <div>
                    <?php echo $this->htmlLink(array('route'=>'admin_default', 'module' => 'sitecoretheme' , 'controller'=>'themes', 'action'=>'clone', 'name'=>$this->activeTheme->name),
                        $this->translate('New Custom Theme'), array(
                            'class' => 'buttonlink admin_themes_header_clone',
                        )) ?> 
                </div>
            </div>

            <div class="admin_theme_editor_edit_wrapper">
                <div class="admin_theme_editor_selected">
                    <?php foreach ($this->themes as $theme):?>
                        <?php
                        // @todo meta key is deprecated and pending removal in 4.1.0; b/c removal in 4.2.0
                        $thumb = $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/anonymous.png';
                        if (!empty($this->manifest[$theme->name]['package']['thumb'])) {
                            $thumb = $this->manifest[$theme->name]['package']['thumb'];
                        }
                        if ($theme->name === $this->activeTheme->name): ?>
                            <div class="theme_wrapper_selected"><img src="<?php echo $thumb ?>" alt="<?php echo $theme->name?>"></div>
                            <div class="theme_selected_info">
                                <?php $themeTitle = $theme->title; ?>
                                <?php if($theme->type == 2): ?>
                                <?php $themeTitle = $theme->title; ?>
                                <?php endif; ?>
                                <h3><?php echo $themeTitle; ?></h3>
                                <?php if (!empty($this->manifest[$theme->name]['package']['author'])): ?>
                                    <h4><?php echo $this->translate('by %s', $this->manifest[$theme->name]['package']['author']) ?></h4>
                                <?php endif; ?>
                            </div>
                            <?php break; endif; ?>
                    <?php endforeach; ?>
                </div>
            </div> 
        </div>
    </form>


    <div class="admin_theme_chooser sitecoretheme_theme_chooser">
        <?php if(count($this->customThemes) > 0): ?>
        <div class="admin_theme_editor_chooser_wrapper_custom">
            <div class="admin_theme_header_controls">
                <h3><?php echo $this->translate("Customized Themes") ?></h3>
            </div>
            <ul class="admin_themes">
                <?php
                // @todo meta key is deprecated and pending removal in 4.1.0; b/c removal in 4.2.0
                $alt_row = true;
                foreach ($this->customThemes as $theme):
                    $thumb = $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/anonymous.png';
                    if (!empty($this->manifest[$theme->name]['package']['thumb'])) {
                        $thumb = $this->manifest[$theme->name]['package']['thumb'];
                    }
                    ?>
                    <li <?php echo ($alt_row) ? ' class="alt_row"' : "";?>>
                        <div class="theme_wrapper">
                            <img src="<?php echo $thumb ?>" alt="<?php echo $theme->name?>">

                            <?php if ($theme->name !== $this->activeTheme->name):?>
                                <a href="<?php echo $this->url(array('module' => 'sitecoretheme', 'action' => 'delete', 'name' => $theme->name)); ?>" class="delete-theme smoothbox">
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="theme_chooser_info">
                            <h3><?php echo $theme->title?></h3>
                            <?php if (!empty($this->manifest[$theme->name]['package']['author'])): ?>
                                <h4><?php echo $this->translate('by %s', $this->manifest[$theme->name]['package']['author']) ?></h4>
                            <?php endif; ?>
                            <?php if ($theme->name !== $this->activeTheme->name):?>
                                <form action="<?php echo $this->url(array('action' => 'change')) ?>" method="post">
                                    <button type="submit" class="activate_button"><?php echo $this->translate('Activate Theme') ?></button>
                                    <?php echo $this->formHidden('theme', $theme->name, array('id'=>'')) ?>
                                </form>
                            <?php else:?>
                                <div class="current_theme">
                                    (<?php echo $this->translate("This is your current theme") ?>)
                                </div>
                            <?php endif;?>
                            <?php $updateColorUrl =  $this->url(array('module' => 'sitecoretheme', 'controller' => 'themes', 'action' => 'update-colors', 'name' => $theme->name)) ?>
                            <button onclick= "window.location.href = '<?php echo $updateColorUrl; ?>'" class="update_button"><?php echo $this->translate('Update Colors') ?></button>
                        </div>
                    </li>
                    <?php $alt_row = !$alt_row; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif;?>
        <?php if(count($this->defaultDoubleColorThemes) > 0): ?>
        <div class="admin_theme_editor_chooser_wrapper_light_default">
            <div class="admin_theme_header_controls">
                <h3> <?php echo $this->translate("Default Theme With Double Colors") ?> </h3>
            </div>
            <ul class="admin_themes">
                <?php 
                // @todo meta key is deprecated and pending removal in 4.1.0; b/c removal in 4.2.0
                $alt_row = true;
                foreach ($this->defaultDoubleColorThemes as $theme):
                    $thumb = $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/anonymous.png';
                    if (!empty($this->manifest[$theme->name]['package']['thumb'])) {
                        $thumb = $this->manifest[$theme->name]['package']['thumb'];
                    }
                    ?>
                    <li <?php echo ($alt_row) ? ' class="alt_row"' : "";?>>
                        <div class="theme_wrapper">
                            <img src="<?php echo $thumb ?>" alt="<?php echo $theme->name?>">
                        </div>
                        <div class="theme_chooser_info">
                            <h3><?php echo $theme->title?></h3>
                            <?php if (!empty($this->manifest[$theme->name]['package']['author'])): ?>
                                <h4><?php echo $this->translate('by %s', $this->manifest[$theme->name]['package']['author']) ?></h4>
                            <?php endif; ?>
                            <?php if ($theme->name !== $this->activeTheme->name):?>
                                <form action="<?php echo $this->url(array('action' => 'change')) ?>" method="post">
                                    <button type="submit" class="activate_button"><?php echo $this->translate('Activate Theme') ?></button>
                                    <?php echo $this->formHidden('theme', $theme->name, array('id'=>'')) ?>
                                </form>
                            <?php else:?>
                                <div class="current_theme">
                                    (<?php echo $this->translate("This is your current theme") ?>)
                                </div>
                            <?php endif;?> 
                        </div>
                    </li>
                    <?php $alt_row = !$alt_row; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif;?>
        <?php if(count($this->defaultWhiteHeaderThemes) > 0): ?>
        <div class="admin_theme_editor_chooser_wrapper_light_default">
            <div class="admin_theme_header_controls">
                <h3> <?php echo $this->translate("Default Theme With White Header") ?> </h3>
            </div>
            <ul class="admin_themes">
                <?php 
                // @todo meta key is deprecated and pending removal in 4.1.0; b/c removal in 4.2.0
                $alt_row = true;
                foreach ($this->defaultWhiteHeaderThemes as $theme):
                    $thumb = $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/anonymous.png';
                    if (!empty($this->manifest[$theme->name]['package']['thumb'])) {
                        $thumb = $this->manifest[$theme->name]['package']['thumb'];
                    }
                    ?>
                    <li <?php echo ($alt_row) ? ' class="alt_row"' : "";?>>
                        <div class="theme_wrapper">
                            <img src="<?php echo $thumb ?>" alt="<?php echo $theme->name?>">
                        </div>
                        <div class="theme_chooser_info">
                            <h3><?php echo $theme->title?></h3>
                            <?php if (!empty($this->manifest[$theme->name]['package']['author'])): ?>
                                <h4><?php echo $this->translate('by %s', $this->manifest[$theme->name]['package']['author']) ?></h4>
                            <?php endif; ?>
                            <?php if ($theme->name !== $this->activeTheme->name):?>
                                <form action="<?php echo $this->url(array('action' => 'change')) ?>" method="post">
                                    <button type="submit" class="activate_button"><?php echo $this->translate('Activate Theme') ?></button>
                                    <?php echo $this->formHidden('theme', $theme->name, array('id'=>'')) ?>
                                </form>
                            <?php else:?>
                                <div class="current_theme">
                                    (<?php echo $this->translate("This is your current theme") ?>)
                                </div>
                            <?php endif;?> 
                        </div>
                    </li>
                    <?php $alt_row = !$alt_row; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif;?>

        <?php if(count($this->defaultLightThemes) > 0): ?>
        <div class="admin_theme_editor_chooser_wrapper_dark_default">
            <div class="admin_theme_header_controls">
                <h3> <?php echo $this->translate("Default Light Themes") ?> </h3>
            </div>
            <ul class="admin_themes">
                <?php
                // @todo meta key is deprecated and pending removal in 4.1.0; b/c removal in 4.2.0
                $alt_row = true;
                foreach ($this->defaultLightThemes as $theme):
                    $thumb = $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/anonymous.png';
                    if (!empty($this->manifest[$theme->name]['package']['thumb'])) {
                        $thumb = $this->manifest[$theme->name]['package']['thumb'];
                    }
                    ?>
                    <li <?php echo ($alt_row) ? ' class="alt_row"' : "";?>>
                        <div class="theme_wrapper">
                            <img src="<?php echo $thumb ?>" alt="<?php echo $theme->name?>">
                        </div>
                        <div class="theme_chooser_info">
                            <h3><?php echo $theme->title?></h3>
                            <?php if (!empty($this->manifest[$theme->name]['package']['author'])): ?>
                                <h4><?php echo $this->translate('by %s', $this->manifest[$theme->name]['package']['author']) ?></h4>
                            <?php endif; ?>
                            <?php if ($theme->name !== $this->activeTheme->name):?>
                                <form action="<?php echo $this->url(array('action' => 'change')) ?>" method="post">
                                    <button type="submit" class="activate_button"><?php echo $this->translate('Activate Theme') ?></button>
                                    <?php echo $this->formHidden('theme', $theme->name, array('id'=>'')) ?>
                                </form>
                            <?php else:?>
                                <div class="current_theme">
                                    (<?php echo $this->translate("This is your current theme") ?>)
                                </div>
                            <?php endif;?> 
                        </div>
                    </li>
                    <?php $alt_row = !$alt_row; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif;?>

        <?php if(count($this->defaultDarkThemes) > 0): ?>
        <div class="admin_theme_editor_chooser_wrapper_dark_default">
            <div class="admin_theme_header_controls">
                <h3> <?php echo $this->translate("Default Dark Themes") ?> </h3>
            </div>
            <ul class="admin_themes">
                <?php
                // @todo meta key is deprecated and pending removal in 4.1.0; b/c removal in 4.2.0
                $alt_row = true;
                foreach ($this->defaultDarkThemes as $theme):
                    $thumb = $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/anonymous.png';
                    if (!empty($this->manifest[$theme->name]['package']['thumb'])) {
                        $thumb = $this->manifest[$theme->name]['package']['thumb'];
                    }
                    ?>
                    <li <?php echo ($alt_row) ? ' class="alt_row"' : "";?>>
                        <div class="theme_wrapper">
                            <img src="<?php echo $thumb ?>" alt="<?php echo $theme->name?>">
                        </div>
                        <div class="theme_chooser_info">
                            <h3><?php echo $theme->title?></h3>
                            <?php if (!empty($this->manifest[$theme->name]['package']['author'])): ?>
                                <h4><?php echo $this->translate('by %s', $this->manifest[$theme->name]['package']['author']) ?></h4>
                            <?php endif; ?>
                            <?php if ($theme->name !== $this->activeTheme->name):?>
                                <form action="<?php echo $this->url(array('action' => 'change')) ?>" method="post">
                                    <button type="submit" class="activate_button"><?php echo $this->translate('Activate Theme') ?></button>
                                    <?php echo $this->formHidden('theme', $theme->name, array('id'=>'')) ?>
                                </form>
                            <?php else:?>
                                <div class="current_theme">
                                    (<?php echo $this->translate("This is your current theme") ?>)
                                </div>
                            <?php endif;?> 
                        </div>
                    </li>
                    <?php $alt_row = !$alt_row; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif;?>
    </div>

</div>

<script type="text/javascript">
    //<![CDATA[
    var updateCloneLink = function(){
        var value = $$('.theme_name input:checked');
        if (!value)
            return;
        else
            var newValue = value[0].value;
        var link = $$('a.admin_themes_header_clone');
        if (link.length) {
            link.set('href', link[0].href.replace(/\/name\/[^\/]+/, '/name/'+newValue));
        }
    }
    //]]>
</script>