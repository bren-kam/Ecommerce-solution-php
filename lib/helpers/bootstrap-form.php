<?php
/**
 * Handles standard form creation
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class BootstrapForm {
    /**
     * Hold variables
     */
    protected $name;
    protected $action;
    protected $method;
    protected $fields;
    protected $errs = '';
    protected $submit;
    protected $submit_classes;
    protected $attributes = array();
    protected $submit_column = 2;

    /**
     * Hold BootstrapValidator
     * @var BootstrapValidator
     */
    protected $v;

    /**
     * Hold Resources object
     * @var Resources
     */
    protected $resources;

    /**
     * Constructor -- Create the name
     *
     * @param string $name
     * @param string $action [optional]
     * @param string $method [optional]
     */
    public function __construct( $name, $action = '', $method = 'post' ) {
        $this->name = $name;
        $this->action = $action;
        $this->method = $method;

        // Need to set this
        $this->submit = _('Save');
    }

    /**
     * Set the action dynamically
     *
     * @param string $action
     */
    public function set_action( $action ) {
        $this->action = $action;
    }

    /**
     * Add a field
     *
     * @param string $type
     * @param string $nice_name
     * @param string $name [optional]
     * @param string $value [optional] the preset value of the field (other than the post)
     * @return BootstrapForm_Field|BootstrapForm_Select
     */
    public function add_field( $type, $nice_name, $name = '', $value = '' ) {
        $class = 'BootstrapForm_' . ucwords( $type );
        $field = new $class( $nice_name, $name, $value );

        // Tie them together
        $this->fields[] = $field;

        return $field;
    }

    /**
     * Submit Button
     *
     * @param string $text
     * @param string $classes [optional] Space separate values
     * @param int $column [optional]
     * @return FormTable
     */
    public function submit( $text, $classes = '', $column = 2 ) {
        $this->submit = $text;

        if ( !empty( $classes ) )
            $this->submit_classes .= " $classes";

        $this->submit_column = $column;

        return $this;
    }

    /**
     * Sets an attribute
     *
     * @param string $attribute
     * @param string $value
     * @return FormTable
     */
    public function attribute( $attribute, $value ) {
        $this->attributes[] = $attribute . '="' . $value . '"';
        return $this;
    }

    /**
     * Error
     *
     * @param string $error
     */
    public function error( $error ) {
        $this->errs .= $error . "<br />";
    }

    /**
     * Generate Form
     *
     * @return string
     */
    public function generate_form() {
        $this->_validator();
        $attributes = ( 0 == count( $this->attributes ) ) ? '' : ' ' . implode( ' ', $this->attributes );
		$html = $hidden = '';

        if ( !is_null( $this->errs ) && !empty( $this->errs ) )
            $html .= '<div class="alert alert-danger">' . $this->errs . '</div>';

        $html .= '<form name="' . $this->name . '" id="' . $this->name . '" action="' . $this->action . '" method="' . $this->method . '"' . $attributes . ' role="form">';

        $html .= '<div class="row"><div class="col-lg-12">';

        // Count
        $i = 0;
        foreach ( $this->fields as $f ) {
            // The count will make sure radio buttons have unique IDs
            $generated_html = $f->generate_html( $i );
            if ( 'hidden' == $f->get_type() ) {
                $hidden .= $generated_html;
            } else {
                $html .= $generated_html;
            }
            $i++;
        }
        $html .= '</div></div>';

        $html .= '<div class="row"><div class="col-lg-12"><button type="submit" class="btn btn-primary '. $this->submit_classes .'">' . $this->submit . '</button></div></div>';

        $html .= nonce::field( 'form-' . $this->name, '_nonce', false );
        $html .= $hidden;
        $html .= '</form>';

        $html .= $this->v->js_validation();

        return $html;
    }

    /**
     * Determine if it was posted
     *
     * @return bool
     */
    public function posted() {
        if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'form-' . $this->name ) ) {
            $this->_validator();
            $this->errs = $this->v->validate();
            return empty( $this->errs );
        }

        return false;
    }

    /**
     * Instantiate the validator class
     */
    private function _validator() {
        if ( is_null( $this->v ) ) {
            $this->v = new BootstrapValidator( $this->name );

            foreach ( $this->fields as $f ) {
               $f->validation( $this->v );
            }
       }
    }
}

abstract class BootstrapForm_Field {
    /**
     * Hold variables
     */
    protected $type;
    protected $nice_name;
    protected $name;
    protected $value;
    protected $validation = NULL;
    protected $attributes = array();
    protected $required = false;

    /**
     * Constructor -- Create a field with validation options
     *
     * @param string $nice_name
     * @param string $name [optional]
     * @param string $value [optional] the preset value of the field (other than the post)
     */
    public function __construct( $nice_name, $name = '', $value = '' ) {
        if ( is_array( $value ) ) {
            switch( strtoupper( $value[0] ) ) {
                case 'GET':
                    $value = isset( $_GET[$value[1]] ) ? $_GET[$value[1]] : '';
                break;

                case 'POST':
                    $value = isset( $_POST[$value[1]] ) ? $_POST[$value[1]] : '';
                break;

                default:break;
            }
        }

        $this->nice_name = $nice_name;
        $this->name = ( empty( $name ) ) ? format::slug( $nice_name ) : $name;
        $this->value = ( isset( $_POST[$name] ) ) ? $_POST[$name] : $value;
    }

    /***** METHOD CHAINING *****/

    /**
     * Add Validation
     *
     * @param string $type
     * @param string $message
     * @return BootstrapForm_Field
     */
    public function add_validation( $type, $message ) {
        switch ( $type ) {
            case 'req':
            case 'required':
                $this->required = true;
            break;
        }

        $this->validation[$type] = $message;

        return $this;
    }

    /**
     * Sets an attribute
     *
     * @param string $attribute
     * @param string $value
     * @return BootstrapForm_Field
     */
    public function attribute( $attribute, $value ) {
        $this->attributes[] = $attribute . '="' . $value . '"';

        return $this;
    }

    /***** OTHER METHODS *****/

    /**
     * Get Type
     *
     * @return string
     */
    public function get_type() {
        return $this->type;
    }

    /**
     * Set Value
     *
     * @param string $value
     */
    public function set_value( $value ) {
        $this->value = $value;
    }

    /**
     * Validation
     *
     * Adds validation to the validator
     *
     * @param Validator $v
     */
    public function validation( $v ) {
        if ( !is_null( $this->validation ) )
        foreach ( $this->validation as $type => $message ) {
            $v->add_validation( $this->name, $type, $message );
        }
    }

    /***** PROTECTED METHODS *****/

    /**
     * Format attributes
     *
     * @return string
     */
    protected function format_attributes() {
        return ( 0 == count( $this->attributes ) ) ? '' : ' ' . implode( ' ', $this->attributes );
    }

    /**
     * Get the ID based off the name
     *
     * @param int|string $count [optional]
     * @return string
     */
    protected function id( $count = '' ) {
        return preg_replace( '/[^a-zA-Z0-9_-]/', '', $this->name . $count );
    }

    /***** ABSTRACT METHODS *****/
    abstract public function generate_html( $count = 0 );
}

/**
 * Text Field
 */
class BootstrapForm_Text extends BootstrapForm_Field {
    /**
     * Constructor -- Create a field with validation options
     *
     * @param string $nice_name
     * @param string $name [optional]
     * @param string $value [optional] the preset value of the field (other than the post)
     */
    public function __construct( $nice_name, $name = '', $value = '' ) {
        parent::__construct( $nice_name, $name, $value );

        $this->type = 'text';
    }

    /**
     * Generate just the HTML
     *
     * @return string
     */
    public function generate() {
        return '<input type="text" class="form-control" name="' . $this->name . '" id="' . $this->id() . '" value="' . $this->value . '"' . $this->format_attributes() .' />';
    }

    /**
     * Generate HTML
     *
     * @param int $count [optional]
     * @return string
     */
    public function generate_html( $count = 0 ) {
        $html = '<div class="form-group">';

        $html .= '<label for="' . $this->id() . '">' . $this->nice_name . ':';
        if ( $this->required )
            $html .= '*';
        $html .= '</label>';

        $html .= $this->generate();

        $html .= '</div>';

        return $html;
    }
}
/**
 * Textarea Field
 */
class BootstrapForm_Textarea extends BootstrapForm_Field {
    /**
     * Constructor -- Create a field with validation options
     *
     * @param string $nice_name
     * @param string $name [optional]
     * @param string $value [optional] the preset value of the field (other than the post)
     */
    public function __construct( $nice_name, $name = '', $value = '' ) {
        parent::__construct( $nice_name, $name, $value );

        $this->type = 'textarea';
    }

    /**
     * Generate just the HTML
     *
     * @return string
     */
    public function generate() {
        return '<textarea name="' . $this->name . '" id="' . $this->id() . '" cols="50" rows="3" class="form-control" ' . $this->format_attributes() .'>' . $this->value . '</textarea>';
    }

    /**
     * Generate HTML
     *
     * @param int $count [optional]
     * @return string
     */
    public function generate_html( $count = 0 ) {
        $html = '<div class="form-group">';

        $html .= '<label for="' . $this->id() . '">' . $this->nice_name . ':';
        if ( $this->required )
            $html .= '*';
        $html .= '</label>';

        $html .= $this->generate();

        $html .= '</div>';

        return $html;
    }
}

/**
 * Password Field
 */
class BootstrapForm_Password extends BootstrapForm_Field {
    /**
     * Constructor -- Create a field with validation options
     *
     * @param string $nice_name
     * @param string $name [optional]
     * @param string $value [optional] the preset value of the field (other than the post)
     */
    public function __construct( $nice_name, $name = '', $value = '' ) {
        parent::__construct( $nice_name, $name, $value );

        $this->type = 'password';
    }

    /**
     * Generate HTML
     *
     * @param int $count [optional]
     * @return string
     */
    public function generate_html( $count = 0 ) {
        $html = '<div class="form-group">';

        $html .= '<label for="' . $this->id() . '">' . $this->nice_name . ':';
        if ( $this->required )
            $html .= '*';
        $html .= '</label>';

        $html .= '<input type="password" class="form-control" name="' . current( explode( '|', $this->name ) ) . '" id="' . $this->id() . '" value="' . $this->value . '"' . $this->format_attributes() .' />';

        $html .= '</div>';

        return $html;
    }
}

/**
 * Select Field
 */
class BootstrapForm_Select extends BootstrapForm_Field {
    /**
     * Hold the options for the select
     * @var array
     */
    protected $options = array();

    /**
     * Constructor -- Create a field with validation options
     *
     * @param string $nice_name
     * @param string $name [optional]
     * @param string $value [optional] the preset value of the field (other than the post)
     */
    public function __construct( $nice_name, $name = '', $value = '' ) {
        parent::__construct( $nice_name, $name, $value );

        $this->type = 'select';

        return $this;
    }

    /**
     * Sets the options if its a select
     *
     * @param array $options
     * @return BootstrapForm_Select
     */
    public function options( array $options ) {
        $this->options = $options;

        return $this;
    }

    /**
     * Generate Select
     *
     * @return string
     */
    public function generate() {
        $html = '<select name="' . $this->name . '" id="' . preg_replace( '/[\[\]]/', '', $this->id() ) . '" class="form-control" ' . $this->format_attributes() . '>';

        foreach ( $this->options as $option_value => $option_name ) {
            if ( is_array( $this->value ) ) {
                $selected = ( in_array( $option_value, $this->value ) ) ? ' selected="selected"' : '';
            } else {
                $selected = ( $this->value == $option_value ) ? ' selected="selected"' : '';
            }
            $html .= '<option value="' . $option_value . '"' . $selected . '>' . $option_name . '</option>';
        }

        $html .= '</select>';

        return $html;
    }

    /**
     * Generate HTML
     *
     * @param int $count [optional]
     * @return string
     */
    public function generate_html( $count = 0 ) {
        $html = '<div class="form-group">';

        $html .= '<label for="' . $this->id() . '">' . $this->nice_name . ':';
        if ( $this->required )
            $html .= '*';
        $html .= '</label>';

        $html .= $this->generate();

        $html .= '</div>';

        return $html;
    }
}

/**
 * Radio button
 */
class BootstrapForm_Radio extends BootstrapForm_Field {
    /**
     * Constructor -- Create a field with validation options
     *
     * @param string $nice_name
     * @param string $name [optional]
     * @param string $value [optional] the preset value of the field (other than the post)
     */
    public function __construct( $nice_name, $name = '', $value = '' ) {
        parent::__construct( $nice_name, $name, $value );

        $this->type = 'radio';
    }

    /**
     * Generate HTML
     *
     * @param int $count [optional]
     * @return string
     */
    public function generate_html( $count = 0 ) {
        $html = '<div class="radio">';

        $html .= '<label>';

        $checked = ( $_POST[$this->name] == $this->value ) ? ' checked="checked"' : '';

        $checked = array_search( 'checked="checked"' , $this->attributes) !== FALSE ? ' checked="checked"' : $checked;

        $html .= '<input type="radio" name="' . $this->name . '" id="' . $this->id( $count ) . '" value="' . $this->value . '" ' . $checked . ' />';

        $html .= $this->nice_name;
        if ( $this->required )
            $html .= '*';

        $html .= '</label>';

        $html .= '</div>';

        return $html;
    }
}


/**
 * Checkbox field
 */
class BootstrapForm_Checkbox extends BootstrapForm_Field {
    /**
     * Constructor -- Create a field with validation options
     *
     * @param string $nice_name
     * @param string $name [optional]
     * @param string $value [optional] the preset value of the field (other than the post)
     */
    public function __construct( $nice_name, $name = '', $value = '' ) {
        parent::__construct( $nice_name, $name, $value );

        $this->type = 'checkbox';
    }

    /**
     * Generate HTML
     *
     * @param int $count [optional]
     * @return string
     */
    public function generate_html( $count = 0 ) {
        $html = '<div class="checkbox">';

        $html .= '<label>';

        $checked = ( '1' == $this->value ) ? ' checked="checked"' : '';

        $html .= '<input type="checkbox" name="' . $this->name . '" id="' . $this->id( $count ) . '" value="1" '. $checked .' />';

        $html .= $this->nice_name;
        if ( $this->required )
            $html .= '*';

        $html .= '</label>';

        $html .= '</div>';

        return $html;
    }
}

/**
 * Hidden field
 */
class BootstrapForm_Hidden extends BootstrapForm_Field {
    /**
     * Constructor -- Create a field with validation options
     *
     * @param string $name
     * @param string $value [optional] the preset value of the field (other than the post)
     */
    public function __construct( $name, $value = '' ) {
        parent::__construct( '', $name, $value );

        $this->type = 'hidden';
    }

    /**
     * Generate HTML
     *
     * @param int $count [optional]
     * @return string
     */
    public function generate_html( $count = 0 ) {
        return '<input type="hidden" name="' . $this->name . '" id="' . $this->id() . '" value="' . $this->value . '"' . $this->format_attributes() .' />';
    }
}

/**
 * Title Row
 */
class BootstrapForm_Title extends BootstrapForm_Field {
    /**
     * Constructor -- Create a title row
     *
     * @param string $name
     */
    public function __construct( $name ) {
        parent::__construct( $name );

        $this->type = 'title';
    }

    /**
     * Generate HTML
     *
     * @param int $count [optional]
     * @return string
     */
    public function generate_html( $count = 0 ) {
        return '<h3>' . $this->nice_name . '</h3>';
    }
}

/**
 * Anchor Row
 */
class BootstrapForm_Anchor extends BootstrapForm_Field {
    /**
     * Constructor -- Create a Anchor row
     *
     * @param string $name
     */
    public function __construct( $nice_name, $name ) {
        parent::__construct( $nice_name, $name );
    }

    /**
     * Generate HTML
     *
     * @param int $count [optional]
     * @return string
     */
    public function generate_html( $count = 0 ) {
        return '<p><a id="' . $this->name . '" ' . $this->format_attributes() . '>' . $this->nice_name . '</a></p>';
    }
}

/**
 * Image Row
 */
class BootstrapForm_Image extends BootstrapForm_Field {
    /**
     * Constructor -- Create a Anchor row
     *
     * @param string $name
     */
    public function __construct( $name ) {
        parent::__construct( $name );
    }

    /**
     * Generate HTML
     *
     * @param int $count [optional]
     * @return string
     */
    public function generate_html( $count = 0 ) {
        return '<p id="' . $this->name . '"><img ' . $this->format_attributes() . ' alt="' . $this->nice_name .'"></p>';
    }
}

/**
 * Empty Div Row
 */
class BootstrapForm_Block extends BootstrapForm_Field {
    /**
     * Constructor -- Create a title row
     * @param string $name
     */
    public function __construct($name) {
        parent::__construct( $name );
    }

    /**
     * Generate HTML
     *
     * @param int $count [optional]
     * @return string
     */
    public function generate_html( $count = 0 ) {
        return '<div id="'. $this->name .'">' . $this->value . '</div>';
    }
}

/**
 * Blank Row
 */
class BootstrapForm_Blank extends BootstrapForm_Field {
    /**
     * Constructor -- Create a title row
     *
     */
    public function __construct() {
        parent::__construct( '' );

        $this->type = 'blank';
    }

    /**
     * Generate HTML
     *
     * @param int $count [optional]
     * @return string
     */
    public function generate_html( $count = 0 ) {
        return '<br />';
    }
}

/**
 * Blank Row
 */
class BootstrapForm_Row extends BootstrapForm_Field {
    /**
     * Constructor -- Create a row
     *
     * @param string $name
     * @param string $value [optional] the preset value of the field (other than the post)
     */
    public function __construct( $name, $value = '' ) {
        parent::__construct( '', $name, $value );

        $this->type = 'row';
    }

    /**
     * Generate HTML
     *
     * @param int $count [optional]
     * @return string
     */
    public function generate_html( $count = 0 ) {
        return '<p class="form-group"><strong>' . $this->name . '</strong>' . $this->value . '</p>';
    }
}


/**
 * Blank Row
 */
class BootstrapForm_Button extends BootstrapForm_Field {
    /**
     * Constructor -- Create a row
     *
     * @param string $name
     * @param string $value [optional] the preset value of the field (other than the post)
     */
    public function __construct( $name, $value = '' ) {
        parent::__construct( '', $name, $value );

        $this->type = 'file';
    }

    /**
     * Generate HTML
     *
     * @param int $count [optional]
     * @return string
     */
    public function generate_html( $count = 0 ) {
        return '<button id="'. $this->value.'" name="'.$this->value.'" class="btn btn-md btn-primary">'.$this->name.'</button>';
    }  

   
}

