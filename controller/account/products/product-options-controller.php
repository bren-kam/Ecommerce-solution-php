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

        $account_product = new AccountProduct();
        $account_product->get( $product_id, $this->user->account->id );

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


            // Set Variables
            $original_product_options = $account_product->product_options();
            $original_product_option_items = [];
            foreach ( $original_product_options as $original_product_option ) {
                $original_product_option_items = array_merge( $original_product_option_items, $original_product_option->items() );
            }
            $product_ids = [];
            $options = [];

            foreach ( $_POST['option-name'] as $key => $name ) {
                $product_option = new ProductOption();
                $product_option_id = (int) substr( $key, 1 );
                $update = isset( $_POST['action'][$product_option_id] );

                if ( $update )
                    $product_option->get( $product_option_id, $this->user->account->id );

                // Set variables
                $product_option->name = $name;

                // This means it's a number
                if ( $product_option->id ) {
                    $product_option->save();

                    // Update the list of product options (that will be removed)
                    unset( $original_product_options[$product_option->id] );
                } else {
                    $product_option->type = $_POST['hType'];
                    $product_option->website_id = $this->user->account->id;
                    $product_option->product_id = $product->id;
                    $product_option->create();
                }

                foreach ( $_POST['list-items'][$product_option_id] as $product_option_item_id => $item ) {
                    $product_option_item = new ProductOptionItem();

                    if ( $update )
                        $product_option_item->get( $product_option_item_id, $product_option->id );

                    // Set variables
                    $product_option_item->name = $item;

                    if ( $product_option_item->id ){
                        $product_option->save();

                        // Update the list of product options (that will be removed)
                        unset( $original_product_option_items[$item->id] );
                    } else {
                        $product_option_item->product_option_id = $product_option->id;
                        $product_option_item->create();
                    }

                    $options[$product_option->id][] = $product_option_item;
                }
            }
            
            $item_permutations = $factor_permutations( $options );
            foreach ($item_permutations as $permutation_group) {
				$names = [];
				foreach ( $permutation_group as $item ) {
					$names[] = $item->name;
				}
				
                $sku_suffix = strtolower(format::slug( implode('-', $names) ) );
                $name_suffix = implode(' ', $names);

                $child_product = new Product();
                $child_product->clone_product($product->product_id, $this->user->id);
                $child_product->get($child_product->product_id);

                $child_product->website_id = $this->user->account->id;
                $child_product->sku .= '-' . $sku_suffix;
                $child_product->name .= ' ' . $name_suffix;
                $child_product->name = str_replace(' (Clone)', '', $child_product->name );
                $child_product->parent_product_id = $product->product_id;
                $child_product->save();

				foreach ( $permutation_group as $item ) {
					$product_ids[$item->id][] = $child_product->id;
				}
            }

            // Add product relations
			foreach ( $options as $items ) {
				foreach ( $items as $item ) {
					$item->add_relations( $product_ids[$item->id] );
				}
			}

            // Delete old product options
            foreach ( $original_product_options as $product_option ) {
                $product_option->remove();
            }

            // Delete old product option items
            foreach ( $original_product_option_items as $product_option_item ) {
                $product_option_item->remove();
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
            ->set( compact( 'product', 'account_product' ) );
    }
}