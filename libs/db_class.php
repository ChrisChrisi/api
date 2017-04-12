<?php

class Db
{
    private static $instance;
    private $connection;

    private function __construct()
    {
        $this->connection = new PDO('mysql:host=' . DBHOST . ';dbname=' . DBNAME, DBUSER, DBPASS);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connection->setAttribute(1002, true); //PDO::MYSQL_ATTR_FOUND_ROWS

        $this->connection->query("SET NAMES 'utf8'");
    }

    public static function get_instance()
    {
        if (empty(self::$instance)) {
            try {
                self::$instance = new Db();
            } catch (PDOException $e) {
                error_log($e->getMessage());
                header('HTTP/1.1 503 Service Temporarily Unavailable');
                header('Status: 503 Service Temporarily Unavailable');
                header('Retry-After: 300');
                exit;
            }
        }

        return self::$instance;
    }


    private function sql_prepare($sql)
    {
        return $this->connection->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    }

    private function sql_query($sql, $params = false)
    {
        $sth = self::sql_prepare($sql);

        if ($params !== false) {
            foreach ($params as $p) {
                $sth->bindParam($p[0], $p[1], $p[2]);
            }
        }

        try {
            $sth->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            header('HTTP/1.1 503 Service Temporarily Unavailable');
            header('Status: 503 Service Temporarily Unavailable');
            header('Retry-After: 300');
            exit;
        }

        return $sth;
    }

    public function query($sql, $params = false)
    {
        $res = self::sql_query($sql, $params);
        return $res->rowCount();
    }

    public function get_row($sql, $params = false, $conf = PDO::FETCH_ASSOC)
    {
        $sth = self::sql_query($sql, $params);
        return $sth->fetch($conf);
    }

    public function get_all($sql, $vars = false, $params = PDO::FETCH_ASSOC)
    {
        $sth = self::sql_query($sql, $vars);

        return $sth->fetchAll($params);
    }

    public function last_insert_id()
    {
        return $this->connection->lastInsertId();
    }

    public function __destruct()
    {
        self::$instance = null;
    }
}