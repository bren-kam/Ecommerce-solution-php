
var sparrow=function(context){$('input[tmpval],textarea[tmpval]',context).each(function(){$(this).focus(function(){if($(this).val()==$(this).attr('tmpval'))
$(this).val('').removeClass('tmpval');}).blur(function(){var value=$(this).val(),tmpValue=$(this).attr('tmpval');if(0==value.length||value==tmpValue)
$(this).val(tmpValue).addClass('tmpval');});if(!$(this).val().length)
$(this).val($(this).attr('tmpval')).addClass('tmpval');});var tables=$('table[ajax],table.dt',context);if(tables.length)
head.js('/resources/js_single/?f=jquery.datatables',function(){tables.addClass('dt').each(function(){var aPerPage=$(this).attr('perPage').split(','),opts='',ths=$(this).find('th').append('<img src="/images/trans.gif" width="9" height="8" />'),sorting=new Array(),columns=new Array(),s='',c='',a=$(this).attr('ajax');for(var i in aPerPage){opts+='<option value="'+aPerPage[i]+'">'+aPerPage[i]+'</option>';}
if(ths.length){for(var i=0;i<ths.length;i++){if(s=$(ths[i]).attr('sort')){var direction=(-1==s.search('desc'))?'asc':'desc';sorting[s.replace(' '+direction,'')-1]=[i,direction];}
if(c=$(ths[i]).attr('column')){columns.push({'sType':c});}else{columns.push(null);}}}else{sorting=[[0,'asc']];}
var settings={bAutoWidth:false,iDisplayLength:parseInt(aPerPage[0]),oLanguage:{sLengthMenu:'<select>'+opts+'</select>',sInfo:"_START_ - _END_ of _TOTAL_"},aaSorting:sorting,aoColumns:columns,fnDrawCallback:function(){sparrow($(this).find('tr:last').addClass('last').end());},sDom:'<"top"lfr>t<"bottom"pi>'};if(a)
settings.bProcessing=1,settings.bServerSide=1,settings.sAjaxSource=a;$(this).dataTable(settings);});});var dialogs=$('a[rel=dialog]',context);if(dialogs.length)
head.js('/resources/js_single/?f=jquery.boxy',function(){dialogs.click(function(e){e.preventDefault();var dialogData=$(this).attr('href').split('#'),content=$('#'+dialogData[1]),settings={title:$(this).attr('title'),behaviours:sparrow};if(content.length&&'0'!=$(this).attr('cache')){new Boxy(content,settings);}else{if(content.length)
content.remove();$('body').append('<div id="'+dialogData[1]+'" class="dialog" />');content=$('#'+dialogData[1]);content.load(dialogData[0],function(){new Boxy(content,settings);});}});});$('a[ajax]',context).click(function(e){e.preventDefault();var confirmQuestion=$(this).attr('confirm');if(confirmQuestion&&!confirm(confirmQuestion))
return
$.get($(this).attr('href'),ajaxResponse,'json');}).removeAttr('ajax');var RTEs=$('textarea[rte]',context);if(RTEs.length)
head.js('/ckeditor/ckeditor.js','/ckeditor/adapters/jquery.js',function(){RTEs.ckeditor({autoGrow_minHeight:100,resize_minHeight:100,height:100,toolbar:[['Bold','Italic','Underline'],['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],['NumberedList','BulletedList','Table'],['Format'],['Link','Unlink'],['Source']]});});var ajaxForms=$('form[ajax]',context);if(ajaxForms.length)
head.js('/resources/js_single/?f=jquery.form',function(){ajaxForms.ajaxForm({dataType:'json',success:ajaxResponse});});}
$.fn.sparrow=function(context){sparrow($(this));}
head.ready(function(){sparrow($('body'));});function ajaxResponse(response){if(response['success']){if(response['refresh']){window.location=window.location;}else if('object'==typeof(response['jquery'])){head.js('/resources/js_single/?f=jquery.php',function(){php(response['jquery']);});}}else{if(response['error'])
alert(response['error']);}}