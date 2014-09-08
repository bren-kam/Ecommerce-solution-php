<?php
/**
 * @package Grey Suit Retail
 * @page Import | Subscribers | Email Marketing
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var EmailList[] $email_lists
 */
?>
<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Import Subscribers
            </header>

            <div class="panel-body">

                <div id="step-1">
                    <p><?php echo _('On this page you can import a list of subscribers who have requested you email them information.'); ?></p>
                    <p><?php echo _('Please make your spreadsheet layout match the example below.'); ?></p>
                    <p><?php echo _('Example:'); ?></p>
                    <table class="table">
                        <tr>
                            <th><?php echo _('Email'); ?></th>
                            <th><?php echo _('Name'); ?></th>
                        </tr>
                        <tr>
                            <td><?php echo _('email@example.com'); ?></td>
                            <td><?php echo _('John Doe'); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo _('jane@doe.com'); ?></td>
                            <td><?php echo _('Jane'); ?></td>
                        </tr>
                        <tr>
                            <td>...</td>
                            <td>...</td>
                        </tr>
                    </table>

                    <?php foreach ( $email_lists as $el ): ?>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="email-list" value="<?php echo $el->id; ?>" <?php if ( 0 == $el->category_id ) echo 'checked'; ?> />
                                <?php echo $el->name; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>

                    <p>
                        <button type="button" id="upload" class="btn btn-primary">Import</button>

                        <div class="progress progress-sm hidden" id="upload-progress">
                            <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>

                        <!-- Real Uploader -->
                        <div id="uploader"></div>
                        <?php nonce::field( 'import_subscribers', '_import_subscribers' ) ?>
                    </p>

                </div>

                <div id="step-2" class="hidden">
                    <p><?php echo _('Please verify the first email addresses below are correct:'); ?></p>
                    <table id="subscriber-list" class="table">
                        <thead>
                            <tr>
                                <th><?php echo _('Email'); ?></th>
                                <th><?php echo _('Name'); ?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <form action="/email-marketing/subscribers/import/" method="post">
                        <?php nonce::field( 'import' ); ?>
                        <input type="hidden" name="hEmailLists" id="hEmailLists" />
                        <button type="submit" class="btn btn-primary">Confirm Import</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>