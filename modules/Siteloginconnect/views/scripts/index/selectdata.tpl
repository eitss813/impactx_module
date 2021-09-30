<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteloginconnect
 * @copyright  Copyright 2016-2017 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: credit-send.tpl 2017-03-08 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php echo $this->content()->renderWidget("user.settings-menu"); ?>
<div>
<p>
Please choose the Profile fields you want to update with the information fetched from <?php echo ucfirst($this->socialsite); ?>.
</p>
</div>
<br/>
<br/>
<form enctype="application/x-www-form-urlencoded" class="socialsite_select_form" action="" method="post">
  <table class="Siteloginconnect_tablesection">
      <thead>
          <tr>
            <th style="width:10%;"> Select </th>
            <th style="width:40%;"> Field <th/>
            <th style="width:50%;"> Field Value <th/>
          </tr>
      </thead>
      <tbody>
          <?php foreach ($this->fieldarray as $key => $value): ?>
          <tr>
            <td><input type="checkbox" name="field_<?php echo $value['field_id'] ?>_<?php echo $value['profile_type'] ?>" /></td>
            <td> <?php echo $value['label'] ?> <td/>
            <td> <?php echo $this->social_site_fields_value[$value['option']] ?> <td/>
          </tr>
          <?php endforeach; ?>
          <?php if(!empty($this->photo_url)): ?>
            <tr>
              <td><input type="checkbox" name="photo_url" /></td>
              <td> <?php echo "Profile Photo" ?> <td/>
              <td> <img src="<?php echo $this->photo_url ?>" height="100px" width="100px" /> <td/>
            </tr>
          <?php endif; ?>
      </tbody>
  </table>
  <br/>
  <br/>
<button type="submit" name="submit">Submit</button>
<button type='button' onclick="redirecturl()"><?php echo $this->translate("Cancel") ?></button>
</form>


<script type="text/javascript">
function redirecturl() {
  window.location.href= en4.core.baseUrl+'sync';
}
</script>