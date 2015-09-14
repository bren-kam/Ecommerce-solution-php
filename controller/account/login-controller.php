<?php
class LoginController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        parent::__construct( );

        // Tell what is the base for all login
        $this->view_base = '';
        $this->section = 'Login';
        $this->title = 'Login';
    }

    /**
     * Login Page
     *
     * @return CustomResponse|RedirectResponse
     */
    protected function index() {
        // Get the template response
        $custom_response = new CustomResponse( $this->resources, $this->view_base . 'login', _('Login') );

        $v = new Validator( 'fLogin' );
        $v->add_validation( 'email', 'email', _('The "Email" field must contain a valid email address') );

        $errs = false;
        $validation = $v->js_validation();

        // If posted
        if ( $this->verified() && !stristr( $_POST['referer'], '//' )  ) {
            $errs = $v->validate();

            if ( empty( $errs ) ) {
                // Try to login
                if ( $this->user->login( $_POST['email'], $_POST['password'] ) ) {
                    // Record the login
                    $this->user->record_login();
                    $this->log( 'login', $this->user->contact_name . ' logged in to the account side.' );

                    $account = new Account();
                    $accounts = array_merge(
                        $account->get_by_user( $this->user->id )
                        , $account->get_by_authorized_user( $this->user->id )
                    );
                    if ( empty( $accounts ) ) {
                        if ( $this->user->role >= User::ROLE_COMPANY_ADMIN ) {
                            $errs .= 'You have no Websites found. Are you trying to login to the <a href="//admin.' .DOMAIN. '/login/">administrator tool</a>?';
                        } else {
                            $errs .= 'You have no Websites found.';
                        }
                    } else {

                        // Two Weeks : Two Days
                        $expiration = ( isset( $_POST['remember-me'] ) ) ? 1209600 : 172800;
                        set_cookie( AUTH_COOKIE, base64_encode( security::encrypt( $this->user->email, security::hash( COOKIE_KEY, 'secure-auth' ) ) ), $expiration );
                        
                        if( !isset( $_SESSION['referer'] ) || isset( $_SESSION['referer'] ) && empty( $_SESSION['referer'] ) ) {

                            if($accounts[0]->geomarketing_only()){
                                return new RedirectResponse('/geo-marketing/analytics');
                            }
                            return new RedirectResponse('/');
                        } else {
                            $referer = $_SESSION['referer'];
                            unset( $_SESSION['referer'] );
                            return new RedirectResponse( $referer );
                        }

                    }
                } else {
                    $errs .= _('Your email and password do not match. Please try again.');
                }
            }
        }

        $custom_response->set( compact( 'errs', 'validation' ) );

        return $custom_response;
    }

    /**
     * Activate user
     *
     * @return RedirectResponse|TemplateResponse
     */
    protected function activate() {
        $token = new Token();
        $token->get( $_GET['t'] );

        if ( !$token->id )
            return new RedirectResponse('/login/');

        $form = new BootstrapForm('activate_account');

        $form->add_field( 'password', _('Password'), 'password' )
            ->attribute( 'maxlength', 30 )
            ->add_validation( 'req', _('The "Password" field is required') );

        $form->add_field( 'password', _('Confirm Password'), 'repassword|password' )
            ->attribute( 'maxlength', 30 )
            ->add_validation( 'match', _('The "Password" and "Confirm Password" field must match') );

        if ( $form->posted() ) {
            $password = $_POST["password"];
            $user = new User();
            $user->get($token->user_id);
            $user->set_password($password);

            $token->remove();

            if ( $user->login($user->email, $password ) ) {
                $this->user = $user;
                $this->notify( _('Your account has been successfully activated!') );
                $this->log( 'activate-account', $this->user->contact_name . ' activated their account.' );
                return new RedirectResponse('/');
            }
        }

        $form = $form->generate_form();

        return $this->get_template_response('change-password')
            ->set(compact('form'));
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