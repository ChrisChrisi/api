<?php

class Candidates_controller extends Base_controller
{

    public function review_action()
    {

        $status = false;
        $code = 404;
        $data = array('message' => 'The requested action is not available');
        $c_id = isset($this->_parameters['uri'][0]) ? $this->_parameters['uri'][0] : null;
        switch ($this->_method) {
            case 'get': {
                if (isset($c_id) && is_numeric($c_id) && count($this->_parameters['uri']) == 1) {
                    $data = $this->get_candidate($c_id);
                    $code = 200;
                }
                break;
            }
            case 'post': {
                if (!isset($c_id) && count($this->_parameters['uri']) == 0) {
                    $data = $this->create_candidate();
                    $code = 200;
                }
                break;
            }
            case 'put': {
                if (isset($c_id) && is_numeric($c_id) && count($this->_parameters['uri']) == 1) {
                    $data = $this->update_candidate($c_id);
                    $code = 200;
                }
                break;
            }
            case 'delete': {
                if (isset($c_id) && is_numeric($c_id) && count($this->_parameters['uri']) == 1) {
                    $data = $this->delete_candidate($c_id);
                    $code = 200;
                }
                break;
            }
        }
        if ($code == 200 && isset($data['id'])) {
            $status = true;
        }
        self::make_response($status, $code, $data);
        return;
    }

    public function list_action()
    {

        if ($this->_method !== 'get' || count($this->_parameters['uri']) > 0) {
            $status = false;
            $code = 404;
            $data = array('message' => 'The requested action is not available');
            self::make_response($status, $code, $data);
            return;
        }
        $query = 'SELECT `cn_id` AS id, `cn_name` AS name, `cn_jb_id` AS job_id, `jb_position` as job_position, `jb_description` as job_description, `cn_created_on` AS crated_on
                  FROM `candidates`
                  LEFT JOIN jobs ON (jobs.`jb_id` = candidates.`cn_jb_id`)';

        $data = $this->db->get_all($query);
        $status = true;
        $code = 200;
        self::make_response($status, $code, $data);
        return;
    }

    public function search_action()
    {
        if ($this->_method !== 'get' || count($this->_parameters['uri']) != 1) {
            $status = false;
            $code = 404;
            $data = array('message' => 'The requested action is not available');
            self::make_response($status, $code, $data);
            return;
        }
        $val = $this->_parameters['uri'][0];

        // if number get all candidates with the given job id
        if (is_numeric($val)) {
            $query = 'SELECT `cn_id` AS id, `cn_name` AS name, `cn_jb_id` AS job_id, `jb_position` as job_position, `jb_description` as job_description, `cn_created_on` AS crated_on
                  FROM `candidates`
                  LEFT JOIN jobs ON (jobs.`jb_id` = candidates.`cn_jb_id`)
                  WHERE `cn_jb_id` = :i';
            $params = array(array(':i', $val, PDO::PARAM_INT));
            $data = $this->db->get_all($query, $params);
        } else {
            $query = 'SELECT `cn_id` AS id, `cn_name` AS name, `cn_jb_id` AS job_id, `jb_position` as job_position, `jb_description` as job_description, `cn_created_on` AS crated_on
                  FROM `candidates`
                  LEFT JOIN jobs ON (jobs.`jb_id` = candidates.`cn_jb_id`)
                  WHERE `cn_name` = :n';
            $params = array(array(':n', $val, PDO::PARAM_STR));
            $data = $this->db->get_all($query, $params);
            if ($data === false) {
                $data = array('message' => 'Requested candidate not found');
            }
        }

        $status = true;
        $code = 200;
        self::make_response($status, $code, $data);
        return;
    }

    public function unknown_action($param = null)
    {
        $status = false;
        $code = 404;
        $data = array('message' => 'The requested action is not available');
        self::make_response($status, $code, $data);
        return;
    }

    /**
     * return candidate by its id
     * @param $id - requested candidate id
     * @return array|mixed
     */
    private function get_candidate($id)
    {
        $query = 'SELECT `cn_id` AS id, `cn_name` AS name, `cn_jb_id` AS job_id, `jb_position` as job_position, `jb_description` as job_description, `cn_created_on` AS crated_on
                  FROM `candidates`
                  LEFT JOIN jobs ON (jobs.`jb_id` = candidates.`cn_jb_id`)
                  WHERE cn_id = :id';
        $params = array(array(':id', $id, PDO::PARAM_INT));
        $cn = $this->db->get_row($query, $params);
        if ($cn === false) {
            $cn = array('message' => 'Requested candidate not found');
        }

        return $cn;

    }

    /**
     * create new candidate
     * requested post parameters: 'name', 'job_id'
     * @return array - returns created candidate's id when successful
     */
    private function create_candidate()
    {
        $data = array();
        if (isset($this->_parameters['post']['name']) && isset($this->_parameters['post']['job_id'])) {
            $name = $this->_parameters['post']['name'];
            $job_id = $this->_parameters['post']['job_id'];
            if (!is_string($name) || strlen($name) < 3) {
                $data['message'] = 'name should be string with at least 3 characters';
                return $data;
            }
            if (!is_numeric($job_id)) {
                $data['message'] = 'job_id should be a number';
                return $data;
            }
            $jquery = "SELECT jb_id FROM jobs WHERE jb_id = :jid";
            $jparams = array(array(':jid', $job_id, PDO::PARAM_INT));
            $cn = $this->db->get_row($jquery, $jparams);
            if ($cn === false) {
                $data['message'] = 'Invalid job_id';
                return $data;
            }
            $query = 'INSERT INTO `candidates` SET `cn_name` = :n, `cn_jb_id` = :i, `cn_created_on` = NOW()';
            $params = array(
                array(':n', $name, PDO::PARAM_STR),
                array(':i', $job_id, PDO::PARAM_INT)
            );
            $candidate = $this->db->query($query, $params);
            if ($candidate > 0) {
                $data['id'] = $this->db->last_insert_id();
            } else {
                $data['message'] = 'System error occurred. Try again later.';
            }
        } else {
            $data['message'] = 'name and job_id are required';
        }
        return $data;
    }

    private function update_candidate($id)
    {
        $update_vals = '';
        $params = array();
        if (isset($this->_parameters['put']['name'])) {
            $name = $this->_parameters['put']['name'];
            if (!is_string($name) || strlen($name) < 3) {
                $data['message'] = 'name should be string with at least 3 characters';
                return $data;
            }
            $update_vals .= ' `cn_name` = :n';
            $params[] = array(':n', $name, PDO::PARAM_STR);
        }
        if (isset($this->_parameters['put']['job_id'])) {
            $data['message'] = 'candidate job_id cannot be changed';
            return $data;
        }
        if (strlen($update_vals) == 0) {
            $data['message'] = 'nothing to update';
            return $data;
        }
        $update_vals = trim($update_vals, ', ');
        $query = 'UPDATE `candidates` SET ' . $update_vals . ' WHERE `cn_id` = :id';
        $params[] = array(':id', $id, PDO::PARAM_INT);
        $ucandidate = $this->db->query($query, $params);
        if ($ucandidate > 0) {
            $data['id'] = $id;
        } else {
            $data['message'] = 'nothing was updated.';
        }
        return $data;
    }

    /**
     * Delete candidate
     * @param $id - candidate's id
     * @return array
     */
    private function delete_candidate($id)
    {
        $data = array();
        $query = 'DELETE
                  FROM `candidates`
                  WHERE cn_id = :id';
        $params = array(array(':id', $id, PDO::PARAM_INT));
        $affected_r = $this->db->query($query, $params);
        if ($affected_r == 1) {
            $data['id'] = $id;
        } else {
            $data['message'] = 'Requested candidate not found';
        }
        return $data;
    }

}