<?php

namespace SaS\Util;


/**
 * Description of Signer
 *
 * @author drak3
 */
class Signer {
    
    protected $secret;
    
    public function __construct($secret) {
        $this->secret = $secret;
    }
    
    /**
     * This function calculates the HMAC for the given data
     * This function only claculates HMACs of arrays that include only keys and values of type string and int
     * (This prevents cases where an array and a string get the same HMAC) 
     * @param array $data
     * @return string
     */
    public function sign(array $data) {
        $stringRep = $this->arrayToString($data);
        return $this->calculateHMAC($stringRep, $this->secret);
    }
           
    protected function toString($data) {
        if(is_string($data)) {
            return $data;
        }
        if(is_int($data)) {
            return (string) $data;
        }
        throw new \LogicException('Non-String value passed to sign');
    }
    
    protected function arrayToString(array $arr) {
        $str = '';
        foreach($arr as $key => $val) {
            $key = $this->toString($key);
            $val = $this->toString($val);
            $str .= $key;
            $str .= '=';
            $str .= $val;
            $str .= ';';
        }
        return $str;
    }
    
    /**
     * Calculates the HMAC for a given message and key
     * @param type $data
     * @param type $key
     */
    protected function calculateHMAC($data, $key) {
        return hash_hmac('sha1', $data, $key);
    }
}

?>
