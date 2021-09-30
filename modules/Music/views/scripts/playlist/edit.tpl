<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: edit.tpl 9987 2013-03-20 00:58:10Z john $
 * @author     Steve
 */
$songs = $this->playlist->getSongs();
?>
<div class="layout_middle">
  <div class="generic_layout_container">
     <div class="headline">
      <h2>
       <?php echo $this->translate('Music');?>
      </h2>
      <div class="tabs">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->navigation)
          ->render();
      ?>
      </div>
    </div>
  </div>
</div>
<div class="layout_middle">
  <div class="generic_layout_container">
<?php echo $this->form->render($this) ?>

<div style="display:none;">
  <?php if (!empty($songs)): ?>
    <ul id="music_songlist">
      <?php foreach ($songs as $song): ?>
      <li id="song_item_<?php echo $song->song_id ?>" class="file file-success">
        <a href="javascript:void(0)" class="file-remove" data-file_id="<?php echo $song->song_id ?>">
          <b><?php echo $this->translate('Remove') ?></b>
        </a>
        <span class="file-name">
          <?php echo $song->getTitle() ?>
        </span>
        (<a href="javascript:void(0)" class="song_action_rename file-rename"><?php echo $this->translate('rename') ?></a>)
      </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
</div>
</div>
<script type="text/javascript">
//<![CDATA[
  en4.core.runonce.add(function() {
    new Uploader('upload_file', {
      uploadLinkClass : 'buttonlink icon_music_new',
      uploadLinkTitle : '<?php echo $this->translate("Add Music");?>',
      uploadLinkDesc : '<?php echo $this->translate("_MUSIC_UPLOAD_DESCRIPTION");?>'
    });
    //$('save-wrapper').inject($('art-wrapper'), 'after');

    // IMPORT SONGS INTO FORM
    if ($$('#music_songlist li.file').length) {
      $$('#music_songlist li.file').inject($('uploaded-file-list'));
      $$('#uploaded-file-list li span.file-name').setStyle('cursor', 'move');
      $('uploaded-file-list').setStyle('display', 'block');
      $('remove_all_files').setStyle('display', 'inline');
    }

    // SORTABLE PLAYLIST
    new Sortables('uploaded-file-list', {
      contrain: false,
      clone: true,
      handle: 'span',
      opacity: 0.5,
      revert: true,
      onComplete: function(){
        new Request.JSON({
          url: '<?php echo $this->url(array('controller'=>'playlist','action'=>'sort'), 'music_extended') ?>',
          noCache: true,
          data: {
            'format': 'json',
            'playlist_id': <?php echo $this->playlist->playlist_id ?>,
            'order': this.serialize().toString()
          }
        }).send();
      }
    });

    // RENAME SONG
    $$('a.song_action_rename').addEvent('click', function(){
      var origTitle = $(this).getParent('li').getElement('.file-name').get('text')
          origTitle = origTitle.substring(0, origTitle.length-6);
      var newTitle  = prompt('<?php echo $this->translate('What is the title of this song?') ?>', origTitle);
      var song_id   = $(this).getParent('li').id.split(/_/);
          song_id   = song_id[ song_id.length-1 ];

      if (newTitle && newTitle.length > 0) {
        newTitle = newTitle.substring(0, 60);
        $(this).getParent('li').getElement('.file-name').set('text', newTitle);
        new Request({
          url: '<?php echo $this->url(array('controller'=>'song','action'=>'rename'), 'music_extended') ?>',
          data: {
            'format': 'json',
            'song_id': song_id,
            'playlist_id': <?php echo $this->playlist->playlist_id ?>,
            'title': newTitle
          }
        }).send();
      }
      return false;
    });

    // REMOVE/DELETE SONG FROM PLAYLIST
    $$('a.file-remove').addEvent('click', function() {
      deleteFile($(this));
    });

  });

  var deleteFile = function (el) {
    var song_id = el.get('data-file_id');
    el.getParent('li').destroy();
    new Request.JSON({
      url: '<?php echo $this->url(array('controller'=>'song','action'=>'delete'), 'music_extended') ?>',
      data: {
        'format': 'json',
        'song_id': song_id,
        'playlist_id': <?php echo $this->playlist->playlist_id ?>
      }
    }).send();
  }
//]]>
</script>


<script type="text/javascript">
  $$('.core_main_music').getParent().addClass('active');
</script>
