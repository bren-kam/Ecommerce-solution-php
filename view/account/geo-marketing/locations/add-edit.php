<?php

$upload_url = '/website/upload-file/?_nonce=' . nonce::create( 'upload_file' );
$search_url = '/website/get-files/?_nonce=' . nonce::create( 'get_files' );
$delete_url = '/website/delete-file/?_nonce=' . nonce::create( 'delete_file' );

?>
<form name="add-edit-location" id="add-edit-location" action="" method="post" role="form">

    <div class="row-fluid">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Add/Edit Location - Required Listing Info
                </header>

                <div class="panel-body">

                    <?php echo $error_messages ?>

                    <div class="form-group">
                        <label for="locationName">Name:*</label><input type="text" class="form-control" name="locationName" id="locationName" value="<?php echo $location['locationName'] ?>" maxlength="100"/>
                    </div>
                    <div class="form-group">
                        <label for="address">Address Line 1:*</label><input type="text" class="form-control" name="address" id="address" value="<?php echo $location['address'] ?>" maxlength="255"/>
                    </div>
                    <div class="form-group">
                        <label for="address2">Address Line 2:</label><input type="text" class="form-control" name="address2" id="address2" value="<?php echo $location['address2'] ?>" maxlength="255"/>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="city">City:*</label><input type="text" class="form-control" name="city" id="city" value="<?php echo $location['city'] ?>" maxlength="80"/>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="state">State:*</label>
                                <select class="form-control" name="state" id="state">
                                    <?php data::states( true, $location['state'] ) ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="zip">ZIP:*</label><input type="text" class="form-control" name="zip" id="zip" value="<?php echo $location['zip'] ?>" maxlength="10"/>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone:*</label><input type="text" class="form-control" name="phone" id="phone" value="<?php echo $location['phone'] ?>" maxlength="10" placeholder="Must be a valid 10-digit phone number. Without dashes or parethesis."/>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="yext-categories">Categories (Select up to 10 categories in order of importance):*</label>
                                <select class="form-control" id="yext-categories">
                                    <option value="">-- Select a Category --</option>
                                    <?php foreach( $yext_categories as $k => $v ): ?>
                                        <option value="<?php echo $k ?>" <?php if ( in_array( $k, (array)$location['categoryIds'] ) ) echo 'disabled' ?>><?php echo $v ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <p><strong>Current Location Categories:</strong></p>
                            <ul id="category-list">
                                <?php if ( $location['categoryIds'] ): ?>
                                    <?php foreach( $location['categoryIds'] as $category_id ): ?>
                                        <li>
                                            <?php echo $yext_categories[$category_id] ?>
                                            <input type="hidden" name="categoryIds[]" value="<?php echo $category_id ?>" />
                                            <a href="javascript:;" class="remove"><i class="fa fa-trash-o"></i></a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <li id="category-template">
                                    <input type="hidden" name="categoryIds[]" />
                                    <a href="javascript:;" class="remove"><i class="fa fa-trash-o"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>
            </section>
        </div>
    </div>

    <div class="row-fluid">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Optional Business Listing:
                </header>

                <div class="panel-body">

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="faxPhone">Fax Phone:</label><input type="text" class="form-control" name="faxPhone" id="faxPhone" value="<?php echo $location['faxPhone'] ?>" maxlength="10" placeholder="Must be a valid 10-digit phone number. Without dashes or parethesis."/>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="mobilePhone">Mobile Phone:</label><input type="text" class="form-control" name="mobilePhone" id="mobilePhone" value="<?php echo $location['mobilePhone'] ?>" maxlength="10" placeholder="Must be a valid 10-digit phone number. Without dashes or parethesis."/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="tollFreePhone">Toll Free Phone:</label><input type="text" class="form-control" name="tollFreePhone" id="tollFreePhone" value="<?php echo $location['tollFreePhone'] ?>" maxlength="10" placeholder="Must be a valid 10-digit phone number. Without dashes or parethesis."/>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="ttyPhone">TTY Phone:</label><input type="text" class="form-control" name="ttyPhone" id="ttyPhone" value="<?php echo $location['ttyPhone'] ?>" maxlength="10" placeholder="Must be a valid 10-digit phone number. Without dashes or parethesis."/>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="yearEstablished">Year Established:</label><input type="text" class="form-control" name="yearEstablished" id="yearEstablished" value="<?php echo $location['yearEstablished'] ?>" maxlength="4"/>
                    </div>

                    <div class="form-group">
                        <label for="specialties">Specialties (one per line - max 10 lines - each line in can't exceed 50 characters):</label><textarea name="specialties" id="specialties" cols="50" rows="3" class="form-control" maxlength="500"><?php echo is_array($location['specialties']) ? implode( "\n", $location['specialties'] ) : $location['specialties'] ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="services">Services (one per line - max 10 lines - each line in can't exceed 50 characters):</label><textarea name="services" id="services" cols="50" rows="3" class="form-control" maxlength="500"><?php echo is_array($location['services']) ? implode( "\n", $location['services'] ) : $location['services'] ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="brands">Brands (one per line - max 10 lines - each line in can't exceed 50 characters):</label><textarea name="brands" id="brands" cols="50" rows="3" class="form-control" maxlength="500"><?php echo is_array($location['brands']) ? implode( "\n", $location['brands'] ) : $location['brands'] ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="languages">Languages (one per line - max 10 lines - each line in can't exceed 50 characters) :</label><textarea name="languages" id="languages" cols="50" rows="3" class="form-control" maxlength="500"><?php echo is_array($location['languages']) ? implode( "\n", $location['languages'] ) : $location['languages'] ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="keywords">Keywords (one per line - max 10 lines - each line in can't exceed 50 characters):</label><textarea name="keywords" id="keywords" cols="50" rows="3" class="form-control" maxlength="500"><?php echo is_array($location['keywords']) ? implode( "\n", $location['keywords'] ) : $location['keywords'] ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label><textarea name="description" id="description" cols="50" rows="3" class="form-control" maxlength="2000"><?php echo $location['description'] ?></textarea>
                    </div>

                    <p><strong>Payment Options:</strong></p>
                    <?php foreach( $payment_options as $k => $v ): ?>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="paymentOptions[]" value="<?php echo $k ?>" <?php if ( in_array( $k, (array)$location['paymentOptions'] ) ) echo 'checked' ?> />
                                <?php echo $v ?>
                            </label>
                        </div>
                    <?php endforeach; ?>

                </div>
            </section>
        </div>
    </div>

    <div class="row-fluid">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Email &amp; Website:
                </header>

                <div class="panel-body">

                    <p><strong>Open Hours:</strong></p>
                    <?php foreach( $days as $day_number => $day ): ?>
                        <div class="row">
                            <div class="form-group">

                                <label for="" class="col-lg-1 control-label"><?php echo $day ?></label>
                                <div class="col-lg-4">
                                    <div class="form-inline">
                                        <div class="form-group">
                                            <select class="form-control" name="hours-array[<?php echo $day_number ?>][open]">
                                                <option value="">Closed</option>
                                                <?php foreach( $hour_options as $hour => $hour_label ): ?>
                                                    <option value="<?php echo $hour ?>" <?php if ( isset( $location[$day_number] ) && $location[$day_number]['open'] == $hour ) echo 'selected' ?>><?php echo $hour_label ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select class="form-control" name="hours-array[<?php echo $day_number ?>][close]">
                                                <option value="">Closed</option>
                                                <?php foreach( $hour_options as $hour => $hour_label ): ?>
                                                    <option value="<?php echo $hour ?>" <?php if ( isset( $location[$day_number] ) && $location[$day_number]['close'] == $hour ) echo 'selected' ?>><?php echo $hour_label ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="form-group">
                        <label for="additionalHoursText">Additional Hours Text:</label><input type="text" class="form-control" name="additionalHoursText" id="additionalHoursText" value="<?php echo $location['additionalHoursText'] ?>" maxlength="255"/>
                    </div>

                    <div class="form-group">
                        <label for="specialOffer">Special Offer:</label><input type="text" class="form-control" name="specialOffer" id="specialOffer" value="<?php echo $location['specialOffer'] ?>" maxlength="50"/>
                    </div>
                    <div class="form-group">
                        <label for="specialOfferUrl">Special Offer URL:</label><input type="text" class="form-control" name="specialOfferUrl" id="specialOfferUrl" value="<?php echo $location['specialOfferUrl'] ?>" maxlength="255"/>
                    </div>
                    <div class="form-group">
                        <label for="websiteUrl">Website URL:</label><input type="text" class="form-control" name="websiteUrl" id="websiteUrl" value="<?php echo $location['websiteUrl'] ?>" maxlength="255"/>
                    </div>
                    <div class="form-group">
                        <label for="reservationsUrl">Reservations URL:</label><input type="text" class="form-control" name="reservationsUrl" id="reservationsUrl" value="<?php echo $location['reservationsUrl'] ?>" maxlength="255"/>
                    </div>

                    <div class="row">
                        <!--
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="facebookPageUrl">Facebook Page URL:</label><input type="text" class="form-control" name="facebookPageUrl" id="facebookPageUrl" value="<?php echo $location['facebookPageUrl'] ?>" maxlength="255"/>
                            </div>
                        </div>
                         -->
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="twitterHandle">Twitter Handle:</label><input type="text" class="form-control" name="twitterHandle" id="twitterHandle" value="<?php echo $location['twitterHandle'] ?>" maxlength="15" placeholder="Valid Twitter handle for the location (e.g., JohnSmith  (without the leading @))"/>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="emails">Emails (one per line - max 10 lines):</label><textarea name="emails" id="emails" cols="50" rows="3" class="form-control" maxlength="500"><?php echo is_array($location['emails']) ? implode( "\n", $location['emails'] ) : $location['emails'] ?></textarea>
                    </div>

                </div>
            </section>
        </div>
    </div>

    <div class="row-fluid">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Photos & Videos:
                </header>

                <div class="panel-body">

                    <div class="row">
                        <div class="col-lg-6">
                            <p class="image-selector" id="logo">
                                <strong>Logo:</strong>
                                <img class="img-responsive" src="<?php echo isset( $location['logo']['url'] ) ? $location['logo']['url'] : '//placehold.it/200x200&text=Add+Logo' ?>" />
                                <input type="hidden" name="logo-url" value="<?php echo $location['logo']['url'] ?>" />
                                <button type="button" class="btn btn-xs btn-default" title="Open Media Manager"
                                        data-media-manager
                                        data-upload-url="<?php echo $upload_url ?>"
                                        data-search-url="<?php echo $search_url ?>"
                                        data-delete-url="<?php echo $delete_url ?>"
                                        data-image-target="#logo">
                                    Select an Image
                                </button>
                            </p>
                        </div>
                        <div class="col-lg-6">
                            <p class="image-selector" id="custom-photo-0">
                                <strong>Image/Photo:</strong>
                                <img class="img-responsive" src="<?php echo isset( $location['custom-photos'][0]['url'] ) ? $location['custom-photos'][0]['url'] : '//placehold.it/200x200&text=Add+Image' ?>" />
                                <input type="hidden" name="custom-photos[0]" value="<?php echo isset($location['custom-photos'][0]['url']) ? $location['custom-photos'][0]['url'] : ''?>" />
                                <button type="button" class="btn btn-xs btn-default" title="Open Media Manager"
                                        data-media-manager
                                        data-upload-url="<?php echo $upload_url ?>"
                                        data-search-url="<?php echo $search_url ?>"
                                        data-delete-url="<?php echo $delete_url ?>"
                                        data-image-target="#custom-photo-0">
                                    Select an Image
                                </button>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <?php for( $k=1; $k<=4; $k++ ): $image = $location['custom-photos'][$k]; ?>
                            <div class="col-lg-6">
                                <p class="image-selector" id="custom-photo-<?php echo $k ?>">
                                    <strong>Image/Photo:</strong>
                                    <img class="img-responsive" src="<?php echo isset( $image['url'] ) ? $image['url'] : '//placehold.it/200x200&text=Add+Image' ?>" />
                                    <input type="hidden" name="custom-photos[<?php echo $k ?>]" value="<?php echo isset($image['url']) ? $image['url'] : ''?>" />
                                    <button type="button" class="btn btn-xs btn-default" title="Open Media Manager"
                                            data-media-manager
                                            data-upload-url="<?php echo $upload_url ?>"
                                            data-search-url="<?php echo $search_url ?>"
                                            data-delete-url="<?php echo $delete_url ?>"
                                            data-image-target="#custom-photo-<?php echo $k ?>">
                                        Select an Image
                                    </button>
                                </p>
                            </div>
                            <?php if ( $k & 0 ) echo '</div><div class="row">'; ?>

                        <?php endfor; ?>
                    </div>

                    <?php if ( $user->account->pages ): ?>
                        <div class="alert alert-info">If there are any remaining unused images (6 total) they will be pulled from your banners and sidebars.</div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="videoUrls">Video URLs - Valid YouTube URLs for embedding a video on some publisher sites (one per line - max 10 lines):</label><textarea name="videoUrls" id="videoUrls" cols="50" rows="3" class="form-control"><?php echo is_array($location['videoUrls']) ? implode( "\n", $location['videoUrls'] ) : $location['videoUrls'] ?></textarea>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" name="synchronize-products" id="synchronize-products33" value="1" <?php if ( $location['synchronize-products'] ) echo 'checked' ?> />List top 100 products on location</label>
                    </div>

                    <p>
                        <?php nonce::field('add_edit'); ?>
                        <input type="hidden" name="id" id="id" value="<?php echo $location['id'] ?>"/>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </p>

                </div>
            </section>
        </div>
    </div>

</form>