<?php

class Error_controller extends Base_controller
{

    public function unknown_action($param = null)
    {
        $data = array('message' => 'The requested controller not found');
        self::make_response(false, 404, $data);

    }
}