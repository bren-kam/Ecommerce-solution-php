<?php
class Template {
    /**
     * Hold available variables
     * @var array
     */
    protected $variables;

    /**
     * Hold available variables
     */
    public function __construct( $variables ) {
        $this->variables = $variables;
    }

    /**
     * Start of the template
     *
     * @param string $title
     * @return string
     */
    public function start( $title ) {
        return '<div id="content"><h1>' . $title . '</h1><br clear="all" /><br />';
    }

    /**
     * End of the template
     *
     * @return string
     */
    public function end() {
        return '</div>';
    }

    /**
     * Return the variable if it exists
     *
     * @param string $string
     * @return string
     */
    public function v( $string ) {
        return ( isset( $this->variables[$string] ) ) ? $this->variables[$string] : '';
    }

    /**
     * Spit out the input value if it exists
     *
     * @param string $string
     */
    public function value( $string ) {
        if ( isset( $this->variables[$string] ) )
            echo ' value="' . $this->variables[$string] . '"';
    }

    /**
     * Spit out the checked status if it exists
     *
     * @param string $string
     */
    public function checked( $string ) {
        if ( isset( $this->variables[$string] ) )
            echo ' checked="checked"';
    }

    /**
     * Show Errors
     */
    public function show_errors() {
        if ( isset( $this->variables['errs'] ) )
            echo '<p class="red">', $this->variables['errs'], '</p>';
    }

    /**
     * Get Head
     */
    public function get_head() {
        // Do stuff
    }

    /**
     * Get Top
     */
    public function get_top() {
        // Do stuff
    }

    /**
     * Get Footer
     */
    public function get_footer() {
        // Do stuff
    }
}