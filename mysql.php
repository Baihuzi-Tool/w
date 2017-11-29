<?php
require "config.php";

class MysqliHandler
{
    
    private $host;          // 主机地址
    private $port;          // 端口
    private $user;          // 授权用户
    private $pwd;           // 密码
    private $database;      // 数据库名称
    private $db_charset;    // 数据库字符集
    private $conn;          // 数据库连接资源
    
    public function __construct($conf)
    {
        $this->host       = $conf['host'];
        $this->port       = $conf['port'];
        $this->user       = $conf['user'];
        $this->pwd        = $conf['pwd'];
        $this->database   = $conf['db_name'];
        $this->start_time = microtime(true);
        if (empty($conf['db_charset'])) {
            $this->db_charset = 'utf8';
        }
        else {
            $this->db_charset = strtolower($conf['db_charset']);
        }
        $this->init_connect();
        $this->select_db();
    }
    
    private function init_connect()
    {
        $this->conn = mysqli_connect($this->host, $this->user, $this->pwd, $this->database, $this->port)
        or die("!!!!!!!!!database connect error !");
        
    }
    
    private function select_db()
    {
        mysqli_select_db($this->conn, $this->database);
        
        $set_sql = "set character set " . $this->db_charset;
        mysqli_query($this->conn, $set_sql);
        
        $set_sql = " set names " . $this->db_charset;
        mysqli_query($this->conn, $set_sql);
        
    }
    
    public function query($sql)
    {
        $rs = mysqli_query($this->conn, $sql);
        if ($rs) {
            if (mysqli_num_rows($rs) > 0) {
                while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
                    $records_arr[] = $row;
                }
                
                return $records_arr;
            }
            else {
                return null;
            }
        }
        else {
            return null;
        }
    }
    
    public function query_first_column($sql)
    {
        
        if (strrpos(strtolower($sql), 'union') !== false) {
        }
        
        $rs = mysqli_query($this->conn, $sql);
        
        if (is_resource($rs)) {
            if (mysqli_num_rows($rs) > 0) {
                if ($row = mysqli_fetch_array($rs)) {
                    foreach ($row as $key => $val) {
                        return $val;
                    }
                    
                    return null;
                }
                else {
                    return null;
                }
            }
            else {
                return null;
            }
        }
        else {
            return null;
        }
    }
    
    public function execute_sql($sql)
    {
        return $this->exe_sql_and_log($sql);
    }
    
    public function insert_sql($sql)
    {
        $rs = $this->exe_sql_and_log($sql);
        if ($rs) {
            $id = mysqli_insert_id($this->conn);
            
            return $id;
        }
        else {
            return false;
        }
    }
    
    private function exe_sql_and_log($sql)
    {
        $rs = mysqli_query($this->conn, $sql);
        if ($rs) {
            return true;
        }
        else {
            return false;
        }
    }
    
    public function close()
    {
        mysqli_close($this->conn);
    }
}

$db = new MysqliHandler($config);