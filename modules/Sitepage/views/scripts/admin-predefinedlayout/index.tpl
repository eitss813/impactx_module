<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    sitepage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 10002 2013-03-26 22:30:42Z jung $
 * @author     John
 */

?>
<h2 class="fleft"><?php echo $this->translate('Directory / Pages Plugin'); ?></h2>
<?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/manageExtensions.tpl'; ?>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<!-- check extension installed or not -->
<?php
$featureExtension = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.extension', 0);
if ($featureExtension) :?>
<div class='tabs'>
  <ul class="navigation">
    <li class="active">
      <?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitepage','controller'=>'predefinedlayout','action'=>'index'), $this->translate('Layout Editor'), array())
      ?>
    </li>
    <li>
      <?php
      echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitepage','controller'=>'layoutmaps','action'=>'manage'), $this->translate('Mapping of layouts'), array())
      ?>
    </li>
  </ul>
</div>
<div>
  <?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitepage','controller'=>'predefinedlayout','action' => 'create'), $this->translate('Create New Layout'), array(
    'class' => 'buttonlink icon_sitepage_admin_add smoothbox',
    )) ?>
  </div>

  <div class='clear seaocore_settings_form'>
    <div class='settings'>
      <form class="global_form">
        <div>
          <h3><?php echo $this->translate("Layouts")?></h3>
          <?php if(!empty($this->layouts->toArray())):?>
            <table class='admin_table' width="100%">
              <thead>
                <tr>
                  <th><?php echo $this->translate("Layout Id") ?></th>
                  <th><?php echo $this->translate("Layout Name") ?></th>
                  <th><?php echo $this->translate("Layout Title") ?></th>
                  <th><?php echo $this->translate("Layout Image") ?></th>
                  <th><?php echo $this->translate("Status") ?></th>
                  <th><?php echo $this->translate("Options") ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($this->layouts as $layout): ?>
                  <tr>
                    <td><?php echo $layout->getIdentity(); ?></td>
                    <td><?php echo Engine_Api::_()->getDbTable('definedlayouts', 'sitepage')->getPageName($layout->page_id) ?></td>
                    <td><?php echo $layout->getTitle(); ?></td>
                    <td>
                      <?php
                      $temp_url = $layout->getPhotoUrl();
                      if (!empty($temp_url)):
                        $url = $layout->getPhotoUrl('thumb.icon');
                      endif;
                      ?>
                      <img src="<?php echo $url;?>">
                      <div class="_view_sitepage_layout"><a title="Preview" href="<?php echo $layout->getPhotoUrl();?>" target="_blank" class="seaocore_icon_view" ></a></div>
                    </td>
                    <td>
                      <?php if($layout->status)
                      echo $this->translate("Enable");
                      else
                        echo $this->translate("Disable"); 
                      ?>  
                    </td>
                    <td>
                      <a href='<?php echo $this->url(array('controller' => 'content', 'action' => 'index', 'page' => $layout->page_id), 'admin_default', true) ?>' class="buttonlink seaocore_icon_edit"><?php echo $this->translate("Edit Layout");?></a>
                      <a href='<?php echo  $this->url(array('module' => 'sitepage','controller' => 'predefinedlayout','action' => 'edit', 'layout_id' => $layout->getIdentity()));?>')"; class="buttonlink seaocore_icon_edit smoothbox" ><?php echo $this->translate("Edit Details");?></a>
                      <a href='<?php echo  $this->url(array('module' => 'sitepage','controller' => 'predefinedlayout','action' => 'delete', 'layout_id' => $layout->getIdentity()));?>')"; class="buttonlink seaocore_icon_delete smoothbox" ><?php echo $this->translate('Remove');?></a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else:?>
            <br/>
            <div class="tip">
              <span><?php echo $this->translate("No Layout has been created yet") ?></span>
            </div>
          <?php endif;?>
        </div>
      </form>
    </div>
  </div>
<?php else:?>
  <div class="tip">
    <span><?php echo $this->translate("Please install the new Feature extension to use this feature.") ?></span>
  </div>
<?php endif;?>