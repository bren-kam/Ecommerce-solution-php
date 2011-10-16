function Boxy(a,b){this.boxy=$(Boxy.WRAPPER);$.data(this.boxy[0],"boxy",this);this.visible=!1;this.options=$.extend({},Boxy.DEFAULTS,b||{});if(this.options.modal)this.options=$.extend(this.options,{center:!0,draggable:!1});this.options.actuator&&$.data(this.options.actuator,"active.boxy",this);this.setContent(a||"<div></div>");this._setupTitleBar();this.boxy.css("display","none").appendTo(document.body);this.toTop();this.options.center&&Boxy._u(this.options.x,this.options.y)?this.center():this.moveTo(Boxy._u(this.options.x)? Boxy.DEFAULT_X:this.options.x,Boxy._u(this.options.y)?Boxy.DEFAULT_Y:this.options.y);this.options.show&&this.show()}Boxy.EF=function(){}; $.extend(Boxy,{WRAPPER:'<div class="boxy-wrapper"><div class="boxy-inner"></div></div>',DEFAULTS:{title:null,closeable:!0,draggable:!1,clone:!1,actuator:null,center:!0,show:!0,modal:!0,fixed:!0,closeText:"[close]",unloadOnHide:!1,clickToFront:!1,behaviours:Boxy.EF,afterDrop:Boxy.EF,afterShow:Boxy.EF,afterHide:Boxy.EF,beforeUnload:Boxy.EF,hideFade:!1,hideShrink:!1},IE6:$.browser.msie&&$.browser.version<7,DEFAULT_X:50,DEFAULT_Y:50,MODAL_OPACITY:0.7,zIndex:1337,dragConfigured:!1,resizeConfigured:!1, dragging:null,load:function(a,b){var b=b||{},c={url:a,type:"GET",dataType:"html",cache:!1,success:function(a){a=$(a);b.filter&&(a=$(b.filter,a));new Boxy(a,b)}};$.each(["type","cache"],function(){this in b&&(c[this]=b[this],delete b[this])});$.ajax(c)},loadImage:function(a,b){var c=new Image;c.onload=function(){new Boxy($('<div class="boxy-image-wrapper"/>').append(this),b)};c.src=a},get:function(a){a=$(a).parents(".boxy-wrapper");return a.length?$.data(a[0],"boxy"):null},linkedTo:function(a){return $.data(a, "active.boxy")},alert:function(a,b,c){return Boxy.ask(a,["OK"],b,c)},confirm:function(a,b,c){return Boxy.ask(a,["OK","Cancel"],function(a){a=="OK"&&b()},c)},ask:function(a,b,c,d){var d=$.extend({modal:!0,closeable:!1},d||{},{show:!0,unloadOnHide:!0}),a=$("<div></div>").append($('<div class="question"></div>').html(a)),e=$('<form class="answers"></form>');e.html($.map(Boxy._values(b),function(a){return"<input type='button' value='"+a+"' />"}).join(" "));$("input[type=button]",e).click(function(){var a= this;Boxy.get(this).hide(function(){c&&$.each(b,function(d,e){if(e==a.value)return c(b instanceof Array?e:d),!1})})});a.append(e);new Boxy(a,d)},isModalVisible:function(){return $(".boxy-modal-blackout").length>0},_u:function(){for(var a=0;a<arguments.length;a++)if(typeof arguments[a]!="undefined")return!1;return!0},_values:function(a){if(a instanceof Array)return a;var b=[],c;for(c in a)b.push(a[c]);return b},_handleResize:function(){$(".boxy-modal-blackout").css("display","none").css(Boxy._cssForOverlay()).css("display", "block")},_handleDrag:function(a){var b;(b=Boxy.dragging)&&b[0].boxy.css({left:a.pageX-b[1],top:a.pageY-b[2]})},_nextZ:function(){return Boxy.zIndex++},_viewport:function(){var a=document.documentElement,b=document.body,c=window;return $.extend($.browser.msie?{left:b.scrollLeft||a.scrollLeft,top:b.scrollTop||a.scrollTop}:{left:c.pageXOffset,top:c.pageYOffset},!Boxy._u(c.innerWidth)?{width:c.innerWidth,height:c.innerHeight}:!Boxy._u(a)&&!Boxy._u(a.clientWidth)&&a.clientWidth!=0?{width:a.clientWidth, height:a.clientHeight}:{width:b.clientWidth,height:b.clientHeight})},_setupModalResizing:function(){if(!Boxy.resizeConfigured){var a=$(window).resize(Boxy._handleResize);Boxy.IE6&&a.scroll(Boxy._handleResize);Boxy.resizeConfigured=!0}},_cssForOverlay:function(){return Boxy.IE6?Boxy._viewport():{width:"100%",height:$(document).height()}}}); Boxy.prototype={estimateSize:function(){this.boxy.css({visibility:"hidden",display:"block"});var a=this.getSize();this.boxy.css("display","none").css("visibility","visible");return a},getSize:function(){return[this.boxy.width(),this.boxy.height()]},getContentSize:function(){var a=this.getContent();return[a.width(),a.height()]},getPosition:function(){var a=this.boxy[0];return[a.offsetLeft,a.offsetTop]},getCenter:function(){var a=this.getPosition(),b=this.getSize();return[Math.floor(a[0]+b[0]/2),Math.floor(a[1]+ b[1]/2)]},getInner:function(){return $(".boxy-inner",this.boxy)},getContent:function(){return $(".boxy-content",this.boxy)},setContent:function(a){a=$(a).css({display:"block"}).addClass("boxy-content");this.options.clone&&(a=a.clone(!0));this.getContent().remove();this.getInner().append(a);this._setupDefaultBehaviours(a);this.options.behaviours.call(this,a);return this},moveTo:function(a,b){this.moveToX(a).moveToY(b);return this},moveToX:function(a){typeof a=="number"?this.boxy.css({left:a}):this.centerX(); return this},moveToY:function(a){typeof a=="number"?this.boxy.css({top:a}):this.centerY();return this},centerAt:function(a,b){var c=this[this.visible?"getSize":"estimateSize"]();typeof a=="number"&&this.moveToX(a-c[0]/2);typeof b=="number"&&this.moveToY(b-c[1]/2);return this},centerAtX:function(a){return this.centerAt(a,null)},centerAtY:function(a){return this.centerAt(null,a)},center:function(a){var b=Boxy._viewport(),c=this.options.fixed?[0,0]:[b.left,b.top];(!a||a=="x")&&this.centerAt(c[0]+b.width/ 2,null);(!a||a=="y")&&this.centerAt(null,c[1]+b.height/2);return this},centerX:function(){return this.center("x")},centerY:function(){return this.center("y")},resize:function(a,b,c){if(this.visible)return a=this._getBoundsForResize(a,b),this.boxy.css({left:a[0],top:a[1]}),this.getContent().css({width:a[2],height:a[3]}),c&&c(this),this},isVisible:function(){return this.visible},show:function(){if(!this.visible){if(this.options.modal){var a=this;Boxy._setupModalResizing();this.modalBlackout=$('<div class="boxy-modal-blackout"></div>').css($.extend(Boxy._cssForOverlay(), {zIndex:Boxy._nextZ(),opacity:Boxy.MODAL_OPACITY})).appendTo(document.body);this.toTop();this.options.closeable&&$(document.body).bind("keypress.boxy",function(b){if((b.which||b.keyCode)==27)a.hide(),$(document.body).unbind("keypress.boxy")})}this.getInner().stop().css({width:"",height:""});this.boxy.stop().css({opacity:1}).show();this.visible=!0;this.boxy.find(".close:first").focus();this._fire("afterShow");return this}},hide:function(a){if(this.visible)return this.options.modal&&($(document.body).unbind("keypress.boxy"), this.modalBlackout.animate({opacity:0},function(){$(this).remove()})),this.boxy.css({display:"none"}),this.visible=!1,this._fire("afterHide"),a&&a(this),this.options.unloadOnHide&&this.unload(),this},toggle:function(){this[this.visible?"hide":"show"]();return this},hideAndUnload:function(a){this.options.unloadOnHide=!0;this.hide(a);return this},unload:function(){this._fire("beforeUnload");this.boxy.remove();this.options.actuator&&$.data(this.options.actuator,"active.boxy",!1)},toTop:function(){this.boxy.css({zIndex:Boxy._nextZ()}); return this},getTitle:function(){return $("> .title-bar h2",this.getInner()).html()},setTitle:function(a){$("> .title-bar h2",this.getInner()).html(a);return this},_getBoundsForResize:function(a,b){var c=this.getContentSize(),c=[a-c[0],b-c[1]],d=this.getPosition();return[Math.max(d[0]-c[0]/2,0),Math.max(d[1]-c[1]/2,0),a,b]},_setupTitleBar:function(){if(this.options.title){var a=this,b=$('<div class="title-bar"></div>').html("<h2>"+this.options.title+"</h2>");this.options.closeable&&b.append($('<a href="javascript:;" class="close"></a>').html(this.options.closeText)); if(this.options.draggable){b[0].onselectstart=function(){return!1};b[0].unselectable="on";b[0].style.MozUserSelect="none";if(!Boxy.dragConfigured)$(document).mousemove(Boxy._handleDrag),Boxy.dragConfigured=!0;b.mousedown(function(b){a.toTop();Boxy.dragging=[a,b.pageX-a.boxy[0].offsetLeft,b.pageY-a.boxy[0].offsetTop];$(this).addClass("dragging")}).mouseup(function(){$(this).removeClass("dragging");Boxy.dragging=null;a._fire("afterDrop")})}this.getInner().prepend(b);this._setupDefaultBehaviours(b)}}, _setupDefaultBehaviours:function(a){var b=this;this.options.clickToFront&&a.click(function(){b.toTop()});$(".close",a).click(function(){b.hide();return!1}).mousedown(function(a){a.stopPropagation()})},_fire:function(a){this.options[a].call(this)}};