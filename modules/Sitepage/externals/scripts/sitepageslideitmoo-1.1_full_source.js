
/* $Id: slideitmoo-1.1_full_source.js  $ */
/**
	SlideItMoo v1.1 - Image slider
	(c) 2007-2010 Constantin Boiangiu <http://www.php-help.ro>
	MIT-style license.
	
	Changes from version 1.0
	- added continuous navigation
	- changed the navigation from Fx.Scroll to Fx.Morph
	- added new parameters: itemsSelector: pass the CSS class for divs
	- itemWidth: for elements with margin/padding pass their width including margin/padding
	
	Updates ( August 4th 2009 )
	- added new parameter 'elemsSlide'. When this is set to a value lower that the actual number of elements in HTML, it will slide at once that number of elements when navigation clicked. Default: null
	- added onChange event that returns the index of the current element
	
	Updates ( January 12th 2010 )
	- vertical sliding available. First, set your HTML to display vertically and set itemHeight:height of individual items ( including padding, border and so on ) and slideVertical:true
	- navigators ( forward/back ) no longer added by script. Instead, add them into overallContainer making their display from CSS and after add the CSS selector class to navs parameter
		IE: navs:{ 
				fwd:'.Sitepage_SlideItMoo_forward',
				bk:'.Sitepage_SlideItMoo_back'
			}
	- new method available resetAll(). When called, this will reset the previous settings and restart the script. Useful if you change slider content on-the-fly
	- new method available to stop autoSlide ( stopAutoSlide() ). To start autoslide back, use startAutoSlide()
**/
var SocialengineSlideItMoo = new Class({
	
  Implements: [Events,Options],
  options: {
    overallContainer: null,/* outer container, contains fwd/back buttons and container for thumbnails */
    elementScrolled: null, /* has a set width/height with overflow hidden to allow sliding of elements */
    thumbsContainer: null,	/* actual thumbnails container */
    itemsSelector: null, /* css class for inner elements ( ie: .Sitepage_SlideItMoo_element ) */
    itemsVisible:4, /* number of elements visible at once */
    elemsSlide: null, /* number of elements that slide at once */
    itemWidth: null, /* single element width */
    itemHeight: null, /* single element height */
       navs:{ /* starting this version, you'll need to put your back/forward navigators in your HTML */
      fwd:null, /* forward button CSS selector */
      bk:null /* back button CSS selector */
    },
    call_count:null,
    forward_link:null,
    fwdbck_click:null,
    total:null,
    limit:null,
    curnt_limit:null,
    category_id:null,
    slide_element_limit:null,
    no_of_row:null,
    in_one_row:null,
    startindex:null,
    slideVertical: false, /* vertical sliding enabled */
    showControls:1, /* show forward/back controls */
    transition: Fx.Transitions.linear, /* transition */
    duration: 8000, /* transition duration */
    direction: 1, /* sliding direction ( 1: enter from left/top; -1:enter from right/bottom ) */
    autoSlide: false, /* auto slide - as milliseconds ( ie: 10000 = 10 seconds ) */
    mouseWheelNav: false, /* enable mouse wheel nav */
    startIndex: null
  /*onChange: $empty*/
  },
	
  initialize: function(options) {
    this.setOptions(options);
    /* all elements are identified on CSS selector (itemsSelector) */
    this.elements = $(this.options.thumbsContainer).getElements(this.options.itemsSelector);
    this.totalElements = this.elements.length;
    if( this.totalElements <= this.options.itemsVisible ) return;
    // width of thumbsContainer children
    var defaultSize = this.elements[0].getSize();
    this.elementWidth = this.options.itemWidth || defaultSize.x;
    this.elementHeight = this.options.itemHeight || defaultSize.y;
    this.currentElement = 0;
    this.direction = this.options.direction;
    this.autoSlideTotal = this.options.autoSlide + this.options.duration;
    if( this.options.elemsSlide == 1 ) this.options.elemsSlide = null;
    this.begin();
  },
		
  begin: function(){
    /* if navigation is needed and enabled, add it */
    this.addControls();
				
    // resizes the container div's according to the number of itemsVisible thumbnails
    this.setContainersSize();
		
    this.myFx = new Fx.Tween(this.options.thumbsContainer, {
      property: (this.options.slideVertical ? 'margin-top':'margin-left'),
      wait: true,
      transition: this.options.transition,
      duration: this.options.duration
    });
				
    /* if autoSlide is not set, scoll on mouse wheel */
    if( this.options.mouseWheelNav && !this.options.autoSlide ){
      $(this.options.thumbsContainer).addEvent('mousewheel', function(ev){
        new Event(ev).stop();
        this.slide(-ev.wheel);
      }.bind(this));
    }
		
    /* start index element */
    if( this.options.startIndex && this.options.startIndex > 0 && this.options.startIndex < this.elements.length ){
      for( var t = 1; t < this.options.startIndex; t++ )
        this.rearange();
    }
		
    if( this.options.autoSlide && this.elements.length > this.options.itemsVisible )
      this.startAutoSlide();
  },
  /* resets the whole slider in case content changes */
  resetAll: function(){
    //$(this.options.overallContainer).removeProperty('style');
    //$(this.options.elementScrolled).removeProperty('style');
    //$(this.options.thumbsContainer).removeProperty('style');
    this.stopAutoSlide();
    if( $defined( this.fwd ) ){
    //this.fwd.dispose();
    //this.bkwd.dispose();
    }
    this.initialize();
  },
  /* sets the containers width to leave visible only the specified number of elements */
  setContainersSize: function(){
    var overallSize = {};
    var scrollSize = {};
    var thumbsSize = {};
		
    if( this.options.slideVertical ){
      //overallSize.height = this.options.itemsVisible * this.elementHeight + 50 * this.options.showControls;
      scrollSize.height = this.options.itemsVisible * this.elementHeight;
      thumbsSize.height = this.totalElements * (this.elementHeight + 10);
    }else{
      /* if navigation is enabled, add the width to the overall size */
      var navsSize = 0;
      if( this.options.showControls ){
        var s1 = $(this.options.foward).getSize();
        var s2 = $(this.options.bck).getSize();
        var navsSize = s1.x+s2.x;
      }
      overallSize.width = this.options.itemsVisible * this.elementWidth + navsSize;
      scrollSize.width = this.options.itemsVisible * this.elementWidth;
      thumbsSize.width = this.totalElements * (this.elementWidth + 10);
    }
    //$(this.options.overallContainer).set({
    //      styles : overallSize
    //    });
    $(this.options.elementScrolled).set({
      styles : scrollSize
    });
    $(this.options.thumbsContainer).set({
      styles : thumbsSize
    });
  },
  /* adds forward/back buttons */
  addControls: function(){
    if( !this.options.showControls || this.elements.length <= this.options.itemsVisible ) return;
		
    this.fwd = $(this.options.overallContainer).getElement(this.options.navs.fwd);
    this.bkwd = $(this.options.overallContainer).getElement(this.options.navs.bk);
		
    if( this.fwd )
      //this.fwd.addEvent('click', this.sendajax.pass(1, this));
      if( this.bkwd ) {
					
  //this.bkwd.addEvent('click', this.slide.pass(-1, this));
  }
  },
  /* slides elements */
  slide: function( direction ){
		
    if(this.started) return;
    this.direction = direction ? direction : this.direction;
    var currentIndex = this.currentIndex();
    /* if multiple elements are to be skipped (elemsSlide > 1), calculate the ending element */
    if( this.options.elemsSlide && this.options.elemsSlide>1 && this.endingElem==null ){
      this.endingElem = this.currentElement;
      for(var i = 0; i < this.options.elemsSlide; i++ ){
        this.endingElem += direction;
        if( this.endingElem >= this.totalElements ) this.endingElem = 0;
        if( this.endingElem < 0 ) this.endingElem = this.totalElements-1;
      }
    }
		
    var s = new Hash();
    var fxDist = 0;
    if( this.options.slideVertical ){
      s.include('margin-top', -this.elementHeight);
      fxDist = this.direction == 1 ? -this.elementHeight : 0;
    }else{
      s.include('margin-left', -this.elementWidth);
      fxDist = this.direction == 1 ? -this.elementWidth : 0;
    }
		
    if( this.direction == -1 ){
      this.rearange();
      if( this.options.slideVertical ) $(this.options.thumbsContainer).setStyles({
        'margin-top': -this.elementHeight
      });
      else $(this.options.thumbsContainer).setStyles({
        'margin-left': -this.elementWidth
      });
    }
    this.started = true;
		
    this.myFx.start( fxDist ).chain( function(){
      this.rearange(true);
      if(this.options.elemsSlide){
        if( this.endingElem != this.currentElement ){
          if( this.options.autoSlide )
            this.stopAutoSlide();
          this.slide(this.direction);
        }
        else {
          if( this.options.autoSlide )
            this.startAutoSlide();
          this.endingElem = null;
        }
      }
    }.bind(this)  );
		
    this.fireEvent('onChange', currentIndex);
		
  },
  /* rearanges elements for continuous navigation */
  rearange: function( rerun ){
		
    if(rerun) this.started = false;
    if( rerun && this.direction == -1 ) return;
		
    this.currentElement = this.currentIndex( this.direction );
		
   if( this.options.slideVertical ) $(this.options.thumbsContainer).setStyles({
      'margin-top': 0
    });
    else $(this.options.thumbsContainer).setStyles({
      'margin-left': 0
    });
		
    if( this.currentElement == 1 && this.direction == 1 ){
      this.elements[0].injectAfter(this.elements[this.totalElements-1]);
      return;
    }
    if( (this.currentElement == 0 && this.direction ==1) || (this.direction==-1 && this.currentElement == this.totalElements-1) ){
      this.rearrangeElement( this.elements.getLast(), this.direction == 1 ? this.elements[this.totalElements-2] : this.elements[0]);
      return;
    }
		
    if( this.direction == 1 ) this.rearrangeElement( this.elements[this.currentElement-1], this.elements[this.currentElement-2]);
    else this.rearrangeElement( this.elements[this.currentElement], this.elements[this.currentElement+1]);
  },
  /* rearanges a single element for continuous navigation */
  rearrangeElement: function( element , indicator ){
    this.direction == 1 ? element.injectAfter(indicator) : element.injectBefore(indicator);
  },
  /* determines the current index in element list */
  currentIndex: function(){
    var elemIndex = null;
    switch( this.direction ){
      /* forward */
      case 1:
        elemIndex = this.currentElement >= this.totalElements-1 ? 0 : this.currentElement + this.direction;
        break;
      /* backwards */
      case -1:
        elemIndex = this.currentElement == 0 ? this.totalElements - 1 : this.currentElement + this.direction;
        break;
    }
    return elemIndex;
  },
  /* starts auto sliding */
  startAutoSlide: function(){
    this.startIt = this.slide.bind(this).pass(this.direction|1);
    this.autoSlide = this.startIt.periodical(this.autoSlideTotal, this);
    this.isRunning = true;
    this.elements.addEvents({
      'mouseenter':function(){
        $clear(this.autoSlide);
        this.isRunning = false;
      }.bind(this),
      'mouseleave':function(){
        this.autoSlide = this.startIt.periodical(this.autoSlideTotal, this);
        this.isRunning = true;
      }.bind(this)
    })
  },
  /* stops auto sliding */
  stopAutoSlide: function(){
    $clear(this.autoSlide);
    this.isRunning = false;
  },


  sendajax: function (direction, obj,module_name,urlcarosel) {
    if (this.options.fwdbck_click == 1 ) {
      this.options.fwdbck_click = 2; 
      var obj = this;
      var startindex_temp = 0;
			
      if (this.options.call_count == 1) {
        this.options.call_count = 2;
        if (direction == 1) {
		
          orderby = 'ASC';
          if (this.options.startindex == -1) {
            this.options.startindex = 0;
          }
          if (this.options.forward_link == 1) {
						
            this.options.startindex = parseInt(this.options.startindex) + parseInt(this.options.slide_element_limit);
            if (this.options.startindex > this.options.total) {
              this.options.startindex = parseInt(this.options.startindex) - parseInt(this.options.slide_element_limit);
            }
          }
          else {
            this.options.forward_link = 1;
          }
          startindex_temp = this.options.startindex;
			
        }
        else {
          orderby = 'DESC';
          if (this.options.startindex == -1 ) {
            this.options.startindex = 0;
					
          }
          else {
            if (this.options.forward_link == 2) {
              this.options.startindex = parseInt(this.options.startindex) - parseInt(this.options.slide_element_limit);
              if (this.options.startindex <= -1) {
                this.options.startindex = 0;
              }
				
            }
            else {
              this.options.forward_link = 2;
            }
				
          }
          startindex_temp = this.options.startindex;
        }

        if(direction ==1){
          document.getElementById(module_name+'_SlideItMoo_forward').style.display= 'none';
          document.getElementById(module_name+'_SlideItMoo_forward_disable').style.display= 'none';
          document.getElementById(module_name+'_SlideItMoo_forward_loding').style.display= 'block';
        }else{
          document.getElementById(module_name+'_SlideItMoo_back').style.display= 'none';
          document.getElementById(module_name+'_SlideItMoo_back_disable').style.display= 'none';
          document.getElementById(module_name+'_SlideItMoo_back_loding').style.display= 'block';

        }
        var obj = this;
        var request = new Request.HTML({
          url : urlcarosel,
          method: 'GET',
          data : {
            format : 'html',
            'task': 'ajax',
            'startindex' : startindex_temp,
            'totalItem':this.options.total,
            'itemsVisible':this.options.curnt_limit,
            'direction' : direction,
            'category_id' : this.options.category_id,
            'inOneRow':this.options.in_one_row,
            'noOfRow' : this.options.no_of_row
          },
          'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
          {	
            $(module_name+'_SlideItMoo_items').innerHTML = responseHTML;							
            obj.custom_sliditmoo (obj, direction, startindex_temp, this.options.forward_link);
            document.getElementById(module_name+'_SlideItMoo_forward_loding').style.display= 'none';
            document.getElementById(module_name+'_SlideItMoo_back_loding').style.display= 'none';
            if(module_name == 'Sitepagevideo' && en4.sitevideoview)
              en4.sitevideoview.attachClickEvent(Array('item_photo_sitepagevideo_video','sitepagevideo_title'));        
          }
        });
        request.send();
      }
    }
  },

  custom_sliditmoo: function (obj, direction) {
  var obj_sliditmoo;
    obj_sliditmoo = new SocialengineSlideItMoo({
      module : this.options.module,
      forward_link:this.options.forward_link,
      fwdbck_click:this.options.fwdbck_click,
			slide_element_limit:this.options.slide_element_limit,
			startindex:this.options.startindex,
			in_one_row:this.options.in_one_row,
			no_of_row:this.options.no_of_row,
			curnt_limit:this.options.curnt_limit,
      category_id:this.options.category_id,
			total:this.options.total,
			limit:this.options.limit,
      overallContainer: this.options.module+'_SlideItMoo_outer',
      elementScrolled: this.options.module+'_SlideItMoo_inner',
      thumbsContainer: this.options.module+'_SlideItMoo_items',
      slideVertical: this.options.slideVertical,
      itemsVisible:1,
      elemsSlide:1,
      call_count:this.options.call_count,
      duration:this.options.duration,
      foward:this.options.module+'_SlideItMoo_forward',
      bck:this.options.module+'_SlideItMoo_back',
      itemsSelector: '.'+this.options.module+'_SlideItMoo_element',
      itemWidth:this.options.itemWidth,
      itemHeight:this.options.itemHeight,
      showControls:1,
      startIndex:1,
      navs:{ /* starting this version, you'll need to put your back/forward navigators in your HTML */
				fwd:'.'+this.options.module+'_SlideItMoo_forward', /* forward button CSS selector */
				bk:'.'+this.options.module+'_SlideItMoo_back' /* back button CSS selector */
			},
      onChange: function(index) { this.options.call_count = 1;
      }
    });

    obj_sliditmoo.slide(direction, obj_sliditmoo);
    var start_Index_D=parseInt(this.options.curnt_limit) * parseInt(this.options.startindex);
    if(start_Index_D<=0 && direction== -1){
         // hidding back button
         document.getElementById(this.options.module+'_SlideItMoo_back').style.display= 'none';
         document.getElementById(this.options.module+'_SlideItMoo_back_disable').style.display= 'block';
      }else{
        // vissible back button
        document.getElementById(this.options.module+'_SlideItMoo_back').style.display= 'block';
        document.getElementById(this.options.module+'_SlideItMoo_back_disable').style.display= 'none';

      }

        if(((start_Index_D>(this.options.total-this.options.limit)|| (start_Index_D>=(this.options.total-this.options.limit))) && direction== 1) || ((start_Index_D>=(this.options.total-this.options.curnt_limit)) && direction==  -1)){
         // hidding forward button
          document.getElementById(this.options.module+'_SlideItMoo_forward').style.display= 'none';
          document.getElementById(this.options.module+'_SlideItMoo_forward_disable').style.display= 'block';

      }else{
         // vissible forward button
           document.getElementById(this.options.module+'_SlideItMoo_forward').style.display= 'block';
           document.getElementById(this.options.module+'_SlideItMoo_forward_disable').style.display= 'none';
      }
     this.options.fwdbck_click = 1;
  }

})
