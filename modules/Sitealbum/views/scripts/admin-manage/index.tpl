<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate("Advanced Albums Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate("Manage Albums") ?></h3>
<p>
  <?php echo $this->translate("This page lists all the photo albums your users have created. You can use this page to monitor these albums and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific album entries. Leaving the filter fields blank will show all the album entries on your social network. ") ?>
</p>
<br />

<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction) {
    // Just change direction
    if (order == currentOrder) {
      $('order_direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC');
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }

    $('filter_form').submit();
  };

  function multiDelete()
  {
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected albums ?")) ?>');
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

<div class="admin_search sitealbum_admin_album_search">
  <div class="search">
    <form method="post" class="global_form_box" action="" width="100%">

      <div>
        <label>
          <?php echo $this->translate("Title") ?>
        </label>
        <?php if (empty($this->title)): ?>
          <input type="text" name="title" /> 
        <?php else: ?>
          <input type="text" name="title" value="<?php echo $this->translate($this->title) ?>"/>
        <?php endif; ?>
      </div>

      <div>
        <label>
          <?php echo $this->translate("Owner") ?>
        </label>	
        <?php if (empty($this->owner)): ?>
          <input type="text" name="owner" /> 
        <?php else: ?> 
          <input type="text" name="owner" value="<?php echo $this->translate($this->owner) ?>" />
        <?php endif; ?>
      </div>        

      <?php $categories = Engine_Api::_()->getDbtable('categories', 'sitealbum')->getCategories(array('fetchColumns' => array('category_id', 'category_name'), 'sponsored' => 0, 'cat_depandancy' => 1)); ?>              
      <div class="form-wrapper" id="category_id-wrapper">
        <div class="form-label" id="category_id-label">
          <label class="optional" for="category_id"><?php echo $this->translate('Category'); ?></label>
        </div>
        <div class="form-element" id="category_id-element">
          <select id="category_id" name="category_id" onchange="subcategories(this.value, '', '');">
            <option value=""></option>
            <?php if (count($categories) != 0): ?>
              <?php
              $categories_prepared[0] = "";
              foreach ($categories as $category) {
                $categories_prepared[$category->category_id] = $category->category_name;
                ?>
                <option value="<?php echo $category->category_id; ?>" <?php if ($this->category_id == $category->category_id) echo "selected"; ?>><?php echo $this->translate($category->category_name); ?></option>
              <?php } ?>
            <?php endif; ?>
          </select>
        </div>
      </div>

      <div class="form-wrapper" id="subcategory_id-wrapper" style='display:none;'>
        <div class="form-label" id="subcategory_id-label">
          <label class="optional" for="subcategory_id"><?php echo $this->translate('Sub-Category'); ?></label>
        </div>
        <div class="form-element" id="subcategory_id-element">
          <select id="subcategory_id" name="subcategory_id"></select>
        </div>
      </div>
      <div>
        <label>
          <?php echo $this->translate("Browse By") ?>	
        </label>
        <select id="" name="albumbrowse">
          <option value="0" ><?php echo $this->translate("Select") ?></option>
          <option value="1" <?php if ($this->albumbrowse == 1) echo "selected"; ?> ><?php echo $this->translate("Most Recent") ?></option>
          <option value="2" <?php if ($this->albumbrowse == 2) echo "selected"; ?> ><?php echo $this->translate("Most Viewed") ?></option>
          <option value="3" <?php if ($this->albumbrowse == 3) echo "selected"; ?> ><?php echo $this->translate("Most Liked") ?></option>
          <option value="4" <?php if ($this->albumbrowse == 4) echo "selected"; ?> ><?php echo $this->translate("Most Commented") ?></option>
          <option value="5" <?php if ($this->albumbrowse == 5) echo "selected"; ?> ><?php echo $this->translate("Most Rated") ?></option>
        </select>
      </div>

      <div class="clear mtop10">
        <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
      </div>
    </form>
  </div>
</div>
<br />

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>
<br />

<?php if (count($this->paginator) > 0): ?>
  <div class='admin_members_results'>
    <div>
      <?php $count = $this->paginator->getTotalItemCount() ?>
      <?php echo $this->translate(array("%s album found.", "%s albums found.", $count), $this->locale()->toNumber($count))
      ?>
    </div>
  <?php else: ?>
    <div class="tip"><span>
        <?php echo $this->translate("No albums were found.") ?></span>
    </div>
  <?php endif; ?>
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
</div>
<br />

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
  <form id='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'multi-delete')); ?>" onSubmit="return multiDelete();">
    <table class='admin_table seaocore_admin_table' width="100%">
      <thead>
        <tr>
          <th><input onclick="selectAll();" type='checkbox' class='checkbox'></th>

          <?php $class = ( $this->order == 'album_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('album_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>

          <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="<?php echo $class ?>"  align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate("Title") ?></a></th>

          <?php $class = ( $this->order == 'displayname' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'ASC');"><?php echo $this->translate("Owner") ?></a></th>

          <?php $class = ( $this->order == 'category_name' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('category_name', 'ASC');"><?php echo $this->translate("Category") ?></a></th>

          <?php $class = ( $this->order == 'photos_count' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="<?php echo $class ?> center"><a href="javascript:void(0);" onclick="javascript:changeOrder('photos_count', 'ASC');" title="<?php echo $this->translate('Photos'); ?>"><?php echo $this->translate("Photos") ?></a></th>

          <?php $class = ( $this->order == 'rating' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('rating', 'ASC');"><?php echo $this->translate('Overall Rating'); ?></a></th>

          <?php $class = ( $this->order == 'view_count' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="<?php echo $class ?> center" class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('view_count', 'ASC');" title="<?php echo $this->translate('Views'); ?>" ><?php echo $this->translate('Views'); ?></a></th>

          <?php $class = ( $this->order == 'like_count' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="<?php echo $class ?> center"  class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('like_count', 'ASC');" title="<?php echo $this->translate('Likes'); ?>" ><?php echo $this->translate('Likes'); ?></a></th>

          <?php $class = ( $this->order == 'comment_count' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="<?php echo $class ?> center" class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('comment_count', 'ASC');" title="<?php echo $this->translate('Comments'); ?>" ><?php echo $this->translate('Comments'); ?></a></th>

          <?php $class = ( $this->order == 'creation_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate("Creation Date") ?></a></th>
          <th class="<?php echo $class ?>" class='admin_table_centered'><?php echo $this->translate("Option") ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($this->paginator)): ?>
          <?php
          foreach ($this->paginator as $item):
            ?>
            <tr>
              <td><input name='delete_<?php echo $item->album_id; ?>' type='checkbox' class='checkbox' value="<?php echo $item->album_id ?>"/></td>
              <td><?php echo $item->album_id ?></td>
              <td class='admin_table_bold'>
                <?php
                echo $this->htmlLink($item->getHref(), $this->string()->truncate($item->getTitle(), 10), array('target' => '_blank'))
                ?>
              </td>
              <td class='admin_table_user'>
                <?php echo $this->htmlLink($item->getOwner()->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getOwner()->getTitle(), 10), array('target' => '_blank')) ?>
              </td>
              <td class='admin_table_email'>
                <?php
                if ($item->category_id):
                  $categoryName = Engine_Api::_()->getDbtable('categories', 'sitealbum')->getCategoryName($item->category_id);
                  ?>
                  <?php if ($categoryName) : ?>
                    <a href="<?php echo $this->url(array('category_id' => $item->category_id, 'categoryname' => Engine_Api::_()->getItem('album_category', $item->category_id)->getCategorySlug()), 'sitealbum_general_category', true) ?>" target="_blank">
                      <span><?php echo $categoryName; ?></span> 
                    </a>
                  <?php endif; ?>
                  <?php
                else :
                  echo "---";
                endif;
                ?>
              </td>
              <td class="center"><?php echo $this->locale()->toNumber($item->photos_count) ?></td>
              <td>
                <div>
                  <span title="<?php echo $item->rating . $this->translate('rating '); ?>">
                    <?php if (($item->rating > 0)): ?>
                      <?php for ($x = 1; $x <= $item->rating; $x++): ?>
                        <span class="rating_star_generic rating_star"></span>
                        <?php
                      endfor;
                      $roundrating = round($item->rating)
                      ?>
                      <?php if (($roundrating - $item->rating) > 0): ?>
                        <span class="rating_star_generic rating_star_half"></span>
                      <?php endif; ?>
                      <?php
                      $roundrating++;
                      for ($x = $roundrating; $x <= 5; $x++) {
                        ?>
                        <span class="rating_star_generic rating_star_disabled"></span>
                      <?php } ?>

                    <?php else : ?>
                      <span class="rating_star_generic rating_star_disabled"></span>
                      <span class="rating_star_generic rating_star_disabled"></span>
                      <span class="rating_star_generic rating_star_disabled"></span>
                      <span class="rating_star_generic rating_star_disabled"></span>
                      <span class="rating_star_generic rating_star_disabled"></span>
                    <?php endif; ?>
                  </span>
                </div>
              </td>
              <td class="center"><?php echo $this->locale()->toNumber($item->view_count) ?></td>
              <td class="center"><?php echo $this->locale()->toNumber($item->like_count) ?></td>
              <td class="center"><?php echo $this->locale()->toNumber($item->comment_count) ?></td>
              <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>             

              <td class='admin_table_options'>
                <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitealbum', 'controller' => 'admin-manage', 'action' => 'delete', 'id' => $item->album_id), $this->translate("delete"), array('class' => 'smoothbox')); ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
    <br />
    <div class='buttons'>
      <button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
    </div>
  </form>
<?php endif; ?>

<script>
  window.addEvent('domready', function() {
    var search_category_id = '<?php echo $this->category_id ?>';
    if (search_category_id != 0) {
      var search_subcategory_id = '<?php echo $this->subcategory_id ?>';
      subcategories(search_category_id, search_subcategory_id, 1);
    }
  });

  var subcategories = function(category_id, subcategory_id, domready)
  {
    if (domready == 0) {
      $('subcategory_id' + '-wrapper').style.display = 'none';
      clear('subcategory_id');
      $('subcategory_id').value = 0;
    }

    if (category_id <= 0)
      return;

    var url = '<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'index', 'action' => 'sub-category', 'showAllCategories' => $this->showAllCategories), "default", true); ?>';

    en4.core.request.send(new Request.JSON({
      url: url,
      data: {
        format: 'json',
        category_id_temp: category_id,
      },
      onSuccess: function(responseJSON) {
        clear('subcategory_id');
        var subcatss = responseJSON.subcats;

        addOption($('subcategory_id'), " ", '0');
        for (i = 0; i < subcatss.length; i++) {
          addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);
          $('subcategory_id').value = subcategory_id;
        }

        if (category_id == 0) {
          clear('subcategory_id');
          $('subcategory_id').style.display = 'none';
          if ($('subcategory_id-label'))
            $('subcategory_id-label').style.display = 'none';
        }
      }
    }), {'force': true});
  };

  function clear(ddName)
  {
    for (var i = (document.getElementById(ddName).options.length - 1); i >= 0; i--)
    {
      document.getElementById(ddName).options[ i ] = null;
    }
  }

  function addOption(selectbox, text, value)
  {
    var optn = document.createElement("OPTION");
    optn.text = text;
    optn.value = value;

    if (optn.text != '' && optn.value != '') {
      $('subcategory_id').style.display = 'inline-block';
      if ($('subcategory_id-wrapper'))
        $('subcategory_id-wrapper').style.display = 'inline-block';
      if ($('subcategory_id-label'))
        $('subcategory_id-label').style.display = 'inline-block';
      selectbox.options.add(optn);
    } else {
      $('subcategory_id').style.display = 'none';
      if ($('subcategory_id-wrapper'))
        $('subcategory_id-wrapper').style.display = 'none';
      if ($('subcategory_id-label'))
        $('subcategory_id-label').style.display = 'none';
      selectbox.options.add(optn);
    }
  }
</script>
