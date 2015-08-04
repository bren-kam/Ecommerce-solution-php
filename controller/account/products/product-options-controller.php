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
                $original_product_option_items = $original_product_option_items + $original_product_option->items();
            }
			
            $product_ids = $all_product_ids = [];
            $options = [];

            foreach ( $_POST['option-name'] as $key => $name ) {
                $product_option = new ProductOption();
                $product_option_id = (int) substr( $key, 1 );
                $update = isset( $_POST['action'][$product_option_id] );

                if ( $update )
                    $product_option->get( $product_option_id, $this->user->account->id );

                // Don't create empty product options
                if ( !$product_option->id && empty( $name ) )
                    continue;

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
                        unset( $original_product_option_items[$product_option_item->id] );
                    } else {
                        $product_option_item->product_option_id = $product_option->id;
                        $product_option_item->create();
						
						$options[$product_option->id][] = $product_option_item;
                    }
                }
            }
            
            $item_permutations = $factor_permutations( $options );
			
            foreach ($item_permutations as $permutation_group) {
				$names = [];
				foreach ( $permutation_group as $item ) {
					$names[] = $item->name;
				}

                if ( empty( $item->name ) )
                    continue;
				
                $sku_suffix = strtolower(format::slug( implode('-', $names) ) );
                $name_suffix = implode(' ', $names);

                $child_product = new Product();
                $child_product->clone_product($product->product_id, $this->user->id);
                $child_product->get($child_product->product_id);

                $child_product->website_id = $this->user->account->id;
                $child_product->sku .= '-' . $sku_suffix;
                $child_product->name .= ' ' . $name_suffix;
                $child_product->name = str_replace(' (Clone)', '', $child_product->name );
                $child_product->slug = format::slug($child_product->name);
                $child_product->parent_product_id = $product->product_id;
                $child_product->weight = $product->weight;
                $child_product->save();

				foreach ( $permutation_group as $item ) {
					$product_ids[$item->id][] = $all_product_ids[] = $child_product->id;
				}
            }

            /**
             * Add product relations
             * @var ProductOptionItem[] $items
             */
			foreach ( $options as $items ) {
				foreach ( $items as $item ) {
					$item->add_relations( $product_ids[$item->id] );
				}
			}

            // Delete old product options
            foreach ( $original_product_options as $product_option ) {
                $product_option->remove();
            }

            /**
             * Delete old product option items
             * @var ProductOptionItem[] $original_product_option_items
             */
            foreach ( $original_product_option_items as $product_option_item ) {
                $product_option_item->remove();
            }

            // add new products to website
			if ( $product_ids ) {
				$account_product = new AccountProduct();
				$account_product->add_bulk_by_ids( $this->user->account->id, $all_product_ids );
			}
			
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

    /**
     * Pricing Tool
     * @return RedirectResponse|TemplateResponse
     */
    protected function pricing_tool() {
        $product_id = $_GET['pid'];
        $parent_product = new Product();
        $parent_product->get($product_id);

        if ( !$parent_product->id )
            return new RedirectResponse('/products');

        $account_product = new AccountProduct();
        $child_prices = $account_product->get_child_prices( $parent_product->id, $this->user->account->id );

        if ( empty( $child_prices ) )
            return new RedirectResponse('/products');

        if ( $this->verified() ) {

            $product_prices = $_POST['product'];
            foreach ( $product_prices as $product_id => &$pp ) {
                $pp['account_id'] = $this->user->account->id;
                $pp['product_id'] = $product_id;
            }


            // Set prices on Master Products (visible on product builder and used by auto-price)
            $product = new Product();
            $child_products = $product->get_by_parent($parent_product->id);
            foreach ( $child_products as $child_product ) {
                if ( !isset($product_prices[ $child_product->id ]) )
                    continue;

                $child_product->price = $product_prices[ $child_product->id ]['wholesale_price'];
                $child_product->price_min = $product_prices[ $child_product->id ]['map_price'];
                $child_product->save();
            }

            // Set prices on Account Products (website visible products)
            $account_product->set_product_prices($this->user->account->id, $product_prices);
            $this->notify('Product Prices Updated');

            // Refresh
            $child_prices = $account_product->get_child_prices( $parent_product->id, $this->user->account->id );
        }

        return $this->get_template_response( 'pricing-tool' )
            ->kb( 19 )
            ->menu_item( 'products/product-options/pricing-tool' )
            ->add_title( 'Pricing' )
            ->set( compact( 'parent_product', 'child_prices' ) );
    }
}