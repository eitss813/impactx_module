<div class="yn-view-modes-block">
<div id="yndform-view-mode-<?php echo $this->identity;?>" class="yn-view-modes yndform-modeview-button">
    <span id="yndform_list-view" onclick="changeModeView(this)" class="yn-view-mode" rel="yndform_list-view" title="<?php echo $this->translate('List View')?>">
        <span class="ynicon yn-list-view"></span>
    </span>
    <span id="yndform_grid-view" onclick="changeModeView(this)" class="yn-view-mode active" rel="yndform_grid-view" title="<?php echo $this->translate('Grid View')?>">
        <span class="ynicon yn-grid-view"></span>
    </span>
</div>
</div>
<?php if ($this -> mode_view === 'list'): ?>
   <div id="yndform_total_item_count">
        <span><?php echo $this->paginator->getTotalItemCount();?></span>
        <?php echo $this->translate(array(" form", " forms",$this->paginator->getTotalItemCount()),$this->paginator->getTotalItemCount()) ?>
   </div>
<?php endif; ?>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class="yndform_grid-view yndform_forms_browse clearfix" id="yndform-listing-content-<?php echo $this ->identity;?>">
        <?php foreach ($this->paginator as $item): ?>
            <?php $category = Engine_Api::_()->getItem('yndynamicform_category', $item->category_id) ?>
            <li class="clearfix">
                <?php echo $this->partial('_formItemGrid.tpl', 'yndynamicform', array('item' => $item, 'mode_view' => $this -> mode_view, 'category' => $category)); ?>
                <?php echo $this->partial('_formItemList.tpl', 'yndynamicform', array('item' => $item, 'mode_view' => $this -> mode_view, 'category' => $category)); ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    )); ?>
<?php else: ?>
    <div class="tip">
        <span><?php echo $this -> translate("There are no available forms now.") ?></span>
    </div>
<?php endif; ?>

<script type="text/javascript">
    var ZendViewID = 'yndform-listing-content-<?php echo $this ->identity;?>';
    var parentElement = $(ZendViewID);

    window.addEvent('domready', function () {

        var modeViewCookie = Cookie.read(ZendViewID);
        $(modeViewCookie).addClass('active');
        changeModeView($(modeViewCookie));
    });

    function changeModeView(ele) {
        var modeView = ele.get('id');

        ele.getSiblings('span').removeClass('active');
        ele.addClass('active');

        parentElement.removeClass('yndform_list-view').removeClass('yndform_grid-view');
        if (modeView == 'yndform_list-view') {
            parentElement.addClass('yndform_list-view');
        } else {
            parentElement.addClass('yndform_grid-view');
        }

        Cookie.write(ZendViewID, modeView);
    }
</script>
