<?php

namespace Classes;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Classes\RequestM;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Description of Routing
 *
 * @author user
 */
class Routing
{

    protected $params;

    public function __construct(RequestM $request, $routes)
    {
        $routeCollection = new RouteCollection();

        foreach ($routes as $path => $controller) {
            $currentRoute = new Route($path, ['controller' => $controller]);
            $routeCollection->add($path, $currentRoute);
        }

        $context = (new RequestContext())->fromRequest($request);

        $matcher = new UrlMatcher($routeCollection, $context);

        try {
            $parameters = $matcher->match('/' . $request->query->get('route', '/'));
        } catch (ResourceNotFoundException $e) {
            $parameters = [
                'controller' => 'IndexController::render',
                '_route'     => 'index',
                'error'      => $e->getMessage()
            ];
        }

        $this->params = explode('::', $parameters['controller']);
    }

    public function getControllerName()
    {
        return '\\Controllers\\' . $this->params[0];
    }

    public function getActionName()
    {
        return $this->params[1];
    }

}
