<?php
/**
 * @package Grey Suit Retail
 * @page LESS | Companies
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Company $company
 */

nonce::field('save_less');
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                CSS/LESS: <?php echo $company->name ?>
            </header>

            <div class="panel-body">

                <div id="editor-container">
                    <div id="editor"><?php echo $company->less; ?></div>
                </div>

                <p>
                    <a href="<?php echo url::add_query_arg( 'cid', $company->id, '/accounts/companies/save-less/' ); ?>" class="btn btn-primary btn-lg" id="save-less" title="<?php echo _('Save'); ?>"><?php echo _('Save'); ?></a>
                </p>

            </div>
        </section>
    </div>
</div>
