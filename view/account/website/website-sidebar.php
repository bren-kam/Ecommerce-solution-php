<?php
/**
 * @package Grey Suit Retail
 * @page Sidebar
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var AccountPage $page
 * @var string $dimensions
 * @var array $files
 * @var array $attachments
 * @var bool $images_alt
 */

echo $template->start( _('Sidebar') );
?>

<a href="#" id="aUploadSidebarImage" class="button" title="<?php echo _('Add Image'); ?>"><?php echo _('Add Image'); ?></a>
<a href="#" class="button loader hidden" id="upload-sidebar-image-loader" title="<?php echo _('Loading'); ?>"><img src="/images/buttons/loader.gif" alt="<?php echo _('Loading'); ?>" /></a>
<div class="hidden-fix position-absolute" id="upload-sidebar-image"></div>
<a href="#dUploadFile" class="button" title="<?php echo _('Upload File (Media Manager)'); ?>" rel="dialog"><?php echo _('Upload File'); ?></a>
<br /><br /><br />
<p class="alert">(<?php echo _('Note: The changes you make to your sidebar are immediately live on your website'); ?>)</p>
<input type="hidden" id="hAccountPageId" value="<?php echo $page->id; ?>" />
<?php
$remove_attachment_nonce = nonce::create( 'remove_attachment' );
nonce::field( 'update_attachment_sequence', '_update_attachment_sequence' );
nonce::field( 'upload_sidebar_image', '_upload_sidebar_image' );
nonce::field( 'upload_sidebar_video', '_upload_sidebar_video' );
?>

<div id="dElementBoxes">
<?php
$h2 = $content_id = $placerholder = $buttons = $value = '';
$update_attachment_status_nonce = nonce::create( 'update_attachment_status' );
$confirm_disable = _('Are you sure you want to deactivate this sidebar element? This will remove it from the sidebar on your website.');
$confirm_remove = _('Are you sure you want to remove this sidebar element?');

/**
 * @var AccountPageAttachment $a
 */
foreach ( $attachments as $a ) {
    $continue = false;
    $remove = true;

    if ( '0' == $a->status ) {
        $disabled =  ' disabled';
        $confirm = '';
        $status = '1';
    } else {
        $confirm = ' confirm="' . $confirm_disable . '"';
        $disabled = '';
        $status = '0';
    }
    
    $enable_disable_url = url::add_query_arg( array( 
            '_nonce' => $update_attachment_status_nonce 
            , 'apaid' => $a->id
            , 's' => $status
        )
        , '/website/update-attachment-status/' 
    );
    
    $enable_disable_link = '<a href="' . $enable_disable_url . '" id="aEnableDisable' . $a->id . '" class="enable-disable' . $disabled . '" title="' . _('Enable/Disable') . '" ajax="1"' . $confirm . '><img src="/images/trans.gif" width="76" height="25" alt="' . _('Enable/Disable') . '" /></a>';

    switch ( $a->key ) {
        case 'email':
            ?>
            <div class="element-box<?php echo $disabled; ?>" id="dAttachment_<?php echo $a->id; ?>">
                <h2><?php echo _('Email Sign Up'); ?></h2>

                <?php echo $enable_disable_link; ?>

                <div id="dEmailContent">
                    <br />
                    <form action="/website/update-sidebar-email/" method="post" ajax="1">
                        <textarea name="taEmail" id="taEmail" cols="50" rows="3"><?php echo $a->value; ?></textarea>
                        <p id="pTempEmailMessage" class="success hidden"><?php echo _('Your Email Sign Up text has been successfully updated.'); ?></p>
                        <input type="hidden" name="hAccountPageAttachmentId" value="<?php echo $a->id; ?>" />
                        <br /><br />
                        <p align="center"><input type="submit" class="button" value="<?php echo _('Save'); ?>" /></p>
                        <?php nonce::field( 'update_sidebar_email', '_nonce' ); ?>
                    </form>
                </div>
            </div>
            <?php
            $continue = true;
            continue;
        break;

        case 'room-planner':
            if ( !empty( $disabled ) || empty( $a->value ) ) {
                $continue = true;
                continue;
            }

            $h2 = _('Room Planner');
            $content_id = 'dRoomPlannerContent';
            $placerholder = '<img src="/images/placeholders/240x100.png" width="240" height="100" alt="' . _('Placeholder') . '" />';
            $value = '<img src="http://' . $user->account->domain . $a->value . '" alt="' . _('Room Planner Image') . '" />';

            $buttons = '<input type="file" id="fUploadRoomPlanner" />';
        break;

        case 'search':
            $h2 = _('Search');
            $content_id = $placerholder = $value = $buttons = '';
            $remove = false;
        break;

        case 'sidebar-image':
            if ( stristr( $a->value, 'http:' ) ) {
                $image_url = $a->value;
            } else {
                $image_url = 'http://' . $user->account->domain . $a->value;
            }
            ?>
            <div class="element-box<?php echo $disabled; ?>" id="dAttachment_<?php echo $a->id; ?>">
                <h2><?php echo _('Sidebar Image'); ?></h2>
                <?php if ( isset( $dimensions ) ) { ?>
                    <p><small><?php echo $dimensions; ?></small></p>
                <?php
                }

                echo $enable_disable_link;
                
                $remove_attachment_url = url::add_query_arg( array(
                        '_nonce' => $remove_attachment_nonce
                        , 'apaid' => $a->id
                        , 't' => 'dAttachment_' . $a->id
                        , 'si' => 1
                    )
                    , '/website/remove-attachment/'
                );
                ?>

                <div id="dSidebarImage<?php echo $a->id; ?>">
                    <br />
                    <form action="/website/update-attachment-extra/" method="post" ajax="1">
                        <div align="center">
                            <p><img src="<?php echo $image_url; ?>" alt="<?php echo _('Sidebar Image'); ?>" /></p>
                            <p><a href="<?php echo $remove_attachment_url; ?>" id="aRemove<?php echo $a->id; ?>" title="<?php echo _('Remove Image'); ?>" ajax="1" confirm="<?php echo $confirm_remove; ?>"><?php echo _('Remove'); ?></a></p>

                            <p><input type="text" class="tb" name="extra" id="tSidebarImage<?php echo $a->id; ?>" placeholder="<?php echo _('Enter Link...'); ?>" value="<?php echo ( empty( $a->extra ) ) ? 'http://' : $a->extra; ?>" /></p>

                            <?php if ( $images_alt ) { ?>
                                <p><input type="text" class="tb" name="meta" placeholder="<?php echo _('Enter Alt Attribute...'); ?>" value="<?php if ( !empty( $a->meta ) ) echo $a->meta; ?>" /></p>
                            <?php } ?>

                            <p id="pTempSidebarImage<?php echo $a->id; ?>" class="success hidden"><?php echo _('Your Sidebar Image has been successfully updated.'); ?></p>
                            <br />
                            <p align="center"><input type="submit" class="button" value="<?php echo _('Save'); ?>" /></p>
                        </div>

                        <input type="hidden" name="hAccountPageAttachmentId" value="<?php echo $a->id; ?>" />
                        <input type="hidden" name="target" value="pTempSidebarImage<?php echo $a->id; ?>" />
                        <?php nonce::field( 'update_attachment_extra', '_nonce' ); ?>
                    </form>
                </div>
            </div>
            <?php
            $continue = true;
            continue;
        break;

        case 'video':
            if ( stristr( $a->value, 'http:' ) ) {
                $video_url = $a->value;
            } else {
                $video_url = 'http://' . $user->account->domain . $a->value;
            }
            
            $h2 = _('Video');
            $content_id = 'dVideoContent';
            $placerholder = '<img src="/images/placeholders/354x235.png" width="354" height="235" alt="' . _('Placeholder') . '" />';
            
            $key = substr( substr( md5( DOMAIN . '17e972798ee5066d58c' ), 11, 30 ), 0, -2 );
            
            $value = '<div id="player" style="width:239px; height:213px; margin:0 auto"></div>';

            echo '
            <script type="text/javascript" language="javascript">
                head.js( "/resources/js_single/?f=flowplayer", function() {
                        $f("player", "/media/flash/flowplayer.unlimited-3.1.5.swf", {
                        key: \'' . $key . '\',
                        playlist: [
                            {
                                url: \'' . $video_url . "',
                                autoPlay: false,
                                autoBuffering: true
                            }
                        ],
                        plugins: {
                            controls: {
                                autoHide: 'never',
                                backgroundColor: '#111009',
                                backgroundGradient: [0.2,0.1,0],
                                borderRadius: '0px',
                                bufferColor: '#151515',
                                bufferGradient: [0.2,0.1,0],
                                buttonColor: '#888888',
                                buttonOverColor: '#adadad',
                                durationColor: '#FFFFFF',
                                fullscreen: false,
                                height: 25,
                                opacity: 1,
                                progressColor: '#6A6969',
                                progressGradient: [0.8,0.3,0],
                                sliderBorder: '1px solid rgba(15, 15, 15, 1)',
                                sliderColor: '#151515',
                                sliderGradient: [0.2,0.1,0],
                                timeBgColor: '#0E0E0E',
                                timeBorder: '0px solid rgba(0, 0, 0, 0.3)',
                                timeColor: '#656565',
                                timeSeparator: ' / ',
                                volumeBorder: '1px solid rgba(128, 128, 128, 0.7)',
                                volumeColor: '#ffffff',
                                volumeSliderColor: '#000000',
                                volumeSliderGradient: [0.1,0],
                                tooltipColor: '#000000',
                                tooltipTextColor: '#ffffff'
                            }
                        }
                    });
                });
            </script>";

            $remove = false;


            $value .= '<br /><a href="#" id="aUploadSidebarVideo" class="button" title="' . _('Upload Video') . '">' . _('Upload') . '</a>';
            $value .= '<a href="#" class="button loader hidden" id="upload-sidebar-video-loader" title="' . _('Loading') . '"><img src="/images/buttons/loader.gif" alt="' . _('Loading') . '" /></a>';
            $value .= '<div class="hidden-fix position-absolute" id="upload-sidebar-video"></div>';
        break;

        case 'current-ad-pdf':
        default:
            $continue = true;
            continue;
        break;
    }

    if ( $continue )
        continue;
    ?>
    <div class="element-box<?php echo $disabled; ?>" id="dAttachment_<?php echo $a->id; ?>">
        <h2><?php echo $h2; ?></h2>

        <?php
        echo $enable_disable_link;

        if ( !empty( $content_id ) ) { ?>
        <div id="<?php echo $content_id; ?>" class="center-content">
            <?php echo ( empty( $a->value ) && isset( $placeholder ) ) ? $placeholder : $value; ?>
        </div>
        <?php } ?>
        <br />

        <?php
        if ( !empty( $buttons ) ) {
            $remove_attachment_url = url::add_query_arg( array(
                    '_nonce' => $remove_attachment_nonce
                    , 'apaid' => $a->id
                    , 't' => $content_id
                )
                , '/website/remove-attachment/'
            );
            ?>
            <div align="center" class="buttons">
                <?php if ( !empty( $a->value ) && $remove ) { ?>
                    <a href="<?php echo $remove_attachment_url; ?>" id="aRemove<?php echo $a->id; ?>" title="<?php echo _('Remove'); ?>" confirm="<?php echo $confirm_remove; ?>"><?php echo _('Remove'); ?></a>
                    <br /><br />
                <?php
                }

                echo $buttons;
                ?>
                <br clear="left" />
            </div>
        <?php } ?>
    </div>
<?php } ?>
</div>

<div id="dUploadFile" class="hidden">
    <input type="text" class="tb" id="tFileName" placeholder="<?php echo _('Enter File Name'); ?>..." error="<?php echo _('You must type in a file name before uploading a file.'); ?>" />
    <a href="#" id="aUploadFile" class="button" title="<?php echo _('Upload'); ?>"><?php echo _('Browse'); ?></a>
    <a href="#" class="button loader hidden" id="upload-file-loader" title="<?php echo _('Loading'); ?>"><img src="/images/buttons/loader.gif" alt="<?php echo _('Loading'); ?>" /></a>
    <div class="hidden-fix position-absolute" id="upload-file"></div>
    <br /><br />

    <div id="file-list">
    <?php
    if ( empty( $files ) ) {
        echo '<p class="no-files">', _('You have not uploaded any files.') . '</p>';
    } else {
        // Set variables
        $delete_file_nonce = nonce::create('delete_file');
        $confirm = _('Are you sure you want to delete this file?');

        /**
         * @var AccountFile $file
         */
        foreach ( $files as $file ) {
            $file_name = f::name( $file->file_path );
            $extension = f::extension( $file->file_path );
            $date = new DateTime( $file->date_created );

            if ( in_array( $extension, image::$extensions ) ) {
                // It's an image!
                echo '<div id="file-' . $file->id . '" class="file"><a href="#', $file->file_path, '" id="aFile', $file->id, '" class="file img" title="', $file_name, '" rel="' . $date->format( 'F jS, Y') . '"><img src="' . $file->file_path . '" alt="' . $file_name . '" /></a><a href="' . url::add_query_arg( array( '_nonce' => $delete_file_nonce, 'afid' => $file->id ), '/website/delete-file/' ) . '" class="delete-file" title="' . _('Delete File') . '" ajax="1" confirm="' . $confirm . '"><img src="/images/icons/x.png" width="15" height="17" alt="' . _('Delete File') . '" /></a></div>';
            } else {
                // It's not an image!
                echo '<div id="file-' . $file->id . '" class="file"><a href="#', $file->file_path, '" id="aFile', $file->id, '" class="file" title="', $file_name, '" rel="' . $date->format( 'F jS, Y') . '"><img src="/images/icons/extensions/' . $extension . '.png" alt="' . $file_name . '" /><span>' . $file_name . '</span></a><a href="' . url::add_query_arg( array( '_nonce' => $delete_file_nonce, 'afid' => $file->id ), '/website/delete-file/' ) . '" class="delete-file" title="' . _('Delete File') . '" ajax="1" confirm="' . $confirm . '"><img src="/images/icons/x.png" width="15" height="17" alt="' . _('Delete File') . '" /></a></div>';
            }
        }
    }
    ?>
    </div>

    <br /><br />
    <div id="dCurrentLink" class="hidden">
        <p><strong><?php echo _('Current Link'); ?>:</strong></p>
        <p><input type="text" class="tb" id="tCurrentLink" value="<?php echo _('No link selected'); ?>" /></p>
        <br />
        <table class="col-1">
            <tr>
                <td class="col-3"><strong><?php echo _('Date'); ?>:</strong></td>
                <td class="col-3"><strong><?php echo _('Size'); ?>:</strong></td>
                <td class="col-3">&nbsp;</td>
            </tr>
            <tr>
                <td id="tdDate"></td>
                <td id="tdSize"></td>
                <td class="text-right"><a href="#" id="insert-into-post" class="button close"><?php echo _('Insert Into Post'); ?></a></td>
            </tr>
        </table>
    </div>
</div>
<?php nonce::field( 'upload_file', '_upload_file' ); ?>

<?php echo $template->end(); ?>