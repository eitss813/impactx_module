<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: services.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
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
    <span><?php echo $this->translate("To set up this section place ".SITECORETHEME_PLUGIN_NAME." - Services Block widget on your landing page via layout editor.") ?></span>
</div>
<div class='clear' style="margin-top: 10px;">
    <form class="global_form">
      <div>
        <h3><?php echo $this->translate("Manage Services Block") ?></h3>
        <p>
          <?php echo $this->translate("Here you can manage various services which you want to display in Services Block. <a title='Preview - Services Block' href='application/modules/Sitecoretheme/externals/images/screenshots/features-section.png' target='_blank' class='sitecoretheme_icon_view' > </a>") ?>
        </p>
        <p>
          <?php echo $this->translate("Note: Icon upload for the services is required here. Icon preview <a title='Preview - Services Block' href='application/modules/Sitecoretheme/externals/images/services/service_1.png' target='_blank' class='sitecoretheme_icon_view' > </a>") ?>
        </p>
        <br/>
        <p>
         <?php
          echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'add-services'), $this->translate('Add New Service'), array(
            'class' => 'smoothbox buttonlink seaocore_icon_add',
            ))
          ?>
        </p>
        <br/>
        <?php if( count($this->services) > 0 ): ?>
          <div class="seaocore_admin_order_list sitecoretheme_admin_manage_services">
            <div class="list_head">

                <div style="width:15%">
                    <?php echo $this->translate("Service Heading") ?>
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
                <?php foreach( $this->services as $item ): ?>
                  <?php
                    $iconUrl = $defaultIcon = $this->layout()->staticBaseUrl . 'application/modules/Sitecoretheme/externals/images/services/service_'.$item->service_id.'.png';
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
                            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'enable-service', 'id' => $item->getIdentity(), 'enable' => '0'), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title' => $this->translate('Make Disabled')))) ?>
                          <?php else: ?>
                            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'enable-service', 'id' => $item->getIdentity(), 'enable' => '1'), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/disapproved.gif', '', array('title' => $this->translate('Make Enabled')))) ?>
                          <?php endif; ?>
                        </div>


                        <div class="center" style="width:20%;text-align:center;">

                        <?php
                        echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'edit-service', 'id' => $item->getIdentity(), 'format' => 'smoothbox'), $this->translate('Edit'), array(
                          'class' => 'smoothbox'
                        ));
                        ?>
                        |
                        <?php
                        echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'delete-service', 'id' => $item->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete'), array(
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
            <span><?php echo $this->translate("There are currently no services.") ?></span>
          </div>
        <?php endif; ?>
        <br/>

      </div>
    </form>
  </div>
</div>

<script type="text/javascript">
  window.addEvent('load', function () {
    var item = 'sitecoretheme_service';
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