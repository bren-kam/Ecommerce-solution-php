<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit | Craigslist | Email Marketing
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var CraigslistAd $ad
 * @var string $errs
 * @var string $js_validation
 * @var CraigslistMarket[] $markets
 * @var Craigslist_API $craiglist_api
 */

nonce::field( 'autocomplete_owned', '_autocomplete_owned' );
nonce::field( 'load_product', '_load_product' );
?>

<form id="fAddCraigslistTemplate" method="post" role="form">
    <div class="row-fluid">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    <?php echo $ad->id ? 'Edit' : 'Add' ?> Craigslist AD
                </header>
                <div class="panel-body">


                    <input id="hCraigslistAdID" name="hCraigslistAdID" type="hidden" value="<?php if ( $craigslist_ad_id ) echo $craigslist_ad_id; ?>" />
                    <input id="hProductID" name="hProductID" type="hidden" value="<?php echo ( isset( $_POST['hProductID'] ) ) ? $_POST['hProductID'] : $ad->product_id;?>" />
                    <input id="hProductName" name="hProductName" type="hidden" value="<?php echo ( isset( $_POST['hProductName'] ) ) ? $_POST['hProductName'] : $ad->product_name; ?>" />
                    <input id="hProductCategoryID" type="hidden" value="0" />
                    <input id="hProductCategoryName" type="hidden" value="" />
                    <input id="hProductSKU" type="hidden" value="<?php echo ( isset( $_POST['hProductSKU'] ) ) ? $_POST['hProductSKU'] : $ad->sku; ?>" />
                    <input id="hProductBrandName" type="hidden" value="0" />
                    <input id="hProductDescription" type="hidden" value="" />
                    <textarea id="hProductSpecifications" class="hidden" rows="5" cols="50"></textarea>
                    <input id="hStoreName" type="hidden" value="<?php echo $user->account->title; ?>" />
                    <input id="hStoreURL" type="hidden" value="<?php echo 'http://', $user->account->domain; ?>" />
                    <input id="hStoreLogo" type="hidden" value="<?php echo str_replace( 'logo/', 'logo/large/', $user->account->logo ); ?>" />
                    <input name="hPostAd" id="hPostAd" type="hidden" value="0" />
                    <textarea name="hCraigslistPost" id="hCraigslistPost" rows="5" cols="50" class="hidden"></textarea>


                    <div class="row">
                        <div class="col-lg-2">
                            <select class="form-control" id="sAutoComplete">
                                <option value="sku">SKU</option>
                                <option value="product">Product Name</option>
                                <option value="brand">Brand</option>
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <input type="text" class="form-control" id="tAutoComplete" placeholder="Search..."/>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="row-fluid <?php if ( !$ad->id ) echo 'hidden'; ?>" id="dCreateAd">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Create AD
                </header>
                <div class="panel-body">

                    <div id="dProductPhotos" class="hidden"></div>

                    <?php
                    for ( $i = 0; $i < 10; $i++ ):
                        $headline = ( isset( $ad->headlines[$i] ) ) ? $ad->headlines[$i] : '';
                        ?>
                        <div class="form-group">
                            <label for="tHeadline<?php echo $i; ?>">Headline <?php echo $i + 1; ?>:</label>
                            <input type="text" class="form-control headline" name="tHeadlines[]" id="tHeadline<?php echo $i; ?>" value="<?php echo ( isset( $_POST['tHeadlines'][$i] ) ) ? $_POST['tHeadlines'][$i] : $headline; ?>" maxlength="70" />
                        </div>
                    <?php endfor; ?>

                    <div class="form-group">
                        <label for="taDescription">Description:</label>
                        <textarea class="form-control" name="taDescription" id="taDescription" rte="1"><?php echo ( isset( $_POST['taDescription'] ) ) ? $_POST['taDescription'] : $ad->text; ?></textarea>
                    </div>
                    <p><strong>Syntax Tags:</strong> [Product Name] [Store Name] [Store Logo] [Category] [Brand] [Product Description] [SKU] [Photo] [Product Specifications]</p>

                    <div class="form-group">
                        <label for="tPrice">Price:</label>
                        <input type="text" class="form-control" name="tPrice" id="tPrice" value="<?php echo ( isset( $_POST['tPrice'] ) ) ? $_POST['tPrice'] : $ad->price; ?>"  />
                    </div>

                    <div class="form-group">
                        <label for="sCraigslistMarkets">Craigslist Markets:</label>
                        <select class="form-control" name="sCraigslistMarkets[]" id="sCraigslistMarkets" tabindex="15" multiple="multiple">
                            <?php
                            $category_markets = array();
                            foreach ( $markets as $market ):
                                if ( !isset( $category_markets[$market->cl_market_id] ) )
                                    $category_markets[$market->cl_market_id] = $craigslist_api->get_cl_market_categories( $market->cl_market_id );

                                $category = '(No Category)';

                                foreach ( $category_markets[$market->cl_market_id] as $cm ):
                                    if ( $cm->cl_category_id == $market->cl_category_id ):
                                        $category = $cm->name;
                                        break;
                                    endif;
                                endforeach;

                                $selected = ( in_array( $market->id, $ad->craigslist_markets ) ) ? ' selected="selected"' : '';
                                ?>
                                <option value="<?php echo $market->id; ?>"<?php echo $selected; ?>><?php echo $market->market, ' / ', $category; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <p>
                        <?php nonce::field( 'add_edit' ) ?>
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-default" id="show-preview">Preview</button>
                    </p>

                </div>
            </section>
        </div>
    </div>


    <div class="row-fluid <?php if ( !$ad->id ) echo 'hidden'; ?>" id="dPreviewAd">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Preview AD
                </header>
                <div class="panel-body">

                    <div id="preview"></div>

                    <p>
                        <button type="button" class="btn btn-primary" id="post-ad">Post AD</button>
                    </p>

                </div>
            </section>
        </div>
    </div>

</form>
