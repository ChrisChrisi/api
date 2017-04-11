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
        $slice_num = 0;
        if (isset($this->_controller)) {
            $slice_num += 1;
        }
        if (isset($this->_action)) {
            $slice_num += 1;
        }
        $this->_params['uri'] = array_slice($components, $slice_num);

        if ($this->_method === 'post') {
            $this->_params['post'] = $_POST;
        } elseif (in_array($this->_method, array('put', 'delete'))) {
            //if the request content type multipart/form-data parse the raw data and store it
            $data = array();
            if ($_SERVER["CONTENT_TYPE"] === 'application/x-www-form-urlencoded') {
                parse_str(file_get_contents("php://input"), $data);
            }
            $this->_params[$this->_method] = $data;
        }
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