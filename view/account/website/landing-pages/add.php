<form name="fAddPage" id="fAddPage" action="/website/landing-pages/add/" method="post" role="form">

    <!-- Modal -->
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="modalLabel">Create new Landing Page</h4>
            </div>
            <div class="modal-body">
                <?php echo $form ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </div>
    </div>

</form>

<script>
    $('#tTitle').change( function() {
        if ( $('#tSlug').val() == '' ) {
            $('#tSlug').val( $('#tTitle').val().slug() );
        }
    } );
</script>