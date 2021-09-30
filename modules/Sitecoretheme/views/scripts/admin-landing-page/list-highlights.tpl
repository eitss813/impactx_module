<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: list-highlights.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecoretheme/externals/scripts/reorder.js'); ?>

<h2>
    <?php echo SITECORETHEME_PLUGIN_NAME; ?>
</h2>

<div class='seaocore_admin_tabs tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
</div>
<div class='seaocore_sub_tabs tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->subNavigation)->render() ?>
</div>
<div class="tip">
    <span><?php echo $this->translate("To set up this section place ".SITECORETHEME_PLUGIN_NAME." - Highlights Block widget on your landing page via layout editor.") ?></span>
</div>

<?php
  $settingsUrl = $this->url(array('module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'highlights'), 'admin_default', false);
  $editUrl = $this->url(array('module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'list-highlights'), 'admin_default', false);
?>
<div class='tabs seaocore_sub_tabs'>
  <ul class="navigation">    
    <li  class="<?php echo ($this->selectedMenuType == 'view')? 'active': ''; ?>">
      <a href="<?php echo $settingsUrl; ?>"><?php echo $this->translate("Settings"); ?></a>
    </li>
    <li  class="<?php echo ($this->selectedMenuType == 'edit')? 'active': ''; ?>">
      <a href="<?php echo $editUrl; ?>"><?php echo $this->translate("Manage Highlights Block"); ?></a>
    </li>
  </ul>
</div>
<?php if($this->oddHightLights || $this->minimumHighlight): ?>
  <div class="seaocore_tip">
    <span><?php echo $this->translate($this->message) ?></span>
  </div>
<?php endif; ?>

<div class='clear' style="margin-top: 10px;">
    <form class="global_form">
      <div>
        <h3><?php echo $this->translate("Manage Highlights Block") ?></h3>
        <p>
          <?php echo $this->translate("This page lists all the points present on Highlights Block. Add / edit various points of the Highlight Block. [Note: Add even Blocks in order to get similar yet admirable visual of this section.]") ?>
        </p>
        <br/>
        <?php if( count($this->highlights) > 0 ): ?>
          <div class="seaocore_admin_order_list sitecoretheme_admin_edit_highlight">
            <div class="list_head">

                <div style="width:15%">
                    <?php echo $this->translate("Title") ?>
                </div>

                <div class="center" style="width:10%;text-align:center;">
                    <?php echo $this->translate("Icon") ?>
                </div>

                <div class="center" style="width:35%;text-align:center;">
                    <?php echo $this->translate("Description") ?>
                </div>

                <div class="center" style="width:10%;text-align:center;">
                    <?php echo $this->translate("Enabled") ?>
                </div>

                <div class="center" style="width:20%;text-align:center;">
                    <?php echo $this->translate("Options") ?>
                </div>
            </div>
            <ul id='menu_list'>
                <?php foreach( $this->highlights as $item ): ?>
                    <?php
                      $iconUrl = $defaultIcon = $this->layout()->staticBaseUrl . 'application/modules/Sitecoretheme/externals/images/highlights/highlight_'.$item->highlights_id.'.png';
                      if($item->file_id) {
                         $icon = Engine_Api::_()->storage()->get($item->file_id);
                         $iconUrl = ( $icon ) ? $icon->getPhotoUrl() : $defaultIcon;
                      } 
                    ?>
                    <li id="content_<?php echo $item->getIdentity(); ?>" class="admin_table_bold item_label">
                        <input type='hidden'  name='order[]' value='<?php echo $item->getIdentity(); ?>'>

                        <div style="width:15%">
                            <?php echo $item->getTitle(); ?>
                        </div>

                        <div class="center" style="width:10%;text-align:center;">
                          <span>
                            <img src="<?php echo $iconUrl; ?>">
                          </span>
                        </div>

                        <div class="center" style="width:35%;text-align:center;">
                            <?php echo $item->description; ?>
                        </div>
                        <div class="center" style="width:10%;text-align:center;">
                          <?php if( $item->enabled == 1 ): ?>
                            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'enable-highlight', 'id' => $item->getIdentity(), 'enable' => '0'), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title' => $this->translate('Make Disabled')))) ?>
                          <?php else: ?>
                            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'enable-highlight', 'id' => $item->getIdentity(), 'enable' => '1'), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/disapproved.gif', '', array('title' => $this->translate('Make Enabled')))) ?>
                          <?php endif; ?>
                        </div>


                        <div class="center" style="width:20%;text-align:center;">

                          <?php
                          echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'edit-highlight', 'id' => $item->getIdentity(), 'format' => 'smoothbox'), $this->translate('Edit'), array(
                            'class' => 'smoothbox'
                          ));
                          ?>
                        </div>

                    <?php endforeach; ?>
            </ul>
        </div>
          
        <?php else: ?>
          <br/>
          <div class="tip">
            <span><?php echo $this->translate("There are currently no highlights.") ?></span>
          </div>
        <?php endif; ?>
        <br/>

      </div>
    </form>
  </div>
</div>

<script type="text/javascript">
  window.addEvent('load', function () {
    var item = 'sitecoretheme_highlight';
    var url = '<?php echo $this->url(array('controller' => 'settings','action' => 'set-order')) ?>';
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
</script>