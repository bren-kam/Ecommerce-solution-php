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
 */

echo $template->start( _('Send Email'), '../sidebar' );
?>

<div id="tab-top">
    <h2 class="tab selected" id="h2Step1"><a href="#" id="aStep1" class="step" title="<?php echo _('Step 1'); ?>"><?php echo _('Step 1'); ?></a></h2>
    <h2 class="tab" id="h2Step2"><a href="#" id="aStep2" class="step" title="<?php echo _('Step 2'); ?>"><?php echo _('Step 2'); ?></a></h2>
    <h2 class="tab" id="h2Step3"><a href="#" id="aStep3" class="step" title="<?php echo _('Step 3'); ?>"><?php echo _('Step 3'); ?></a></h2>
</div>
<div id="dMainContent">
    <form name="fSendEmail" id="fSendEmail" action="/email-marketing/emails/save/" method="post" ajax="1">
    <div id="dStep1">
        <h2><?php echo _('Basic Email Information'); ?></h2>
        <br />
        <?php
        if ( !empty( $message->date_sent ) ) {
            // Adjust for timezone
            $message->date_sent = dt::adjust_timezone( $message->date_sent, $server_timezone, $timezone );

            list( $date, $time ) = explode( ' ', $message->date_sent );
        } else {
            $date = $time = '';
        }

        ?>
        <table>
            <tr>
                <td><label for="tSubject"><?php echo _('Subject'); ?>:</label></td>
                <td colspan="3"><input type="text" class="tb" name="tSubject" id="tSubject" maxlength="50" value="<?php echo $message->subject; ?>" /></td>
            </tr>
            <tr>
                <td><label for="tDate"><?php echo _('Send Date'); ?>:</label></td>
                <td><input type="text" class="tb" name="tDate" id="tDate" value="<?php echo ( empty( $date ) ) ? dt::adjust_timezone( 'now', $server_timezone, $timezone, 'Y-m-d' ) : $date; ?>" maxlength="10" /></td>
                <td><label for="tTime"><?php echo _('Time'); ?></label>:</td>
                <td><input type="text" class="tb" name="tTime" id="tTime" style="width: 75px;" value="<?php echo ( empty( $time ) ) ? dt::adjust_timezone( 'now', $server_timezone, $timezone, 'h:i a' ) : dt::date( 'h:i a', strtotime( $time ) ); ?>" maxlength="8" /></td>
            </tr>
            <tr>
                <td valign="top"><label><?php echo _('Mailing List(s)'); ?>:</label></td>
                <td>
                    <a href="#" id="aCheckAll" title="<?php echo _('Check All'); ?>"><?php echo _('Check All'); ?></a> | <a href="#" id="aUncheckAll" title="<?php echo _('Uncheck All'); ?>"><?php echo _('Uncheck All'); ?></a>
                    <br /><br />
                    <?php
                    $options = '';
                    if ( $message->email_lists )
                        $email_list_ids = array_keys( $message->email_lists );

                    foreach ( $email_lists as $el ) {
                        $disabled = ( 0 == $el->count ) ? ' disabled="disabled"' : '';
                        $checked = ( isset( $email_list_ids ) && $email_list_ids && in_array( $el->id, $email_list_ids ) ) ? ' checked="checked"' : '';

                        if ( 0 == $el->category_id ) {
                            $options = '<p><input type="checkbox" class="cb mailing-list" id="cbMailingList' . $el->id . '" name="email_lists[]" value="' . $el->id . '"' . $checked . $disabled . ' /> <label for="cbMailingList' . $el->id . '">' . $el->name . ' (' . _('Subscribers') . ': ' . $el->count . ')</label></p>' . $options;
                        } else {
                            $options .= '<p><input type="checkbox" class="cb mailing-list" id="cbMailingList' . $el->id . '" name="email_lists[]" value="' . $el->id . '"' . $checked . $disabled . ' /> <label for="cbMailingList' . $el->id . '">' . $el->name . ' (' . _('Subscribers') . ': ' . $el->count . ')</label></p>';
                        }
                    }

                    echo $options;
                    ?>
                </td>
            </tr>
        </table>
        <p><a href="#" id="aNextStep2" class="next button" title="<?php echo _('Next'); ?>"><?php echo _('Next'); ?></a></p>
    </div>
    <div class="hidden" id="dStep2" style="position:relative">
        <h2 class="col-2"><?php echo _('Choose Email Type'); ?></h2>
        <p class="col-2 text-right" style="position: absolute; right: 10px; top: 15px"><a href="#" id="aPreviousStep1" class="previous button" title="<?php echo _('Previous'); ?>"><?php echo _('Previous'); ?></a> <a href="#" class="save button" title="<?php echo _('Save'); ?>"><?php echo _('Save'); ?></a></p>
        <br />
        <div id="dChooseType" style="height: 170px">
            <div class="choose-div">
                <a href="#" id="aProduct" title="<?php echo _('Product Email'); ?>" class="choose">
                    <img src="/images/icons/email-marketing/emails/email-product.gif" width="162" height="131" alt="<?php echo _('Product Email'); ?>" />
                    <br />
                    <?php echo _('Product'); ?>
                </a>
            </div>
            <div class="choose-div">
                <a href="#" id="aCustom" title="<?php echo _('Custom Email'); ?>" class="choose">
                    <img src="/images/icons/email-marketing/emails/email-custom.gif" width="162" height="131" alt="<?php echo _('Custom Email'); ?>" />
                    <br />
                    <?php echo _('Custom'); ?>
                </a>
            </div>
        </div>
        <div id="dProduct" class="hidden email-type">
            <div class="slider">
                <ul id="ulSlider_product" style="height:400px;width:100px">
                    <?php
                    foreach ( $templates as $email_template ) {
                        if ( 'product' != $email_template->type )
                            continue;
                        ?>
                        <li><a href="#" title="<?php echo $email_template->name; ?>"><img src="<?php echo ( empty( $email_template->thumbnail ) ) ? '/images/emails/thumbnails/default.jpg' : $email_template->thumbnail; ?>" class="slide" id="aSlide<?php echo $email_template->id; ?>" width="100" height="113" alt="<?php echo $email_template->name; ?>" /></a></li>
                        <?php
                        break;
                    } ?>
                </ul>
            </div>
            <div class="template-image">
                <img src="<?php echo ( empty( $email_template->image ) ) ? '/images/emails/default.jpg' : $email_template->image; ?>" id="iTemplateImage<?php echo $email_template->id; ?>" class="selected" width="400" alt="<?php echo $email_template->name; ?>" />
                <a href="#" class="button choose-template" title="<?php echo _('Choose Template'); ?>"><?php echo _('Choose Template'); ?></a>
            </div>
            <br clear="all" />
        </div>
        <div id="dCustom" class="hidden email-type">
            <div class="slider">
                <ul id="ulSlider_custom" style="height:400px;width:100px">
                    <?php
                    reset( $templates );
                    foreach ( $templates as $email_template ) {
                        if ( 'default' != $email_template->type )
                            continue;
                        ?>
                        <li><a href="#" title="<?php echo $email_template->name; ?>"><img src="<?php echo ( empty( $email_template->thumbnail ) ) ? '/images/emails/thumbnails/default.jpg' : $email_template->thumbnail; ?>" class="slide" id="aSlide<?php echo $email_template->id; ?>" width="100" height="113" alt="<?php echo $email_template->name; ?>" /></a></li>
                        <?php
                        break;
                    } ?>
                </ul>
            </div>
            <div class="template-image">
                <img src="<?php echo $email_template->image; ?>" id="iTemplateImage<?php echo $email_template->id; ?>" class="selected" width="400" alt="<?php echo $email_template->name; ?>" />
                <a href="#" class="button choose-template" title="<?php echo _('Choose Template'); ?>"><?php echo _('Choose Template'); ?></a>
            </div>
            <br clear="all" />
        </div>
        <input type="hidden" name="hEmailType" id="hEmailType" value="<?php if ( !empty( $message->type ) ) echo $message->type; ?>" />
        <input type="hidden" name="hEmailTemplateID" id="hEmailTemplateID" value="<?php if ( !empty( $message->email_template_id ) ) echo $message->email_template_id; ?>" />
    </div>
    <div class="hidden" id="dStep3" style="position:relative">
        <h2 class="col-2"><?php echo _('Email Content'); ?></h2>
        <p class="col-2 text-right" style="position: absolute; right: 10px; top: 5px"><a href="#" id="aPreviousStep2" class="previous button" title="<?php echo _('Previous'); ?>"><?php echo _('Previous'); ?></a> <a href="#" class="save button" title="<?php echo _('Save'); ?>"><?php echo _('Save'); ?></a></p>
        <br />
        <textarea name="taMessage" id="taMessage" cols="50" rows="3" rte="1"><?php echo $message->message; ?></textarea>
        <br />
        <p><a href="http://www.ftc.gov/bcp/edu/pubs/business/ecommerce/bus61.shtm" target="_blank" title="<?php echo _('The CAN-SPAM Act'); ?>"><?php echo _('The CAN-SPAM Act'); ?></a></p>
        <br />
        <div id="dCustom_product" class="custom-template<?php if ( empty( $message->type ) || 'product' != $message->type ) echo ' hidden'; ?>">
            <br /><br />

            <h2><?php echo _('Products'); ?></h2>
            <br clear="all" /><br />

            <div id="dNarrowSearchContainer">
                <div id="dNarrowSearch">
                    <h2><?php echo _('Narrow Your Search'); ?></h2>
                    <br />
                    <table id="tNarrowSearch">
                        <tr>
                            <td width="264">
                                <select id="sAutoComplete">
                                    <option value="sku"><?php echo _('SKU'); ?></option>
                                    <option value="product"><?php echo _('Product Name'); ?></option>
                                    <option value="brand"><?php echo _('Brand'); ?></option>
                                </select>
                            </td>
                            <td valign="top"><input type="text" class="tb" id="tAutoComplete" tmpval="<?php echo _('Enter SKU...'); ?>" style="width: 100% !important;" /></td>
                            <td class="text-right" width="125"><a href="#" id="aSearch" title="<?php echo _('Search'); ?>" class="button"><?php echo _('Search'); ?></a></td>
                        </tr>
                    </table>
                    <img id="iNYSArrow" src="/images/narrow-your-search.png" alt="" width="76" height="27" />
                </div>
            </div>
            <br clear="left" /><br />
            <br /><br />
            <br />
            <table cellpadding="0" cellspacing="0" id="tAddProducts" width="100%">
                <thead>
                    <tr>
                        <th width="45%"><?php echo _('Name'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
                        <th width="25%"><?php echo _('Brand'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
                        <th width="15%"><?php echo _('SKU'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
                        <th width="15%"><?php echo _('Status'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <br /><br />

            <h2><?php echo _('Selected Products'); ?></h2>
            <div id="dSelectedProducts">
                <?php
                if ( isset( $message->meta ) && 'product' == $message->type ) {
                    $meta = array();
                    /**
                     * @var Product $product
                     */
                    foreach ( $message->meta as $product ) {
                        $meta[$product->order] = $product;
                    }

                    ksort( $meta );

                    foreach ( $meta as $product ) {
                        $images = $product->get_images();
                        $product_image = 'http://' . $product->industry . '.retailcatalog.us/products/' . $product->id . '/' . current( $images );
                        ?>
                        <div id="dProduct_<?php echo $product->id; ?>" class="product">
                            <h4><?php echo $product->name; ?></h4>
                            <p align="center"><img src="<?php echo $product_image; ?>" alt="<?php echo $product->name; ?>" height="110" style="margin:10px" /></p>
                            <p>
                                <?php echo _('Brand'), ': ', $product->brand; ?><br />
                                <label for="tProductPrice<?php echo $product->id; ?>"><?php echo _('Price'); ?>:</label>
                                <input type="text" class="tb product-price" name="tProductPrice<?php echo $product->id; ?>" id="tProductPrice<?php echo $product->id; ?>" value="<?php echo $product->price; ?>" maxlength="10" />
                            </p>
                            <p class="product-actions" id="pProductAction<?php echo $product->id; ?>"><a href="#" class="remove-product" title="<?php echo _('Remove'); ?>"><?php echo _('Remove'); ?></a></p>
                            <input type="hidden" name="products[]" class="hidden-product" id="hProduct<?php echo $product->id; ?>" value="<?php echo $product->id, '|', $product->price; ?>" />
                        </div>
                    <?php
                    }
                }
                ?>
            </div>
            <br clear="all" />
        </div>
        <br />

        <p style="padding-bottom:0"><a href="#" id="aSendTest" title="<?php echo _('Send Test'); ?>"><?php echo _('Send Test'); ?> [ + ]</a></p>
        <div id="dSendTest" class="hidden">
            <p id="pSuccessMessage" class="hidden"><?php echo _('Please check your email in a minute or two for the test email.'); ?></p>
            <input type="text" class="tb" id="tTestEmail" maxlength="200" tmpval="<?php echo _('Test email...'); ?>" /> <input type="button" id="bSendTest" class="button" value="<?php echo _('Send Test'); ?>" error="<?php echo _('Please enter a valid test email, then try again.'); ?>" />
            <br />
        </div>
        <br />
        <a href="#" class="button" id="aSendEmail" title="<?php echo _('Send Email'); ?>" error="<?php echo _('Please save and test your email before you send it'); ?>"><?php echo _('Send Email'); ?></a>
    </div>
    <input type="hidden" name="hEmailMessageID" id="hEmailMessageID" value="<?php echo ( $message->email_message_id ) ? $message->email_message_id : '0'; ?>" />
    <?php nonce::field( 'save' ); ?>
    </form>
    <?php
    // Do not need to be submitted with the form, simply have to be on the page
    nonce::field( 'test', '_test' );
    nonce::field( 'autocomplete_owned', '_autocomplete_owned' );
    nonce::field( 'delete-product', '_delete_product' );
    nonce::field( 'schedule', '_schedule' );
    nonce::field( 'get-templates', '_get_templates' );
    nonce::field( 'search', '_search' );
    ?>
</div>

<?php echo $template->end(); ?>