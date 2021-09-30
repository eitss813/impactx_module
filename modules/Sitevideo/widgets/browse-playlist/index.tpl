<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->params['identity'] = $this->identity;
if (!$this->id)
    $this->id = $this->identity;
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');

$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css');
?>

<div class="sitevideo_browse_lists_view_options txt_right" id='videoViewFormat'>
    <div class="fleft">
        <?php if (empty($this->heading)) : ?>
            <?php echo $this->translate(array('%s playlist found.', '%s playlists found.', $this->totalCount), $this->totalCount); ?>
        <?php else : ?>
            <h3>
                <?php echo $this->heading; ?>
            </h3>
        <?php endif; ?>
    </div>
    <div class="fright">
        <?php if (in_array('gridView', $this->viewType)) : ?>
            <span class="seaocore_tab_select_wrapper fright">
                <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View"); ?></div>
                <span class="seaocore_tab_icon tab_icon_grid_view seaocore_tab_icon_<?php echo $this->identity ?>" onclick="viewObj.changeView('gridView')" id="gridView" ></span>
            </span>
        <?php endif; ?>
        <?php if (in_array('listView', $this->viewType)) : ?>
            <span class="seaocore_tab_select_wrapper fright">
                <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View"); ?></div>
                <span class="seaocore_tab_icon tab_icon_list_view seaocore_tab_icon_<?php echo $this->identity ?>" onclick="viewObj.changeView('listView')" id="listView" ></span>
            </span>
        <?php endif; ?>
    </div>
</div>

<?php
switch ($this->viewFormat) {
    case 'gridView' :
        include APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/playlist/_grid_view.tpl';
        break;
    case 'listView' :
        include APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/playlist/_list_view.tpl';
        break;
}
?>
<script lang="javascript">
    var View = function ()
    {
        this.selectedViewFormat = '';
        this.changeView = function (type)
        {
            this.selectedViewFormat = type;
            this.addBoldClass();
            window.location.href = en4.core.baseUrl + 'videos/playlists/browse/viewFormat/' + type;
        }
        this.addBoldClass = function ()
        {
            $$('.seaocore_tab_icon_<?php echo $this->identity ?>').each(function (el) {
                el.removeClass('active');
            });
            $(this.selectedViewFormat).addClass('active');
        }
    }
    viewObj = new View();
    viewObj.selectedViewFormat = '<?php echo $this->viewFormat ?>';
    viewObj.addBoldClass()
</script>





