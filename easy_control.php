<?php
require_once('easy_db.php');
// create table
class easy_control extends easy_db{
    private $error;
    private $errno;

    // Escape values to prevent SQL injection
    private function escape_value($value) {
        $escaped_value = str_replace("'", "''", $value);
        return $escaped_value;
    }
    
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
                $table_name = $data['table_name'];
                $q = "INSERT INTO `$table_name` (";
                $columns = array();
                $values = array();
                foreach ($data as $key => $value) {
                    if ($key == 'table_name') {
                        continue;
                    }
                    $escaped_value = $this->escape_value($value);
                    $columns[] = "`$key`";
                    $values[] = "'$escaped_value'";
                }
                $q .= implode(', ', $columns);
                $q .= ") VALUES (";
                $q .= implode(', ', $values);
                $q .= ")";
                return $q;
    
                break;
                    
            case "update":
                $table_name = $data['table_name'];
                $q = "UPDATE `" . $table_name . "` SET ";
                foreach ($data['values'] as $key => $value) {
                    $escaped_value = $this->escape_value($value);
                    $q .= "`$key` = '$escaped_value', ";
                }
                $q = rtrim($q, ", ");
                $q .= " WHERE ";
                $i = 0;
                foreach ($data['conditions'] as $key => $value) {
                    $escaped_value = $this->escape_value($value);
                    $q .= "`$key` = '$escaped_value'";
                    if ($i < count($data['conditions']) - 1) {
                        $q .= " AND ";
                    }
                    $i++;
                }
                return $q;
                break;

            case "change_field":
                $table_name = $data['table_name'];
                $old_field = $data['field_name'];
                $new_field = isset($data['field_name_update']) ? str_replace(" ", "_", $data['field_name_update']) : false;
                $options = $data['options'];
                $q = "ALTER TABLE `" . $table_name . "` CHANGE " . $old_field . " " . $new_field . " " . $options;
                return $q;
                break;
            
            case "select":
                $table_name = $data['table_name'];
                $fields = $data['fields'];
                $conditions = isset($data["conditions"]) ? $data["conditions"] : false;
                if ($conditions) {
                    $q = "select " . $fields . " from `" . $table_name . "` where " . $conditions . " ;";
                } else {
                    $q = "select " . $fields . " from `" . $table_name . "`;";
                }
                return $q;
                break;

            case "delete":
                $table_name = $data['table_name'];
                $conditions = isset($data["conditions"]) ? $data["conditions"] : false;
                if ($conditions) {
                    $q = "DELETE FROM `" . $table_name . "` where " . $conditions . " ;";
                } else {
                    $q = "DELETE FROM `" . $table_name . "`;";
                }
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
        echo $q;
        $query = $this->query($q);
        if($query == true){return true;}
        else{return false;};
    }

    // update data in table
    public function update($data){
        $q = $this->query_generator("update", $data);
        $query = $this->query($q);
        if($query == true){return true;}
        else{return false;};
    }

    // change field
    public function change_field($data){
        $q = $this->query_generator("change_field", $data);
        $query = $this->query($q);
        if(!$query){
            echo "Failed with Error: ". $this->error. ":". $this->errno;
        }
    }

    // easy fetch object
    public function easy_object($data){
        $q = $this->query_generator("select", $data);
        $res = $this->fetch_object($q);
        if($res){return $res;}else{return false;}
    }

    // easy fetch assoc all
    public function easy_fetch_assoc_all($data){
        $response = array();
        $q = $this->query_generator("select", $data);
        $res = $this->fetch_object($q);
        while($row = $res->fetch_assoc()) {
            $response[] = $row;
        }
        if($res){return $response;}else{return false;}
    }

    //fetch assoc all
    public function fetch_assoc_all($q){
        $response = array();
        $res = $this->fetch_object($q);
        while($row = $res->fetch_assoc()) {
            $response[] = $row;
        }
        if($res){return $response;}else{return false;}
    }

    // easy fetch object
    public function easy_assoc($data){
        $q = $this->query_generator("select", $data);
        $res = $this->fetch_assoc($q);
        if($res){return $res;}else{return false;}
    }

    // easy fetch array
    public function easy_array($data){
        $q = $this->query_generator("select", $data);
        $res = $this->fetch_array($q);
        if($res){return $res;}else{return false;}
    }

    // easy fetch num rows
    public function easy_num_rows($data){
        $q = $this->query_generator("select", $data);
        $res = $this->num_rows($q);
        if($res){return $res;}else{return false;}
    }

    // delete row
    public function delete_row($data){
        $q = $this->query_generator("delete", $data);
        $query = $this->query($q);
        if(!$query){
            echo "Failed with Error: ". $this->error. ":". $this->errno;
        }
    }
}
