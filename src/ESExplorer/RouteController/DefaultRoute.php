<?php
namespace ESExplorer\RouteController;

use Silex\Application;

use ESExplorer\RouteController\AbstractRoute;

class DefaultRoute extends AbstractRoute
{    
    public function connect(Application $app)
    {
        $factory = $app['controllers_factory'];
        $factory->get('/', '\ESExplorer\RouteController\DefaultRoute::getAction');
        return $factory;
    }
    
    public function getAction(Application $app)
    {
        $context = [];
        $this->setContext($context);
        return parent::globalGetAction($app);
    }
}