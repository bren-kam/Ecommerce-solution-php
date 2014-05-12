
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
            <td><input type="checkbox" class="cb" name="email_lists[]" value="<?php echo $list->id ?>" <?php if ( $list->count == 0 ) echo 'disabled="disabled"' ?>/></td>
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
            <input type="text" class="tb" name="name" id="name" value="" />
        </td>
        <td>
            <label for="subject">Email Subject:</label><br/>
            <input type="text" class="tb" name="subject" id="subject" value="" />
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <input type="checkbox" class="cb" name="overwrite_from" id="overwrite_from" value="1" /><label for="overwrite_from">Personalize the "from" field:</label><br />
            <input type="text" class="tb tb-large" name="from" id="from" value="" />
        </td>
    </tr>
</table>

<br /><br />

<div class="campaign-schedule">
    <div class="container">
        <input type="checkbox" class="cb" name="schedule" id="schedule" value="1" />
        <label for="schedule">I would like to schedule this campaign to be sent at a later time:</label>
    </div>

    <br />

    <div class="schedule">
        <div id="dDate" class="float-left col-4"></div>
        <input type="hidden" name="date" id="date" val="" />

        <div class="float-left">
            <input type="text" class="tb" name="tTime" id="tTime" style="width: 75px;" value="12:00 am" maxlength="8" />
            <select>
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