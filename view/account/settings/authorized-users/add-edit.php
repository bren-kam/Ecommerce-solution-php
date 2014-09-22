<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <?php echo $auth_user_website->user_id ? 'Edit' : 'Add' ?> User
            </header>
            <div class="panel-body">
                <?php echo $form ?>
            </div>
        </section>
    </div>
</div>