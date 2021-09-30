<ul class="yndform_forms_browse clearfix">
    <?php foreach ($this -> forms as $item): ?>
        <?php $category = Engine_Api::_() -> getItem('yndynamicform_category', $item -> category_id) ?>
        <li class="clearfix">
            <?php echo $this -> partial('_formItem.tpl', 'yndynamicform', array('item' => $item, 'mode_view' => $this -> mode_view, 'category' => $category)); ?>
        </li>
    <?php endforeach; ?>
</ul>
