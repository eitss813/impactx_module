<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: inner-images.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecoretheme/externals/scripts/reorder.js'); ?>

<?php
$verticalThemeActivated = true;
$themeInfo = Zend_Registry::get('Themes', null);
if (!empty($themeInfo)):
    foreach ($themeInfo as $key => $value):
        if ($key != 'sitecoretheme'):
            $verticalThemeActivated = false;
        endif;
    endforeach;
endif;

if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecoretheme.isActivate', 0)) && empty($verticalThemeActivated)):
    ?>
    <div class="seaocore_tip">
        <span>
            <?php echo "Please activate the '".SITECORETHEME_PLUGIN_NAME."' from 'Appearance' >> 'Theme Editor' available in the admin panel of your site." ?>
        </span>
    </div>
<?php endif; ?>

<h2>
    <?php echo SITECORETHEME_PLUGIN_NAME ?>
</h2>

<div class='seaocore_admin_tabs tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
</div>

<div class='seaocore_sub_tabs tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->subNavigation)->render() ?>
</div>


<h3><?php echo $this->translate('Inner Page Slider Images'); ?></h3>
<p class="form-description">
    <?php echo $this->translate("This theme enables you to have Sliders (banners) on desired widgetized pages of your website. Sliders (banners) enable you to make your website's pages look attractive by enhancing their visual appeal, and are also good for Search Engine Optimization (SEO). Here, you can add, delete and manage your images. In order to change the sequence of images, drag and drop images vertically and set them in any order you want them to appear on landing page. Multiple images can be added to display them in circular manner i.e one after another.  Wherever you want to show Slider, place the '".SITECORETHEME_PLUGIN_NAME." - Inner Page Slider' widget, and configure its settings such as Title, Description, images etc. to show on the slider."); ?>
</p>

<br />
<p>
    <a href='<?php echo $this->url(array("module" => 'sitecoretheme', "controller" => "settings", "action" => 'add-banners'), "admin_default", true) ?>' class="smoothbox buttonlink seaocore_icon_add"><?php echo $this->translate("Add New Image"); ?></a>
</p>

<br />

<?php if (COUNT($this->list)): ?>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'multi-delete-banners')); ?>" onSubmit="return multiDelete()">
        <div class="seaocore_admin_order_list">
            <div class="list_head">
                <div class="center" style="width:2%;text-align:center;">
                    <input onclick="selectAll()" type='checkbox' class='checkbox'>
                </div>
                <div class="center" style="width:5%;text-align:center;">
                    <?php echo "Id"; ?>
                </div>

                <div style="width:15%">
                    <?php echo "Image Name"; ?>
                </div>

                <div class="center" style="width:30%;text-align:center;">
                    <?php echo "Banner Images"; ?>
                </div>

                <div class="center" style="width:15%;text-align:center;">
                    <?php echo "Enabled"; ?>
                </div> 

                <div class="center" style="width:15%;text-align:center;">
                    <?php echo "Options"; ?>
                </div>
            </div>
            <ul id='menu_list'>
                <?php foreach ($this->list as $item): ?>
                    <li id="content_<?php echo $item->getIdentity(); ?>" class="admin_table_bold item_label">
                        <input type='hidden'  name='order[]' value='<?php echo $item->getIdentity(); ?>'>
                        <div class="center" style="width:2%;text-align:center;">
                            <input name='delete_<?php echo $item->getIdentity(); ?>' type='checkbox' class='checkbox' value="<?php echo $item->getIdentity() ?>"/>
                        </div>
                        <div class="center" style="width:5%;text-align:center;">
                            <?php echo $item->getIdentity(); ?>
                        </div>

                        <div style="width:15%">
                            <?php echo $item->getTitle(); ?>
                        </div>

                        <div class="center" style="width:30%;text-align:center;">
                            <?php
                            $iconSrc = Engine_Api::_()->sitecoretheme()->displayPhoto($item->icon_id, 'thumb.icon');
                            if (!empty($iconSrc)):
                                ?>
                                <img src="<?php echo $iconSrc; ?>" />
                            <?php endif; ?>
                        </div>
                        <div class="center" style="width:15%;text-align:center;">
                            <a href='<?php echo $this->url(array("module" => 'sitecoretheme', "controller" => "settings", "action" => 'enabled-banners', 'id' => $item->getIdentity()), "admin_default", true) ?>' >
                                <?php if (!empty($item->enabled)): ?>
                                    <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif' ?>" alt="" title="Make Disabled">
                                <?php else: ?>
                                    <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/disapproved.gif' ?>" alt="" title="Make Enabled">
                                <?php endif; ?></a>
                        </div> 

                        <div class="center" style="width:15%;text-align:center;">
                            <a href='<?php echo $this->url(array("module" => 'sitecoretheme', "controller" => "settings", "action" => 'delete-banners', 'id' => $item->getIdentity()), "admin_default", true) ?>' class="smoothbox"><?php echo "Delete"; ?></a>
                        </div>

                    <?php endforeach; ?>
            </ul>
        </div>
        <br />
        <div class='buttons'>
            <button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
        </div>
    </form>
<?php else: ?>
    <div class="tip" style="width: 100%">
        <span style="width: 100%"><?php echo $this->translate('No banners Found!') ?></span>
    </div>
<?php endif; ?>

<script type="text/javascript">
    window.addEvent('load', function () {
    var item = 'sitecoretheme_banner';
    var url = '<?php echo $this->url(array('action' => 'set-order')) ?>';
    var SortablesInstance;
    SortablesInstance = new Sortables('menu_list', {
        clone: true,
        constrain: false,
        handle: '.item_label',
        onComplete: function (e) {
            reorder(e,item,url);
        }
    });

  });

    function multiDelete()
    {
        return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected banners ?")) ?>');
    }

    function selectAll()
    {
        var i;
        var multidelete_form = $('multidelete_form');
        var inputs = multidelete_form.elements;
        for (i = 1; i < inputs.length - 1; i++) {
            if (!inputs[i].disabled) {
                inputs[i].checked = inputs[0].checked;
            }
        }
    }
</script>