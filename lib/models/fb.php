<?php
class Fb {
    /**
     * Hold the facebook library
     * @var Facebook
     */
    protected $facebook;

    /**
     * List the apps with app IDs and secrets
     * @var array
     */
    protected $apps = array(
        'posting' => array(
            'id' => '268649406514419'
            , 'secret' => '6ca6df4c7e9d909a58d95ce7360adbf3'
        )
    );

	/**
	 * Sets up everything necessary to run a facebook app
	 *
     * @throws InvalidParametersException
	 * @param string $app
	 */
	public function __construct( $app ) {
        // Make sure we have the app they're talking about
        if ( !array_key_exists( $app, $this->apps ) )
            throw new InvalidParametersException( _('You entered an invalid facebook application name') );

        // Get facebook
		library('facebook/facebook');

		// Create our Application instance (replace this with your appId and secret).
		$this->facebook = new Facebook(array(
			'appId' => $this->apps[$app]['id'],
			'secret' => $this->apps[$app]['secret'],
			'cookie' => true,
		));
	}

	// Magic Method -- make it paralel the actual facebook class
	function __call( $method, $arguments ) {
		return call_user_func_array( array( $this->facebook, $method ), $arguments );
	}
}