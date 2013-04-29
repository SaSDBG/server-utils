<?php

namespace SaS\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * Description of ResponseBuilder
 *
 * @author drak3
 */
class ResponseBuilder {
    
    public static function buildErrorResponse(array $errors, $code, Response $response=null) {
        $errorResponseData = [];
        foreach($errors as $error) {
            $errorResponseData[] = ['code' => $error[0], 'message' => $error[1]];
        }
        $jsonResponseData = json_encode($errorResponseData, JSON_PRETTY_PRINT);
        
        if($response === null) {
            $response = new Response();
        }
        
        $response->setStatusCode($code);
        $response->setContent($jsonResponseData);
        $response->headers->set('content-type', 'application/json');
        
        return $response;
    }
}

?>
