<?php
/**
 * @package Grey Suit Retail
 * @page Step1 | Create | Campaigns | Email Marketing
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

<?php if ( $campaign->id ) { ?>
    <input type="hidden" name="id" value="<?php echo $campaign->id ?>" />
<?php } ?>

<table class="subscribers">
    <thead>
    <tr>
        <th width="3%"></th>
        <th>Name</th>
        <th>Subscribers</th>
        <th>Date Added</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ( $email_lists as $list ) { ?>
        <tr>
            <td><input type="checkbox" class="cb" name="email_lists[]" value="<?php echo $list->id ?>" <?php if ( $list->count == 0 ) echo 'disabled="disabled"' ?> <?php if ( $campaign->email_lists && in_array( $list->id, $campaign->email_lists ) ) echo 'checked="checked"' ?> /></td>
            <td><?php echo $list->name ?></td>
            <td><?php echo $list->count ?> subscribers</td>
            <td><?php $date = DateTime::createFromFormat( 'Y-m-d H:i:s', $list->date_created ); echo $date->format( 'F j, Y' ) ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<br /><br />

<table class="campaign-settings">
    <tr>
        <td>
            <label for="name">Campaign Name:</label><br/>
            <input type="text" class="tb" name="name" id="name" value="<?php echo $campaign->name ?>" />
        </td>
        <td>
            <label for="subject">Email Subject:</label><br/>
            <input type="text" class="tb" name="subject" id="subject" value="<?php echo $campaign->subject ?>" />
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <input type="checkbox" class="cb" name="overwrite_from" id="overwrite_from" value="1" <?php if ( $overwrite_from ) echo 'checked="checked"' ?> /><label for="overwrite_from">Personalize the "from" field:</label><br />
            <input type="text" class="tb tb-large" name="from" id="from" value="<?php echo $overwrite_from ? $campaign->from : '' ?>" placeholder="Will be sent as '<?php echo $default_from ?>'" />
        </td>
    </tr>
</table>

<br /><br />

<div class="campaign-schedule">
    <div class="container">
        <input type="checkbox" class="cb" name="schedule" id="schedule" value="1" <?php if ( $scheduled_datetime ) echo 'checked="checked"' ?> />
        <label for="schedule">I would like to schedule this campaign to be sent at a later time:</label>
    </div>

    <br />

    <div class="schedule">
        <div id="dDate" class="float-left col-4"></div>
        <input type="hidden" name="date" id="date" value="<?php echo $scheduled_datetime ? $scheduled_datetime->format('Y-m-d') : '' ?>" />

        <div class="float-left">
            <input type="text" class="tb" name="time" id="tTime" style="width: 75px;" value="<?php echo $scheduled_datetime ? strtolower( $scheduled_datetime->format('h:s A') ) : '12:00 am' ?>" maxlength="8" />
            <select name="timezone">
                <?php foreach ( $timezones as $tz_key => $tz_name ) { ?>
                    <option value="<?php echo $tz_key ?>" <?php if ( $settings['timezone'] == $tz_key ) echo 'selected' ?>><?php echo $tz_name ?></option>
                <?php } ?>
            </select>
        </div>

        <br clear="all"/>
    </div>
</div>

<p class="text-right"><a href="#" data-step="2" class="button" title="<?php echo _('Next'); ?>"><?php echo _('Next >'); ?></a></p>

<br clear="all" />