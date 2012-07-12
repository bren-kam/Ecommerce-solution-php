// When the page has loaded
head.js( '/js2/?f=jquery.datatables', function() {
	// Create the functionality to narrow down by parent category
	$('#sParentCategoryID').change( function() {
		$('#tWebsiteCategories').dataTable().fnDraw();
	});

    // Create the data table
    $('#tWebsiteCategories:not(.dt)').dataTable({
        aaSorting: [[0,'asc']],
        bAutoWidth: false,
        bProcessing : 1,
        bServerSide : 1,
        iDisplayLength : 20,
        sAjaxSource : '/ajax/website/list-categories/',
        sDom : '<"top"lr>t<"bottom"pi>',
        oLanguage: {
                sLengthMenu: 'Rows: <select><option value="20">20</option><option value="50">50</option><option value="100">100</option></select>',
                sInfo: "_START_ - _END_ of _TOTAL_"
        },
        fnDrawCallback : function() {
            // Run Sparrow on new content and add the class last to the last row
            sparrow( $(this).find('tr:last').addClass('last').end() );
        },
        fnServerData: function ( sSource, aoData, fnCallback ) {
            aoData.push({ name : 'pcid', value : $('#sParentCategoryID').val() });

            // Get the data
            $.ajax({
                url: sSource,
                dataType: 'json',
                data: aoData,
                success: fnCallback
            });
        },
    });

    setTimeout( function() {
        $('#tWebsiteCategories').addClass('dt');
    }, 500 );
});