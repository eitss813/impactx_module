<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>


<script type="text/javascript">

  function multiDelete()
  {
    return confirm("<?php echo $this->translate('Are you sure you want to delete the selected verify entry?'); ?>");
  }

  function selectAll()
  {
    var i;
    var multidelete_form = $('multidelete_form');
    var inputs = multidelete_form.elements;
    for (i = 1; i < inputs.length; i++) {
      if (!inputs[i].disabled) {
        inputs[i].checked = inputs[0].checked;
      }
    }
  }
</script>

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

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->subnavigation)->render()
    ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Verifications') ?></h3>
<p>
  <?php echo $this->translate("This page lists all the verifications made by the site members. You can use this page to monitor these verification entries and delete / edit / enable / disable any verification entry if necessary. For each verified page, the total verifications count is shown.") ?>
</p>
<br />

<?php if (count($this->paginator)): ?>
  <form id='multidelete_form' method="post" action="<?php echo $this->url(); ?>" onSubmit="return multiDelete();">
    <table class='admin_table'>
      <thead>
        <tr>
          <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
          <th class='admin_table_short'><?php echo $this->translate("ID"); ?></th>
          <th><?php echo $this->translate("Verified Page") ?></th>
          <th><?php echo $this->translate("Verified By") ?></th>
          <th><?php echo $this->translate("Total Verifications") ?></th>
          <th><?php echo $this->translate("Marked Verified") ?></th>
          <th class="center"><?php echo $this->translate("Status") ?></th>
          <th class="center"><?php echo $this->translate("Comments") ?></th>
          <th><?php echo $this->translate("Verification Date") ?></th>
          <th><?php echo $this->translate("Options") ?></th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($this->paginator as $item):
          // OBJECTS OF USERS ACCORDING TO RESOURCE ID AND POSTER ID
          $resource = Engine_Api::_()->getItem('sitepage_page', $item->resource_id);
          $poster = Engine_Api::_()->getItem('user', $item->poster_id);
          $verify_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.verify.limit', 3);
          $verifyTable = Engine_Api::_()->getDbtable('verifies', 'sitepage');
          ?>
          <tr>
            <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->getIdentity(); ?>' value="<?php echo $item->getIdentity(); ?>" /></td>
            <td><?php echo $item->getIdentity(); ?></td>
            <td><?php
              echo $this->htmlLink($resource->getHref(), $this->itemPhoto($resource, 'thumb.icon')) . '<br />';
              echo $this->htmlLink($resource->getHref(), $resource->getTitle(), array('target' => "_blank"))
              ?> </td>
            <td><?php
              echo $this->htmlLink($poster->getHref(), $this->itemPhoto($poster, 'thumb.icon')) . '<br />';
              echo $this->htmlLink($poster->getHref(), $poster->getTitle(), array('target' => "_blank"))
              ?> </td>
            <td>
              <a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $this->url(array('route' => 'default', 'module' => 'sitepage', 'controller' => 'verify', 'action' => 'content-verify-member-list', 'resource_id' => $item->resource_id), 'default', true) ?>');"><?php echo $verifyTable->getVerifyCount($item->resource_id); ?></a></td>
            <td><?php
              if ($verifyTable->getVerifyCount($item->resource_id) >= $verify_limit):
                echo $this->translate("Yes");
              else:
                echo $this->translate("No");
              endif;
              ?></td>

            <?php if (!empty($item->status)): ?>
              <td class="center"><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'verify', 'action' => 'status', 'id' => $item->getIdentity()), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepageverify_approved1.gif', '', array('title' => $this->translate('Disable this verification')))) ?></td>
            <?php else: ?>
              <td class="center"><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'verify', 'action' => 'status', 'id' => $item->getIdentity()), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepageverify_approved0.gif', '', array('title' => $this->translate('Enable this verification')))) ?></td>
            <?php endif; ?>

            <td class="center"><?php
              $verify_comments = Engine_Api::_()->seaocore()->seaddonstruncateTitle($item->comments);
              if ($verify_comments):
                ?>
                <span title="<?php echo $item->comments; ?>"> <?php echo $verify_comments; ?></span>
                <?php
              else :
                echo $this->translate("---");
              endif;
              ?></td>

              
            <td><?php echo $this->locale()->toDateTime($item->creation_date, array('format' => 'MMMM d, y')); ?></td>
            <td>
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'verify', 'action' => 'detail', 'id' => $item->verify_id), $this->translate('details'), array('class' => 'smoothbox')) ?>
              |
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'verify', 'action' => 'edit', 'id' => $item->verify_id), $this->translate('edit'), array('class' => 'smoothbox')) ?>
              |
              <?php
              echo $this->htmlLink(
                      array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'verify', 'action' => 'delete', 'id' => $item->verify_id), $this->translate("delete"), array('class' => 'smoothbox'))
              ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <br/>
    <div class='buttons'>
      <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
    </div>
  </form>
  <br/>
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no verified entries yet.") ?>
    </span>
  </div>
<?php endif; ?>
