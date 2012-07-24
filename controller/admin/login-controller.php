<?php
class LoginController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        parent::__construct( );

        // Tell what is the base for all login
        $this->view_base = 'login/';
        $this->section = 'Login';
    }

    /**
     * Login Page
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
        // Get the template response
        $template_response = $this->get_template_response('index');

        // Instantiate a new form table
        $ft = new FormTable( $this->resources, 'fLogin' );

        // Choose login button
        $ft->submit( _('Login') );

        // Add fields
        $ft->add_field( 'text', _('Email'), 'tEmail' )
            ->add_validation( 'req', _('The "Email" field is required') )
            ->add_validation( 'email', _('The "Email" field must contain a valid email address') )
            ->attribute( 'maxlength', 200 );

        $ft->add_field( 'password', _('Password'), 'tPassword' )
            ->add_validation( 'req', _('The "Password" field is required') )
            ->attribute( 'maxlength', 30 );

        $ft->add_field( 'checkbox', _('Remember me?'), 'cbRememberMe' );

        $ft->add_field( 'hidden', 'referer', array( 'GET', 'r' ) );

        // Add extra columns
        $ft->add_end_column( '', '<a href="/forgot-your-password/" title="' . _('Forgot Your Password?') . '">' . _('Forgot Your Password?') . '</a>' );

        // If posted
        if ( $ft->posted() ) {
            // Try to login
            if ( $this->user->login( $_POST['tEmail'], $_POST['tPassword'] ) ) {
                // Two Weeks : Two Days
                $expiration = ( isset( $_POST['cbRememberMe'] ) ) ? 1209600 : 172800;
                set_cookie( AUTH_COOKIE, base64_encode( security::encrypt( $this->user->email, security::hash( COOKIE_KEY, 'secure-auth' ) ) ), $expiration );

                if( !isset( $_POST['referer'] ) || isset( $_POST['referer'] ) && empty( $_POST['referer'] ) ) {
                    return new RedirectResponse('/');
                } else {
                    return new RedirectResponse('/');
                }
            } else {
                $ft->error( _('Your email and password do not match. Please try again.') );
            }
        }

        // Add the form
        $template_response->add( 'form', $ft->generate_form() );

        return $template_response;
    }

    /**
     * Override login function
     * @return bool
     */
    protected function get_logged_in_user() {
        $this->user = new User();
        return true;
    }
}


