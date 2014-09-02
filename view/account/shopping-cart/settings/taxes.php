<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Taxes
            </header>
            <div class="panel-body">
                <form id="fTaxes" method="post">

                    <div id="tax-list">
                        <?php
                            if ( !empty( $taxes['states'] ) )
                            foreach ( $taxes['states'] as $abbr => $tax ) :
                        ?>
                            <?php
                                $ta = "";
                                if ( isset( $taxes['zip_codes'][$abbr] ) ):
                                    foreach ( $taxes['zip_codes'][$abbr] as $zip => $cost ):
                                        $ta .= $zip . ' ' . $cost. "\n";
                                    endforeach;
                                endif;
                            ?>
                            <div class="row tax">
                                <div class="col-lg-6">
                                    <a href="javascript:;" class="toggle-zip-codes" title="Edit Tax Zip Codes"><?php echo $states[$abbr]; ?></a>
                                    <textarea name="zip_codes[<?php echo $abbr; ?>]" class="form-control hidden" col="50" rows="3" placeholder="[Zip] [Cost]"><?php echo $ta; ?></textarea>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="states[<?php echo $abbr; ?>]" value="<?php echo $tax; ?>" maxlength="5" />
                                    </div>
                                </div>
                                <div class="col-lg-1">
                                    <a href="javascript:;" class="delete"><i class="fa fa-trash-o"></i></a>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <select class="form-control" id="state">
                                <option value="">-- Select a State --</option>
                                <?php foreach ( $states as $key => $state ) : ?>
                                    <option class="<?php if ( isset( $taxes['states'] ) && array_key_exists( $key, $taxes['states'] ) ) echo ' hidden'; ?>" value="<?php echo $key; ?>"><?php echo $state; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <input type="text" class="form-control" id="tax" maxlength="5" />
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <a href="javascript:;" class="add btn btn-success">Add Tax</a>
                        </div>
                    </div>

                    <p class="text-right">
                        <?php nonce::field('taxes') ?>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </p>

                </form>
            </div>
        </section>
    </div>
</div>

<div class="row tax hidden" id="tax-template">
    <div class="col-lg-6">
        <a href="javascript:;" class="toggle-zip-codes" title="Edit Tax Zip Codes">STATE_NAME</a>
        <textarea name="zip_codes[STATE_CODE]" class="form-control hidden" col="50" rows="3" placeholder="[Zip] [Cost]"></textarea>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <input type="text" class="form-control" name="states[STATE_CODE]" maxlength="5" value="TAX" />
        </div>
    </div>
    <div class="col-lg-1">
        <a href="javascript:;" class="delete"><i class="fa fa-trash-o"></i></a>
    </div>
</div>