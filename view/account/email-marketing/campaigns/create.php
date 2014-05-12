<?php
/**
 * @package Grey Suit Retail
 * @page Send | Email Messages | Email Marketing
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var EmailMessage $message
 * @var EmailList[] $email_lists
 * @var array $settings
 * @var string $timezone
 * @var string $server_timezone
 * @var EmailTemplate[] $templates
 * @var AccountFile[] $files
 */

echo $template->start( _('Create a New Campaign'), '../sidebar' );
?>

<ul class="progress-bar">
    <li><a href="#" data-step="1" title="Campaign Info">Campaign Info</a></li>
    <li class="active"><a href="#" data-step="2" title="Build">Build</a></li>
    <li><a href="#" data-step="3" title="Preview &amp; Send">Preview &amp; Send</a></li>
</ul><!-- .progress-bar -->

<form action="" method="post" id="fCreateCampaign">

    <div class="hidden" data-step="1">
        <?php require_once dirname( __FILE__ ) . '/_step1.php' ?>
    </div>

    <div data-step="2">
        <?php require_once dirname( __FILE__ ) . '/_step2.php' ?>
    </div>

    <div class="hidden" data-step="3">
        <?php require_once dirname( __FILE__ ) . '/_step3.php' ?>
    </div>

</form>
<?php echo $template->end(); ?>