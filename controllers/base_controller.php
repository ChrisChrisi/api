<?php


abstract class Base_controller
{
    protected $_parameters,
        $_method,
        $db;


    public function __construct($parameters, $method)
    {
        $this->db = Db::get_instance();
        $this->_parameters = $parameters;
        $this->_method = $method;
    }

    /**
     * generate and output  response
     * @param $success - true - everything is alright, false - something went wrong
     * @param $data - returned data - if no data is returned - empty array
     * @param int $code - response status code
     */
    public function make_response($success, $code = 200, $data = array())
    {
        $response = array(
            'success' => $success,
            'data' => $data
        );
        header('Content-type: application/json');
        $response = json_encode($response);
        http_response_code($code);
        echo $response;
        return;
    }

    //all controllers must contain the unknown method
    abstract function unknown_action($param = null);


}