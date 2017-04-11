<?php

class Router
{
    protected $_controller,
        $_action,
        $_method,
        $_params = array(),
        $_route,
        $scnd_component;

    public function __construct($route)
    {
        $this->_route = $route;
    }

    /**
     * Parse all the components of the route and request and store them
     *
     * Structure of the route
     */
    private function parse_route()
    {
        //separate the components of the url
        $components = explode('/', $this->_route);

        // set the controller class name
        if (isset($components[0])) {
            $controller = str_replace('-', '_', $components[0]);
            $this->_controller = ucfirst($controller) . '_controller';
        }

        // set the controller action name (always starts with the request method in lowercase)
        $this->_method = strtolower($_SERVER["REQUEST_METHOD"]);
        if (isset($components[1])) {
            // store the second component of the url in case it is param not an action
            $this->scnd_component = $components[1];
            $action = str_replace('-', '_', $components[1]);
            $this->_action = $action . '_action';
        }

        // remove the controller and action parts and store the remaining parts as position parameters
        $this->_params['url_params'] = array_slice($components, 2);
    }

    /**
     * Executes the rigth method from the right class
     * Or execute error response
     */
    public function dispatch()
    {
        $this->parse_route();

        //validate controller and action
        if (!class_exists($this->_controller)) {
            $this->_controller = 'Error_controller';
            $this->_action = 'unknown_action';
        } else {
            if (!method_exists($this->_controller, $this->_action)) {
                $this->_action = 'unknown_action';
            }
        }
        $dispatch = new $this->_controller($this->_params, $this->_method);

        call_user_func_array(array($dispatch, $this->_action), array($this->scnd_component));
    }
}