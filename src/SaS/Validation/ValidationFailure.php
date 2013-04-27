<?php

namespace SaS\Validation;

/**
 * Description of ValidationFailure
 *
 * @author drak3
 */
class ValidationFailure implements ValidationResult {
    protected $errors;
    
    public function __construct(array $errors) {
        $this->errors = $errors;
    }
    
    public function getErrors() {
        return $this->errors;
    }
}

?>
