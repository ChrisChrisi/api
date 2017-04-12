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
            $data = array();
            if (0 === strpos($_SERVER["CONTENT_TYPE"], 'multipart/form-data')) {
                $data  = $this->parse_multipart_form_data();
            } elseif ($_SERVER["CONTENT_TYPE"] === 'application/x-www-form-urlencoded') {
                $data = array();
                parse_str(file_get_contents("php://input"), $data);
            }
            $this->_params[$this->_method] = $data;
        }
    }
    private function parse_multipart_form_data()
    {
        $input_data = fopen("php://input", "r");

        // read the data 1 KB at a time and write to the variable
        $raw_data = '';
        while ($chunk = fread($input_data, 1024)) {
            $raw_data .= $chunk;
        }
        fclose($input_data);

        // fetch content and determine boundary
        $data = array();
        $files = array();
        $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));
        if (empty($boundary)) {
            parse_str($raw_data, $data);
            return array('data' => $data, 'files' => $files);
        }

        // fetch each part
        $parts = array_slice(explode($boundary, $raw_data), 1);
        foreach ($parts as $part) {
            // if this is the last part, break
            if ($part == "--\r\n") break;

            // separate content from headers
            $part = ltrim($part, "\r\n");
            list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

            // parse the headers list
            $raw_headers = preg_split("/(\r\n|; )/", $raw_headers);
            $headers = array();
            foreach ($raw_headers as $header) {
                list($name, $value) = preg_split("/(:|=)/", $header);
                $value = str_replace('"', "", $value);
                $headers[strtolower($name)] = ltrim($value, ' ');
            }
            // parse the Content-Disposition to get the field name, etc.
            if (isset($headers['content-disposition'])) {
                //Parse File
                if (!isset($headers['filename'])) {
                    $data[$headers['name']] = substr($body, 0, strlen($body) - 2);
                }
            }
        }
        return $data;
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