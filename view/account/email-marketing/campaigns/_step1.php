<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <div class="panel-body">

                <?php if ( $campaign->id ) : ?>
                    <input type="hidden" name="id" id="campaign-id" value="<?php echo $campaign->id ?>" />
                <?php endif; ?>

                <div class="form-group">
                    <label for="subject">Email Subject:</label>
                    <input type="text" class="form-control" id="subject" name="subject" value="<?php echo $campaign->subject?>" />
                </div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="schedule" id="schedule" value="1" <?php if ( $scheduled_datetime ) echo 'checked="checked"' ?> />
                        I would like to schedule this campaign to be sent at a later time.
                    </label>
                </div>

                <div class="row schedule <?php if ( !!$scheduled_datetime ) echo 'hidden' ?>">
                    <div class="col-lg-4">
                        <div id="schedule-datepicker"></div>
                        <input type="hidden" name="date" id="date" value="<?php echo $scheduled_datetime ? $scheduled_datetime->format('Y-m-d') : '' ?>" />
                    </div>
                    <div class="col-lg-2">
                        <input type="text" class="form-control" name="time" id="tTime" value="<?php echo $scheduled_datetime ? strtolower( $scheduled_datetime->format('h:s A') ) : '12:00 am' ?>" maxlength="8" />
                    </div>
                    <div class="col-lg-4">
                        <select name="timezone" class="form-control">
                            <?php foreach ( $timezones as $tz_key => $tz_name ) : ?>
                                <option value="<?php echo $tz_key ?>" <?php if ( $settings['timezone'] == $tz_key ) echo 'selected' ?>><?php echo $tz_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <h4>Select Subscriber Lists:</h4>

                <table class="table subscribers">
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
                            <td><input type="checkbox" id="select-all-subscribers"/></td>
                            <td><label for="select-all-subscribers">All Subscribers</label></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php foreach ( $email_lists as $k => $list ) : ?>
                            <tr>
                                <td><input type="checkbox" name="email_lists[]" value="<?php echo $list->id ?>" id="s<?php echo $k ?>" <?php if ( $list->count == 0 ) echo 'disabled="disabled"' ?> <?php if ( $campaign->email_lists && in_array( $list->id, $campaign->email_lists ) ) echo 'checked="checked"' ?> /></td>
                                <td><label for="s<?php echo $k ?>"><?php echo $list->name ?></label></td>
                                <td><?php echo number_format( $list->count, 0 ) ?> subscribers</td>
                                <td><?php $date = DateTime::createFromFormat( 'Y-m-d H:i:s', $list->date_created ); echo $date->format( 'F j, Y' ) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <p class="text-right">
                    <a href="javascript:;" data-step="2" class="btn btn-primary">Next &gt;</a>
                </p>


            </div>
        </section>
    </div>
</div>