<?php
class CraigslistController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'craigslist/';
        $this->section = 'craigslist';
        $this->title = _('Craigslist');
    }

    /**
     * List ads
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
        return $this->get_template_response( 'index' )
            ->add_title( _('Craigslist Ads') );
    }

    /***** AJAX *****/

    /**
     * List All
     *
     * @return DataTableResponse
     */
    protected function list_all() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        $craigslist_ad = new CraigslistAd();

        // Set Order by
        $dt->order_by( 'cah.`headline`', 'ca.`text`', 'p.`name`', 'p.`sku`', 'ca.`active`', 'ca.`date_created`' );
        $dt->add_where( ' AND ca.`website_id` = ' . (int) $this->user->account->id );
        $dt->search( array( 'cah.`headline`' => false, 'ca.`text`' => true, 'p.`name`' => true, 'p.`sku`' => false ) );

        // Get items
        $ads = $craigslist_ad->list_all( $dt->get_variables() );
        $dt->set_row_count( $craigslist_ad->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;
        $confirm = _('Are you sure you want to delete a craigslist ad? This cannot be undone.');
        $delete_nonce = nonce::create( 'delete' );
        $copy_nonce = nonce::create( 'copy' );

        /**
         * @var CraigslistAd $ad
         */
        if ( is_array( $ads ) )
        foreach ( $ads as $ad ) {
            $status = ( '0000-00-00 00:00:00' == $ad->date_posted ) ? _('Waiting Approval') : _('Posted');
            $date = new DateTime( $ad->date_created );

            $data[] = array(
                $ad->headline . '<br />' .
                '<div class="actions">' .
                    '<a href="' . url::add_query_arg( 'caid', $ad->id, '/craigslist/add-edit/' ) . '" title="' . _('Edit') . '">' . _('Edit / Post') . '</a> | ' .
                    '<a href="' . url::add_query_arg( array( 'caid' => $ad->id, '_nonce' => $copy_nonce ), '/craigslist/copy/' ) . '" title="' . _('Copy') . '" ajax="1">' . _('Copy') . '</a> | ' .
                    '<a href="' . url::add_query_arg( array( 'caid' => $ad->id, '_nonce' => $delete_nonce ), '/craigslist/delete/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>
                </div>'
                , format::limit_chars( html_entity_decode( str_replace( "\n", '', $ad->text ) ), 100, NULL, TRUE ) . '...'
                , $ad->product_name
                , $ad->sku
                , $status
                , $date->format('F jS, Y')
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }
}


