
jQuery(postLoad);function postLoad($){$('#cbEnableFreeShipping').live('click',setFreeShipping);$('#tFreeShippingQuantity').live('change',setFreeShipping);}
function setFreeShipping(){$.post('/ajax/shopping-cart/shipping/set-free/',{_nonce:$('#free_shipping_nonce').val(),c:$("#cbEnableFreeShipping").attr('checked'),q:$("#tFreeShippingQuantity").val()},ajaxResponse,'json');}var sparrow=function(a){$("input[tmpval],textarea[tmpval]",a).each(function(){$(this).focus(function(){$(this).val()==$(this).attr("tmpval")&&$(this).val("").removeClass("tmpval")}).blur(function(){var b=$(this).val(),a=$(this).attr("tmpval");(0==b.length||b==a)&&$(this).val(a).addClass("tmpval")});$(this).val().length||$(this).val($(this).attr("tmpval")).addClass("tmpval")});var e=$("table[ajax],table.dt",a);e.length&&head.js("/js2/?f=jquery.datatables",function(){e.addClass("dt").each(function(){var b=$(this).attr("perPage").split(","),a="",d=$(this).find("th").append('<img src="/images/trans.gif" width="10" height="8" />'),g=[],h=[],i="",e="",f=$(this).attr("ajax"),c;for(c in b)a+='<option value="'+b[c]+'">'+b[c]+"</option>";if(d.length)for(c=0;c<d.length;c++){if(i=$(d[c]).attr("sort")){var j=-1==i.search("desc")?"asc":"desc";g[i.replace(" desc","")-1]=[c,j]}(e=$(d[c]).attr("column"))?h.push({sType:e}):h.push(null)}else g=[[0,"asc"]];b={bAutoWidth:!1,iDisplayLength:parseInt(b[0]),oLanguage:{sLengthMenu:"Rows: <select>"+a+"</select>",sInfo:"_START_ - _END_ of _TOTAL_"},aaSorting:g,aoColumns:h,fnDrawCallback:function(){sparrow($(this).find("tr:last").addClass("last").end())},sDom:'<"top"Tlfr>t<"bottom"pi>'};if(f)b.bProcessing=1,b.bServerSide=1,b.sAjaxSource=f;$(this).dataTable(b)})});var f=$("a[rel=dialog]",a);f.length&&head.js("/js2/?f=jquery.boxy",function(){f.click(function(b){b.preventDefault();var b=$(this).attr("href").split("#"),a=$("#"+b[1]),d={title:$(this).attr("title"),behaviours:sparrow};a.length&&"0"!=$(this).attr("cache")?new Boxy(a,d):($("body").append('<div id="'+b[1]+'" class="dialog" />'),a=$("#"+b[1]),a.load(b[0],function(){new Boxy(a,d)}))})});$("a[ajax]",a).click(function(a){a.preventDefault();a=$(this).attr("confirm");(!a||confirm(a))&&$.get($(this).attr("href"),ajaxResponse,"json")}).removeAttr("ajax");"function"==typeof mammoth&&mammoth(a)};$.fn.sparrow=sparrow;head.ready(function(){sparrow($("body"))});function ajaxResponse(a){a.success?a.refresh?window.location=window.location:"object"==typeof a.jquery&&head.js("/js2/?f=jquery.php",function(){php(a.jquery)}):a.error&&alert(a.error)};jQuery(function(){$("#aTicket").click(function(){var a=$(this);a.hasClass("loaded")?new Boxy($("#dTicketPopup"),{title:a.attr("title")}):head.js("/js2/?f=jquery.boxy","/js2/?f=jquery.form","/js2/?f=swfobject","/js2/?f=jquery.uploadify",function(){a.addClass("loaded");new Boxy($("#dTicketPopup"),{title:a.attr("title")});$("#fCreateTicket").addClass("ajax").ajaxForm({dataType:"json",beforeSubmit:function(){var a=$("#tTicketSummary"),d=a.val(),b=$("#taTicket"),c=b.val();if(!d.length||d==a.attr("tmpval"))return alert(a.attr("error")),!1;return!c.length||c==b.attr("tmpval")?(alert(b.attr("error")),!1):!0},success:ajaxResponse});$("#fTicketUpload").uploadify({auto:!0,multi:!0,displayData:"speed",buttonImg:"/images/buttons/add-attachment.png",cancelImg:"/images/icons/x.png",fileExt:"*.pdf;*.mov;*.wmv;*.flv;*.swf;*.f4v;*.mp4;*.avi;*.mp3;*.aif;*.wma;*.wav;*.csv;*.doc;*.docx;*.rtf;*.xls;*.xlsx;*.wpd;*.txt;*.wps;*.pps;*.ppt;*.wks;*.bmp;*.gif;*.jpg;*.jpeg;*.png;*.psd;*.eps;*.tif;*.zip;*.7z;*.rar;*.zipx;*.aiff;*.odt;",fileDesc:"Valid File Formats",onComplete:function(a,d,b,c){ajaxResponse($.parseJSON(c))},onSelect:function(){$("#fTicketUpload").uploadifySettings("scriptData",{_nonce:$("#_ajax_ticket_upload").val(),uid:$("#hUserID").val(),wid:$("#hTicketWebsiteID").val(),tid:$("#hTicketID").val()});return!0},sizeLimit:6291456,script:"/ajax/support/ticket-upload/",uploader:"/media/flash/uploadify.swf",width:124,height:34})})})});