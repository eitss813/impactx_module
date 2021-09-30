<h2>
    <?php echo $this -> translate('Dynamic Form Plugin') ?>
</h2>

<?php if( count($this -> navigation) ): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        // -> setUlClass()
        echo $this -> navigation() -> menu() -> setContainer($this -> navigation) -> render()
        ?>
    </div>
<?php endif; ?>

<div class='clear'>
    <div class='settings'>
        <?php echo $this -> level_form -> render($this) ?>
    </div>

</div>

<script type="text/javascript">
    var fetchLevelSettings =function(level_id){
        window.location.href= en4.core.baseUrl+'admin/yndynamicform/settings/level/id/'+level_id;
        //alert(level_id);
    }
</script>