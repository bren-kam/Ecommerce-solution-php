<?php
/**
 * @package Grey Suit Retail
 * @page CSS | Customize
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $less
 * @var Account $account
 * @var string|bool $unlocked_less
 */
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                CSS
            </header>

            <div class="panel-body">

                <?php
                nonce::field('save_less');
                if ( $unlocked_less ): ?>

                    <h3>Base Theme CSS (Uneditable)</h3>
                    <div id="core-container">
                        <div id="core"><?php echo $unlocked_less; ?></div>
                    </div>

                    <h3>CSS</h3>

                <?php endif; ?>

                <div id="editor-container">
                    <div id="editor"><?php echo $less; ?></div>
                </div>

                <p><a href="/website/save-less/" class="btn btn-primary btn-lg" id="save-less" title="<?php echo _('Save'); ?>"><?php echo _('Save'); ?></a></p>

            </div>
        </section>
    </div>
</div>
