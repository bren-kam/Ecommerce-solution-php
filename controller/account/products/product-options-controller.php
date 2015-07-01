<?php
class ProductOptionsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'products/product-options/';
        $this->section = 'products';
        $this->title .= _('Product Options');
    }

    /**
     * Add/Edit
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function add_edit() {
        // Get the id if there is one
        $product_id = $_GET['pid'];
        $product = new Product();
        $product->get($product_id);

        if ( $this->verified() ) {
            $factor_permutations = function ($lists) {
                $permutations = array();
                $iter = 0;
                while (true) {
                    $num = $iter++;
                    $pick = array();
                    foreach ($lists as $l) {
                        $r = $num % count($l);
                        $num = ($num - $r) / count($l);
                        $pick[] = $l[$r];
                    }
                    if ($num > 0) break;
                    $permutations[] = $pick;
                }
                return $permutations;
            };

            $product_ids = [];

            foreach ( $_POST['option-name'] as $key => $name ) {
                $product_option = new ProductOption();
                $product_option->website_id = $this->user->account->id;
                $product_option->product_id = $product->id;
                $product_option->name = $name;
                $product_option->type = $_POST['hType'];
                $product_option->create();

                foreach ( $_POST['list-items'][substr($key, 1)] as $item ) {
                    $product_option_item = new ProductOptionItem();
                    $product_option_item->product_option_id = $product_option->id;
                    $product_option_item->name = $item;
                    $product_option_item->create();
                }
            }


            $child_sku_pieces = $factor_permutations($_POST['list-items']);
            foreach ($child_sku_pieces as $child_sku_piece) {
                $sku_suffix = strtolower(format::slug( implode('-', $child_sku_piece) ) );
                $name_suffix = implode(' ', $child_sku_piece);

                $child_product = new Product();
                $child_product->clone_product($product->product_id, $this->user->id);
                $child_product->get($child_product->product_id);

                $child_product->website_id = $this->user->account->id;
                $child_product->sku .= '-' . $sku_suffix;
                $child_product->name .= ' ' . $name_suffix;
                $child_product->name = str_replace(' (Clone)', '', $child_product->name );
                $child_product->parent_product_id = $product->product_id;
                $child_product->save();

                $product_ids[] = $child_product->id;
            }

            // add new products to website
            $account_product = new AccountProduct();
            $account_product->add_bulk_by_ids( $this->user->account->id, $product_ids );

            return new RedirectResponse("/products/#!p={$product->product_id}/options");
        }

        $this->resources
            ->javascript( 'products/product-options/add-edit' )
            ->css( 'products/product-options/add-edit' );

        return $this->get_template_response( 'add-edit' )
            ->kb( 19 )
            ->select( 'products', 'products/product-options/add' )
            ->add_title( 'Add' )
            ->set( compact( 'product' ) );
    }
}