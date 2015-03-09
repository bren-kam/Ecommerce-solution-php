<?php
/**
 * Created by PhpStorm.
 * User: gbrunacci
 * Date: 14/04/14
 * Time: 15:13
 */

class LostYourPasswordController extends BaseController {

    public function __construct() {
        parent::__construct();

        // Tell what is the base
        $this->view_base = 'lost-your-password/';
        $this->section = 'Lost your password';
    }

    /**
     * Index
     *
     * @return AjaxResponse|RedirectResponse
     */
    protected function index() {

        $v = new Validator( 'fLostYourPassword' );
        $v->add_validation( 'email', 'email', _('The "Email" field must contain a valid email address') );

        $errs = false;
        $validation = $v->js_validation();

        // If posted
        if ( $this->verified() && !stristr( $_POST['referer'], '//' )  ) {
            $errs = $v->validate();

            if ( empty( $errs ) ) {

                $user = new User();
                $user->get_by_email( $_POST['email'] );

                if ( $user->id ) {

                    $account = new Account();
                    if ( in_array( $user->role, array( User::ROLE_AUTHORIZED_USER, User::ROLE_MARKETING_SPECIALIST ) ) ) {
                        $accounts = $account->get_by_authorized_user( $user->id );
                    } else {
                        $accounts = $account->get_by_user( $user->id );
                    }
                    $account = array_pop( $accounts );

                    if ( $account->id ) {

                        if ( User::STATUS_ACTIVE == $user->status ) {
                            $date_valid = new DateTime();
                            $date_valid->add( new DateInterval('P1D') );

                            // Create token
                            $token = new Token();
                            $token->user_id = $user->id;
                            $token->type = Token::TYPE_RECOVER_PASSWORD;
                            $token->date_valid = $date_valid->format('Y-m-d H:i:s');
                            $token->create();

                            $message = '<br /><strong>Dear ' . $user->contact_name . ', </strong><br><br>';
                            $message .= 'To generate your new password please click<a href="http://account.' .  $user->domain . '/login/activate/?t=' . $token->key . '">http://account.' .  $user->domain . '/login/activate/?t=' . $token->key .'</a>';
                            $message .= '<br /><br />Please contact ' .  $user->domain . ' if you have any questions. Thank you for your time.<br /><br />';
                            $message .= '<strong>Email:</strong> info@' .  $user->domain . '<br /><strong>Phone:</strong> (800) 549-9206<br /><br />';

                            $intro = new EmailHelper();
                            $intro->to = $user->email;
                            $intro->message = $message;
                            $intro->from = "{$account->title} <{$user->email}>";
                            $intro->subject = $account->title . ' Password recovery ' . DOMAIN . '.';

                            $intro->send();

                            $success = _( 'You have been sent an email with further instructions to recover your password.' );
                            $this->log( 'forgot-password-request', $this->user->contact_name . ' requested password reset.' );
                        }
                    } else {
                        $errs .= _('That email is not registered. Please try again.');
                    }
                } else {
                    $errs .= _('That email is not registered. Please try again.');
                }

            }

            $response = new AjaxResponse( true );
            $response->check( empty( $errs ), $errs );
            if ( !$response->has_error() )
                $response->notify( $success );
            return $response;
        }

        return new RedirectResponse( '/' );
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