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
 * @var DateTime $scheduled_datetime
 */
?>

<?php if ( $campaign->id ) { ?>
    <input type="hidden" name="id" id="campaign-id" value="<?php echo $campaign->id ?>" />
<?php } ?>

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
</table>

<br /><br />

<div class="campaign-schedule">
    <div class="container">
        <label for="schedule">
            <input type="checkbox" class="cb" name="schedule" id="schedule" value="1" <?php if ( $scheduled_datetime ) echo 'checked="checked"' ?> />
            I would like to schedule this campaign to be sent at a later time:
        </label>
    </div>

    <br />

    <div class="schedule hidden">
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

<br /><br />

<h3>Select Subscribers List:</h3>
<br />
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
        <tr>
            <td><input type="checkbox" class="cb" id="select-all-subscribers"/></td>
            <td><label for="select-all-subscribers">All Subscribers</label></td>
            <td></td>
            <td></td>
        </tr>
    <?php foreach ( $email_lists as $k => $list ) { ?>
        <tr>
            <td><input type="checkbox" class="cb" name="email_lists[]" value="<?php echo $list->id ?>" id="s<?php echo $k ?>" <?php if ( $list->count == 0 ) echo 'disabled="disabled"' ?> <?php if ( $campaign->email_lists && in_array( $list->id, $campaign->email_lists ) ) echo 'checked="checked"' ?> /></td>
            <td><label for="s<?php echo $k ?>"><?php echo $list->name ?></label></td>
            <td><?php echo number_format( $list->count, 0 ) ?> subscribers</td>
            <td><?php $date = DateTime::createFromFormat( 'Y-m-d H:i:s', $list->date_created ); echo $date->format( 'F j, Y' ) ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<br /><br />

<p class="text-right"><a href="#" data-step="2" class="button" title="<?php echo _('Next'); ?>"><?php echo _('Next >'); ?></a></p>

<br clear="all" />