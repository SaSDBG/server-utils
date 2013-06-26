<?php

namespace SaS\ServiceProvider;

use Silex\ServiceProviderInterface;
use Silex\Application;

/**
 * @author drak3
 */
class DoctrineProvider implements ServiceProviderInterface {
    
    /**
     * Requires:
     * db.params database configuration
     * @param \Silex\Application $app
     * @return void
     */
    public function boot(Application $app) {
        $app['db.connection'] = $app->share(function($c) {
            return \Doctrine\DBAL\DriverManager::getConnection($c['db.params']);
        }); 
    }

    public function register(Application $app) {
        
    }

}

?>
