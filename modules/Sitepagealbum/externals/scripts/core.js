/* $Id: core.js 2011-05-05 9:40:21Z SocialEngineAddOns $ */

en4.sitepagealbum = {

  composer : false,

  getComposer : function(){ 
    if( !this.composer ){
      this.composer = new en4.sitepagealbum.compose();
    }

    return this.composer;
  }

};



en4.sitepagealbum.compose = new Class({

  Extends : en4.activity.compose.icompose,

  name : 'sitepagephoto',

  active : false,

  options : {},

  frame : false,

  photo_id : false,

  initialize : function(element, options){ 
    if( !element ) element = $('activity-compose-sitepagephoto');
    this.parent(element, options);
  },
  
  activate : function(){
    this.parent();
    this.element.style.display = '';
    $('activity-compose-sitepagephoto-input').style.display = '';
    $('activity-compose-sitepagephoto-loading').style.display = 'none';
    $('activity-compose-sitepagephoto-preview').style.display = 'none';
    $('activity-form').addEvent('beforesubmit', this.checkSubmit.bind(this));
    this.active = true;

    // @todo this is a hack
    $('activity-post-submit').style.display = 'none';
  },

  deactivate : function(){
    if( !this.active ) return;
    this.active = false
    this.photo_id = false;
    if( this.frame ) this.frame.destroy();
    this.frame = false;
    $('activity-compose-sitepagephoto-preview').empty();
    $('activity-compose-sitepagephoto-input').style.display = '';
    this.element.style.display = 'none';
    $('activity-form').removeEvent('submit', this.checkSubmit.bind(this));;

    // @todo this is a hack
    $('activity-post-submit').style.display = 'block';
    $('activity-compose-sitepagephoto-activate').style.display = '';
    $('activity-compose-link-activate').style.display = '';
  },

  process : function(){
    if( this.photo_id ) return;
    
    if( !this.frame ){
      this.frame = new IFrame({
        src : 'about:blank',
        name : 'albumSitepageComposeFrame',
        styles : {
          display : 'none'
        }
      });
      this.frame.inject(this.element);
    }

    $('activity-compose-sitepagephoto-input').style.display = 'none';
    $('activity-compose-sitepagephoto-loading').style.display = '';
    $('activity-compose-sitepagephoto-form').target = 'albumSitepageComposeFrame';
    $('activity-compose-sitepagephoto-form').submit();
  },

  processResponse : function(responseObject){
    if( this.photo_id ) return;
    
    (new Element('img', {
      src : responseObject.src,
      styles : {
        //'max-width' : '100px'
      }
    })).inject($('activity-compose-sitepagephoto-preview'));
    $('activity-compose-sitepagephoto-loading').style.display = 'none';
    $('activity-compose-sitepagephoto-preview').style.display = '';
    this.photo_id = responseObject.photo_id;

    // @todo this is a hack
    $('activity-post-submit').style.display = 'block';
    $('activity-compose-sitepagephoto-activate').style.display = 'none';
    $('activity-compose-link-activate').style.display = 'none';
  },

  checkSubmit : function(event)
  {
    if( this.active && this.photo_id )
    {
      //event.stop();
      $('activity-form').attachment_type.value = 'sitepage_photo';
      $('activity-form').attachment_id.value = this.photo_id;
    }
  }
  
});
