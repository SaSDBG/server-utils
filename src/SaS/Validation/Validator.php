<?php

namespace SaS\Validation;

/**
 * Description of RequestValidator
 *
 * @author drak3
 */
class Validator {
    
    protected $validChars;
    
    public function __construct($validChars) {
        $this->validChars = $validChars;
    }
    
    /**
     * array
     */
    private $currentErrors;
    
    /**
     * 
     * @param array $data
     * @param array $constraints
     * @param boolean $noAdditionals
     * @return ValidationResult
     */
    public function validate(array $data, array $constraints, $noAdditionals=true) {
        $this->reset();
        foreach($data as $key => $val) {
            if(!array_key_exists($key, $constraints)) {
                if($noAdditionals) {
                    $ekey = htmlspecialchars($key); // additional security measure, normally not neccessary
                    $this->addError([999, "Unknown parameter $ekey"]);
                }
                continue;
            }
            $valConstraints = $constraints[$key];
            $data[$key] = $this->validateValue($val, $valConstraints);
        }
        $this->validateRequired($data, $constraints);
        return $this->getValidationResult($data);
    }
    
    protected function validateValue($val, $valConstraints) {
        foreach($valConstraints as $c => $error) {
            switch ($c) {
                case 'int':
                    $val = $this->validateInt($val, $error);
                    break;
                case 'token':
                case 'sha1':
                    $val = $this->validateSHA1($val, $error);
                    break;
                case 'valid-chars':
                    $val = $this->validateValidChars($val, $error);
                    break;
                case 'required':
                    continue;
                    break;
                default:
                    throw new \LogicException("Unkown validator constraint $c");
                    break;
            }
        }
        return $val;
    }
    
    protected function validateInt($val, array $error) {
        // conversion in case it is an all-digit string
        if(is_string($val) && ctype_digit($val)) {
            $val = (int) $val;
        }
        if(!is_int($val)) {
            $this->addError($error);
        }
        return $val;
    }
    
    protected function validateSHA1($val, array $error) {
        if(!is_string($val) || strlen($val) !== 40 || !$this->matches('/^[a-f0-9]$/', $val)) {
            $this->addError($error);
        } 
        
        return $val;
    }
    
    protected function validateValidChars($val, array $error) {
        if(!is_string($val)) {
            $this->addError($error);
        } else {
            $pattern = sprintf('/^[%s]$/', preg_quote($this->validChars, '/'));
            if(!$this->matches($pattern, $val)) {
                $this->addError($error);
            }
        }
        return $val;
    }
    
    protected function validateRequired($data, $constraints) {
        foreach($constraints as $key => $valConstraints) {
            if(array_key_exists('required', $valConstraints)) {
                $this->requireData($key, $data, $valConstraints['required']);
            }
        }
    }
    
    protected function requireData($name, array $data, array $error) {
        if(!array_key_exists($name, $data)) {
            $this->addError($error);
        }
    }
    
    private function reset() {
        $this->currentErrors = [];
    }
    
    protected function addError(array $error) {
        $this->currentErrors[] = $error;
    } 
    
    protected function getValidationResult($data) {
        if($this->currentErrors === []) {
            return new ValidationSuccess($data);
        } else {
            return new ValidationFailure($this->currentErrors);
        }
    }
    
    private function matches($pattern, $string) {
        $matches = preg_match($pattern, $string);
        if($matches === false) { // preg_match returns false on failure and 0 when no match was found
                throw new \Exception("Could not complete preg_match");
        }
        if($matches === 0) {
            return false;
        } else {
            return true;
        }
    }
} 

?>
