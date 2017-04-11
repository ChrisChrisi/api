<?php

class Jobs_controller extends Base_controller
{

    public function unknown_action($param = null)
    {
        $status = false;
        $code = 404;
        $data = array('message' => 'The requested action is not available');

        switch ($this->_method) {
            case 'get': {
                if ((int)$param > 0 && count($this->_parameters['uri']) == 0) {
                    $data = $this->get_job($param);
                    $code = 200;
                    if (isset($data['id'])) {
                        $status = true;
                    }
                }
                break;
            }
            case 'post': {
                if (!isset($param) && count($this->_parameters['uri']) == 0) {
                    $data = $this->create_job();
                    $code = 200;
                    if (isset($data['id'])) {
                        $status = true;
                    }
                }
                break;
            }
            case 'delete': {
                if ((int)$param > 0  && count($this->_parameters['uri']) == 0) {
                    $data = $this->delete_job($param);
                    $code = 200;
                    if (isset($data['id'])) {
                        $status = true;
                    }
                }
                break;
            }
        }
        self::make_response($status, $code, $data);
    }

    private function get_job($id)
    {
        $query = 'SELECT `jb_id` AS id, `jb_position` AS position, `jb_description` AS description, `jb_created_on` AS crated_on
                  FROM `jobs`
                  WHERE jb_id = :id';
        $params = array(array(':id', $id, PDO::PARAM_INT));
        $job = $this->db->get_row($query, $params);
        if ($job === false) {
            $job = array('message' => 'Requested job not found');
        }

        return $job;

    }

    private function create_job()
    {
        $data = array();
        if (isset($this->_parameters['post']['position']) && isset($this->_parameters['post']['description'])) {
            $query = 'INSERT INTO `jobs` SET `jb_position` = :p, `jb_description` = :d, `jb_created_on` = NOW()';
            $params = array(
                            array(':p', $this->_parameters['post']['position'], PDO::PARAM_STR),
                            array(':d', $this->_parameters['post']['description'], PDO::PARAM_STR)
                           );
            $job = $this->db->query($query, $params);
            if ($job > 0) {
                $data['id'] = $this->db->last_insert_id();
            } else {
                $data['message'] = 'System error occurred. Try again later.';
            }
        } else {
            $data['message'] = 'position and description are required';
        }
        return $data;
    }

    private function delete_job($id){
        $data = array();
        $query = 'DELETE
                  FROM `jobs`
                  WHERE jb_id = :id';
        $params = array(array(':id', $id, PDO::PARAM_INT));
        $affected_r = $this->db->query($query, $params);
        if ($affected_r == 1) {
            $data['id'] = $id;
        } else {
            $data['message'] = 'Requested job not found';
        }
        return $data;
    }

}