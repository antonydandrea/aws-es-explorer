<?php
namespace ESExplorer\RouteController;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractRoute implements ControllerProviderInterface
{    
    private $layout = 'home.html.twig';
    
    
    private $context = [];
    
    public abstract function connect(Application $app);
    
    public function globalGetAction(Application $app)
    { 
        $response = $this->render($app, $this->layout, $this->context);
        return new Response($response, 200);
    }
    
    protected function render($app, $layout, $context)
    {
        $response = $app['twig']->render($layout, $context);
        return $response;
    }
    
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }
    
    public function setNavLayout($navLayout)
    {
        $this->navLayout = $navLayout;
    }
    
    public function setContext($context = [])
    {
        $this->context = $context;
    }
}