<?php
/**
 * @var APIKey $api_key
 * @var Company[] $companies
 */
?>

<form method="post" role="form">
    <div class="row-fluid">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Manage API Key - <?php echo $api_key->company ?>
                </header>

                <div class="panel-body">
                    <div class="form-group">
                        <label for="company-id">Company:</label>
                        <select name="company-id" id="company-id" class="form-control">
                            <?php
                            foreach ( $companies as $company ):
                                echo '<option value="'. $company->id .'" ' . ($api_key->company_id == $company->id ? 'selected' : '') . '>'. $company->name .'</option>';
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1" <?php if ($api_key->id && $api_key->status == 1) echo 'selected' ?>>Active</option>
                            <option value="0" <?php if ($api_key->id && $api_key->status == 0) echo 'selected' ?>>Inactive</option>
                        </select>
                    </div>
                    <ul>
                        <li><strong>Key:</strong> <?php echo $api_key->key ? $api_key->key : 'Will be generated on save.' ?></li>
                    </ul>
                    <p>
                        <button class="btn btn-primary" type="submit">Save</button>
                    </p>
                </div>
            </section>
        </div>
    </div>

    <div class="row-fluid">
        <div class="col-lg-6">
            <section class="panel">
                <header class="panel-heading">
                    Brands
                </header>

                <div class="panel-body">

                    <div class="form-group">
                        <input type="text" class="form-control" id="brand-autocomplete" placeholder="Add a Brand" />
                    </div>

                    <p><strong>Available Brands:</strong></p>
                    <ul id="brand-list">
                        <?php foreach( $selected_brands as $brand ): ?>
                            <li><?php echo $brand['name'] ?> <input type="hidden" name="brands[]" value="<?php echo $brand['id'] ?>" /><a href="javascript:;" class="remove"><i class="fa fa-trash-o"></i></a></li>
                        <?php endforeach; ?>
                    </ul>

                    <p>
                        <button class="btn btn-primary" type="submit">Save</button>
                    </p>
                </div>
            </section>
        </div>

        <div class="col-lg-6">
            <section class="panel">
                <header class="panel-heading">
                    Ashley Accounts
                </header>

                <div class="panel-body">
                    <div class="form-group">
                        <input type="text" class="form-control" id="ashley-account-autocomplete" placeholder="Add an Account" />
                    </div>

                    <p><strong>Available Ashley Accounts:</strong></p>
                    <ul id="ashley-account-list">
                        <?php foreach( $selected_ashley_accounts as $ashley_account ): ?>
                            <li><?php echo $ashley_account['name'] ?> <input type="hidden" name="ashley-accounts[]" value="<?php echo $ashley_account['id'] ?>" /><a href="javascript:;" class="remove"><i class="fa fa-trash-o"></i></a></li>
                        <?php endforeach; ?>
                    </ul>

                    <p>
                        <button class="btn btn-primary" type="submit">Save</button>
                    </p>
                </div>
            </section>
        </div>
    </div>

    <?php nonce::field( 'manage' ) ?>
</form>
<script>
    var Brands = <?php echo json_encode( $brands ) ?>;
    var AshleyAccounts = <?php echo json_encode( $ashley_accounts ) ?>;
</script>