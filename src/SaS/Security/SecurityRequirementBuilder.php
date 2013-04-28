<?php
namespace SaS\Security;
/**
 * Description of SecurityRequirementBuilder
 *
 * @author drak3
 */
class SecurityRequirementBuilder {
    
    
    protected $newRequirement = true;
    protected $currentRequirement = 0;
    protected $requirements = [[]];
    
    public function requires() {
        return $this;
    }
    
    public function with() {
        return $this;
    }
    
    public function orRequires() {
        if(!array_key_exists('role', $this->requirements[$this->currentRequirement])) {
            $this->requirements[$this->currentRequirement]['role'] = ''; 
        }
        $this->currentRequirement += 1;
        $this->requirements[$this->currentRequirement] = [];
        return $this;
    }
    
    public function token($token) {
        $this->requirements[$this->currentRequirement]['token_name'] = $token;
        return $this;
    }
    
    public function role($role) {
        $this->requirements[$this->currentRequirement]['role'] = $role;
        return $this;
    }
    
    public function get() {
        return $this->requirements;
    }
}

?>
