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
<h2>
  <?php echo SITECORETHEME_PLUGIN_NAME; ?>
</h2>

<div  class='seaocore_admin_tabs tabs clr'>
  <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
</div>

<h2>
  <?php echo $this->translate("Manage Informative Blocks") ?>
</h2>
<p>
  Informative Blocks are a great way to make your page look attractive and informative. Here you can create new blocks and manage the existing ones. For each block, you can choose a heading, tagline, description and URL of the CTA link and image. 
</p>
<br />
<div class="tip">
  <span>
    In order to showcase this block on desired page, it is mandatory to create at least one informative block.   To set up this section place <?php echo SITECORETHEME_PLUGIN_NAME ?> - Informative Block widget via layout editor.
  </span>
</div>

<div>
  <?php
  echo $this->htmlLink(array('action' => 'create', 'reset' => false), $this->translate("Create New Informative Block"), array(
    'class' => 'buttonlink',
    'style' => 'background-image: url(' . $this->layout()->staticBaseUrl . 'application/modules/Sitecoretheme/externals/images/admin/add.png);'))
  ?>
</div>

<br/>
<div class='admin_results'>
  <div>
    <?php $count = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s block found.", "%s blocks found.", $count), $count) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
</div>

<br />
<?php if( count($this->paginator) ): ?>
  <table class='admin_table'>
    <thead>
      <tr>
        <th style="width: 1%;">
          <?php echo $this->translate("ID") ?>
        </th>
        <th>
          <?php echo $this->translate("Title") ?>
        </th>
        <th style="width: 1%;">
          <?php echo $this->translate("Options") ?>
        </th>
      </tr>
    </thead>
    <tbody>
      <?php foreach( $this->paginator as $item ): ?>
        <tr>
          <td><?php echo $item->block_id ?></td>
          <td style="white-space: normal;"><?php echo $item->getTitle() ?></td>
          <td class="admin_table_options">
            <a href='<?php echo $this->url(array('action' => 'edit', 'id' => $item->block_id)) ?>'>
              <?php echo $this->translate("edit") ?>
            </a> |
            <a class='smoothbox' href='<?php echo $this->url(array('action' => 'preview-block', 'controller' => 'general', 'module' => 'sitecoretheme',  'id' => $item->block_id), 'default', true) ?>'>
              <?php echo $this->translate("preview") ?>
            </a>

            |
            <a class='smoothbox' href='<?php echo $this->url(array('action' => 'delete', 'id' => $item->block_id)) ?>'>
              <?php echo $this->translate("delete") ?>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

<?php else: ?>

  <div class="tip">
    <span><?php echo $this->translate("There are no blocks created.") ?></span>
  </div>
<?php endif; ?>