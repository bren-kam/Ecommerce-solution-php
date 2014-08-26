<?php
/**
 * @package Grey Suit Retail
 * @page Create | Campaigns | Email Marketing
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var User $user
 * @var EmailMessage $campaign
 * @var EmailList[] $email_lists
 * @var array $settings
 * @var string $timezone
 * @var string $server_timezone
 * @var EmailTemplate[] $templates
 * @var AccountFile[] $files
 * @var string $default_from
 * @var boolean $overwrite_from
 * @var DateTime $scheduled_datetime
 */
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <?php echo $campaign->id ? 'Edit' : 'Create a New'?> Campaign
            </header>
            <div class="panel-body">
                <ul class="form-steps">
                    <li class="active"><a href="javascript:;" data-step="1" title="Campaign Info">Campaign Info</a></li>
                    <li><a href="javascript:;" data-step="2" title="Build">Build</a></li>
                    <li><a href="javascript:;" data-step="3" title="Preview &amp; Send">Preview &amp; Send</a></li>
                </ul><!-- .progress-bar -->
            </div>
        </section>
    </div>
</div>

<form method="post" id="create-campaign">

    <div data-step="1">
        <?php require_once dirname( __FILE__ ) . '/_step1.php' ?>
    </div>

    <div class="hidden" data-step="2">
        <?php require_once dirname( __FILE__ ) . '/_step2.php' ?>
    </div>

    <div class="hidden" data-step="3">
        <?php require_once dirname( __FILE__ ) . '/_step3.php' ?>
    </div>

</form>
