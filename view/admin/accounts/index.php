<?php
/**
 * @package Grey Suit Retail
 * @page List Accounts
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Accounts') );
?>

<div id="subcontent">
    <div id="dNarrowSearchContainer">
        <div id="dNarrowSearch">
            <?php
            nonce::field( 'autocomplete', '_ajax_autocomplete' );
            nonce::field( 'change-state', '_ajax_change_state' );
            nonce::field( 'search', '_ajax_search' );
            ?>

            <h2><?php echo _('Narrow Your Search'); ?></h2>

            <form id="fSubmitSearch">
                <div style="float:right">
                <label for="sState"><?php echo _('View:'); ?></label>
                <select id="sState">
                    <option value="all"><?php echo _('All Accounts'); ?></option>
                    <option value="live"><?php echo _('Live'); ?></option>
                    <option value="staging"><?php echo _('Staging'); ?></option>
                    <option value="inactive"><?php echo _('Inactive'); ?></option>
                </select>
                </div>
                <br clear="left" />

                <table cellpadding="0" cellspacing="0" id="tNarrowSearch" width="100%">
                    <tr>
                        <td width="50" class="col-1">
                            <select id="sAutoComplete">
                                <option value="title"><?php echo _('Account Name'); ?></option>
                                <option value="domain"><?php echo _('Account Domain'); ?></option>
                                <option value="store_name"><?php echo _('Store Name'); ?></option>
                            </select>
                        </td>
                        <td width="245" valign="top"><input type="text" name="tAutoComplete" id="tAutoComplete" tmpval="<?php echo _('Enter Account Name...'); ?>" style="height:17px; padding: 1px 0 0 2px; width: 100%;" /></td>
                        <td colspan="2">&nbsp;</td>
                        <td id="tdSearch"><a href="javascript:;" id="aResetSearch" title="<?php echo _('Reset Search'); ?>" style="margin-right:14px"><?php echo _('Reset Search'); ?></a> <a href="#" id="aSearch" title="<?php echo _('Search'); ?>" class="button"><?php echo _('Search'); ?></a></td>
                    </tr>
                </table>
                <br clear="left" />
            </form>
            <img id="iNYSArrow" src="/images/accounts/narrow-your-search.png" alt="" width="76" height="27" />
        </div>
    </div>
    <br clear="left" /><br />
    <br />

    <table ajax="/ajax/accounts/list_all/" perPage="30,50,100">
        <thead>
            <tr>
                <th width="4%">&nbsp;</th>
                <th width="55%" sort="1"><?php echo _('Account'); ?></th>
                <th width="16%"><?php echo _('User Name' ); ?></th>
                <th width="15%"><?php echo _('Online Specialist'); ?></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<?php echo $template->end(); ?>