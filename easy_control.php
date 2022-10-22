<?php
require_once('easy_db.php');


// create table
class easy_control extends easy_db{

    // query generator
    private function query_generator($state, $data){
        switch($state){
            case "table":
                $table_name = str_replace(" ", "_", $data[0]["table_name"]);
                $q = "";
                $q = $q."CREATE TABLE `";
                $q = $q.$table_name."`";
                $q = $q." (";
    
                foreach($data as $row){
                    if(isset($row["table_name"])){continue;}
                    $field = str_replace(" ", "_", $row["field"]);
                    $q = $q.$field." ".$row["data_type"]." ";
                    if(isset($row["options"])){
                        $q = $q.$row["options"].", ";
                    }else{
                        $q = $q.",";
                    }
                }
                $q = substr($q, 0, -1);
                $q = $q."); ";
    
                return $q;
                
                break;

            case "insert":
                $keys = array_keys($data);
    
                $q = "INSERT INTO `".$data["table_name"]."` (";
                $key_set = "";
                foreach($keys as $key){
                    if($key == "table_name"){continue;}
                    $key_set = $key_set.$key.",";
                }
                $key_set = substr($key_set, 0,-1);
                
                $value_set = "";
                foreach($data as $value){
                    if($data["table_name"] == $value){continue;}
                    $value_set = $value_set."'".$value."',";
                }
                $value_set = substr($value_set, 0,-1);
                
                $q = $q.$key_set.") VALUES (".$value_set.");";
                
                return $q;
    
                break;
                    
            case "update":
                $table_name = $data['table_name'];
                $q = "UPDATE `" . $table_name . "` SET ";
                foreach ($data['values'] as $key => $value) {
                    $q = $q . $key . " = '" . $value . "',";
                }
                $q = substr($q, 0, -1);
                $q = $q . " WHERE ";
                foreach ($data['conditions'] as $key => $value) {
                    $q = $q . $key . " = '" . $value . "',";
                }
                $q = substr($q, 0, -1);
                return $q;
                break;

            default:
                break;
    
        }
    }
 
    // create new table
    public function create_table($table_field_set){
        $q = $this->query_generator("table", $table_field_set);
        $query = $this->query($q);
        if(!$query){
            echo "Failed with Error no: ". $this->errno;
        }
    }

    // drop table
    public function drop_table($table_name){
        $q = "DROP TABLE `".$table_name."` ;";
        $query = $this->query($q);
        if(!$query){
            echo "Failed with Error: ". $this->error. "$this->errno";
        }
    }

    // insert into table
    public function insert($data){
        $q = $this->query_generator("insert", $data);
        $query = $this->query($q);
        if(!$query){
            echo "Failed with Error: ". $this->error. ":". $this->errno;
        }
    }

    // update data in table
    public function update($data){
        $q = $this->query_generator("update", $data);
        $query = $this->query($q);
        if(!$query){
            echo "Failed with Error: ". $this->error. ":". $this->errno;
        }
    }
}
