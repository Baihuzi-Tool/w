<?php
header('Content-type: application/json');

require "mysql.php";

class Weight
{
    /** @var  MysqliHandler */
    private $mysqliHandler;
    
    public function __construct($mysqliHandler)
    {
        $this->mysqliHandler = $mysqliHandler;
    }
    
    public function getAll($sort = "asc")
    {
        $sql = "select *,FROM_UNIXTIME(date_index) as `date` from pregnancy_weight order by date_index " . $sort;
        $res = $this->mysqliHandler->query($sql);
        
        return $res;
    }
    
    public function lineAll()
    {
        $res     = $this->getAll();
        $labels  = [];
        $weights = [];
        
        foreach ($res as $re) {
            $labels[]  = $re['date'];
            $weights[] = $re['weight'];
        }
        
        return ['labels' => $labels, 'weights' => $weights];
    }
    
    public function add($weight = 0, $datetime = 0)
    {
        if (!$datetime) {
            $datetime = time();
        }
        if ($weight <= 40 || $weight >= 100) {
            return false;
        }
        
        $sql = "insert into pregnancy_weight (date_index,weight) VALUES ('{$datetime}','{$weight}')";
        $res = $this->mysqliHandler->insert_sql($sql);
        
        return $res;
    }
    
    public function remove($id)
    {
        $sql = "delete from pregnancy_weight where id='{$id}'";
        $res = $this->mysqliHandler->execute_sql($sql);
        
        return $res;
    }
}

$weight = new Weight($db);

$m = $_GET['m'];

switch ($m) {
    case 'add': {
        $w   = $_REQUEST['weight'];
        $res = $weight->add($w);
        break;
    };
    case 'remove': {
        $id  = $_REQUEST['id'];
        $res = $weight->remove($id);
        break;
    };
    case 'all': {
        $res = $weight->lineAll($id);
        break;
    };
    default:
        $res = $weight->getAll('desc');
}

exit(json_encode($res));
