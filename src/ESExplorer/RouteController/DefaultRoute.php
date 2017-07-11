<?php
namespace ESExplorer\RouteController;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use ESExplorer\RouteController\AbstractRoute;

class DefaultRoute extends AbstractRoute
{    
    public function connect(Application $app)
    {
        $factory = $app['controllers_factory'];
        $factory->get('/', '\ESExplorer\RouteController\DefaultRoute::getAction');
        $factory->post('/', '\ESExplorer\RouteController\DefaultRoute::postAction');
        return $factory;
    }
    
    public function getAction(Application $app)
    {
        $context = [];
        $this->setContext($context);
        return parent::globalGetAction($app);
    }
    
    public function postAction(Application $app, Request $request)
    {
        $accessToken = $request->request->get('access_token');
        $secretKey = $request->request->get('secret_key');
        $region = $request->request->get('region');
        $server = $request->request->get('server');
        if (!empty($accessToken) && !empty($secretKey) && !empty($region) && !empty($server)) {
            $client = new \ESExplorer\AWSClient($server, $accessToken, $secretKey, $region);
            $client = $client->getClient();
            $indices = $client->indices()->getAliases();
            $context = [
                'host' => $server,
                'indices' => $indices
            ];
            $this->setContext($context);
            return parent::globalGetAction($app);
        } else {
            var_dump('error');
        }
    }
}