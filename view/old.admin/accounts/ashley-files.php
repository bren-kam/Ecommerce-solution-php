<?php
/**
 * @package Grey Suit Retail
 * @page Ashley Files Popup
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var array $files
 * @var int $file_count
 */

if ( $file_count > 0 ) {
?>
<table id="tAshleyFiles">
    <thead>
        <tr>
            <th class="text-left"><strong><?php echo _('Name'); ?></strong></th>
            <th class="text-left"><strong><?php echo _('Size'); ?></strong></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 0;
        foreach ( $files as $file ) {
            $i++;
            $strong = $i == $file_count;
            ?>
        <tr>
            <td><?php if ( $strong ) echo '<strong>'; echo $file['name']; if ( $strong ) echo '</strong>'; ?></td>
            <td><?php if ( $strong ) echo '<strong>'; echo $file['size']; if ( $strong ) echo '</strong>'; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } else { ?>
    <p><?php echo _('There are no files in this directory'); ?></p>
<?php } ?>