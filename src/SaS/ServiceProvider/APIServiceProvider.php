<?php
namespace SaS\ServiceProvider;

use SaS\Token\TokenRegistry;
use SaS\Validation\Validator;
use SaS\Security\SecurityRequirementChecker;
use SaS\Security\NullAuthenticator;
use SaS\Controller\ControllerManager;

use Silex\ServiceProviderInterface;
use Silex\Application;

/**
 * Description of APIServiceProvider
 *
 * @author drak3
 */
class APIServiceProvider implements ServiceProviderInterface {
    
    /**
     * Requires following options/services:
     *  logger                  A PSR-3 Logger
     *  token.token_file        File with the given tokens (php assoc array)
     *  security.authenticator  Instance of AuthenticatorInterface, 
     *                          preconfigured are security.authenticator.null (throws when invoked)
     *                                        and security.authenticator.auth_server (speaks to an auth-server, requires sasdbg/auth-client)
     *  security.authenticator.auth_server.url     The Adress of the auth-server, only needed when using security.authenticator.auth_server                                                               
     * @param \Silex\Application $app
     * @return void
     * @throws \RuntimeException
     */
    public function register(Application $app)
    {
        $app['token.loader'] = $app->protect(function($fileName) use ($app) {
            if(is_string($fileName) && file_exists($fileName) && is_readable($fileName)) {
                return require($fileName);
            }
            throw new \RuntimeException("Could not load tokenfile");
        });
        
        $app['token.registry'] = $app->share(function() use ($app) {
            $reg = new TokenRegistry;
            $givenTokens = $app['token.loader']($app['token.token_file']);
            $reg->setGivenTokens($givenTokens);
            return $reg;
        });
        
        $app['validator'] = $app->share(function() use ($app) {
            if(isset($app['validator.valid_chars'])) {
                $validChars = $app['validator.valid_chars'];
            } else {
                $validChars = 'a-zA-Z0-9()&\/';
            }
            return new Validator($validChars);
        });
        
        $app['security.authenticator.null'] = $app->share(function() use ($app) {
            return new NullAuthenticator();
        });
        
        $app['security.authenticator.auth_server'] = $app->share(function() use ($app) {
            //todo: implement this service
        });
        
        $app['security.checker'] = $app->share(function() use ($app) {
            return new SecurityRequirementChecker($app['token.registry'], $app['security.authenticator']);
        });
        
        $app['api.controller_manager'] = $app->share(function() use ($app) {
            //todo integrate with logger
            return new ControllerManager($app['validator'], $app['security.checker']);
        });
    }

    /**
     * Requires the following options/services:
     *  api.controllers         The Controllers for the API (array of \SaS\Controller\ControllerInterface)
     * @param \Silex\Application $app
     */
    public function boot(Application $app)
    {
        $app['api.controller_manager']->setControllers($app['api.controllers']);
        $app->mount('/', $app['api.controller_manager']);
    }
}

?>
