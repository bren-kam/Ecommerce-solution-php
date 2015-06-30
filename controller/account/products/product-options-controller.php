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
     * List Users
     *
     * @return TemplateResponse
     */
    protected function index() {
        // Don't forget to RUN:
        // alter table products add column parent_product_id int null default null, add index fk_products_products(parent_product_id);
        return $this->get_template_response( 'index' )
            ->kb( 18 )
            ->add_title( _('Product Options') )
            ->select( 'products', 'products/product-options' );
    }

    /**
     * List Product Options
     *
     * @return DataTableResponse
     */
    protected function list_all() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        // Set Order by
        $dt->order_by( 'p.`product_id`', 'p.`sku`', 'p.`name`' );
        $dt->search( array( 'p.`product_id`', 'p.`sku`' => true, 'p.`name`' => true ) );
        $dt->add_where( " AND p.website_id = {$this->user->account->id} " );

        // Get product option
        $product = new Product();

        // Get attributes
        $child_products = $product->list_child_products( $dt->get_variables() );
        $dt->set_row_count( $product->count_child_products( $dt->get_count_variables() ) );

        // Set initial data
        $data = [];

        if ( is_array( $child_products ) ) {
            foreach ($child_products as $cp) {
                $data[] = [
                    $cp->product_id . "<br><a href=\"/products/product-builder/add-edit/?pid={$cp->product_id}\">Edit</a>",
                    $cp->sku,
                    $cp->name,
                ];
            }
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
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

            $product_option = new ProductOption();
            $product_option->website_id = $this->user->account->id;
            $product_option->name = current($_POST['option-name']);
            $product_option->type = $_POST['hType'];
            $product_option->create();

            $product_ids = [];

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
            $product_option->add_relations($product_ids);

            return new RedirectResponse('/products/#!p={$product->product_id}/options');
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