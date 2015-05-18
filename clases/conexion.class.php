<?php

class conexion {
    
    private $host = 'localhost';
    private $usuario = 'root';
    private $password = 'VjtP5AuasxRyHjZD';
    private $bd = 'bitacora';
    
    private $con = false;
    private $result = array();
    private $link;

    public function conectar()
    {
        
        if(!$this->con)
        {      
            
            $link = mysqli_connect($this->host, $this->usuario, $this->password);
            
            if($link)
            {
                
                $this->link = $link;
                
                $selbd = mysqli_select_db($link, $this->bd);
                
                if($selbd)
                {
                    $this->con = true;
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return true;
        }
    } // End conectar()
    
    public function delete($tabla, $where = null)
    {
        if($this->existeTabla($tabla))
        {
            if($where == null)
            {
                $delete = 'DELETE '.$tabla;
            }
            else
            {
                $delete = 'DELETE FROM '.$tabla.' WHERE '.$where;
            }
            $del = mysqli_query($this->link,$delete);

            if($del)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    } // End delete()
    
    private function existeTabla($tabla)
    {        
        $tablasEnBD = mysqli_query($this->link,'SHOW TABLES FROM '.$this->bd.' LIKE "'.$tabla.'"');
        
        if($tablasEnBD)
        {
            if(mysqli_num_rows($tablasEnBD) == 1)
            {                
                return true;
            }
            else
            {                
                return false;
            }
        }
    } // End existeTabla()
    
    public function getResult()
    {        
        return $this->result;
    } // End getResult()
    
    public function insert($tabla, $values, $rows = null)
    {
        
        if($this->existeTabla($tabla))
        {
            
            $insert = 'INSERT INTO '.$tabla;
            
            if($rows != null)
            {
                $insert .= ' ('.$rows.')';
            }

            for($i = 0; $i < count($values); $i++)
            {
                if(is_string($values[$i]))
                    $values[$i] = '"'.$values[$i].'"';
            }
            
            $values = implode(',',$values);
            $insert .= ' VALUES ('.$values.')';
            //echo "<p>$insert<p>";
            $ins = mysqli_query($this->link,$insert);

            if($ins)
            {
                return true;
            }
            else
            {
                return false;
            }
        } // End if existe tabla
    } // End insert()
    
    public function select($tabla, $rows = '*', $where = null, $order = null)
    {
        
        $q = 'SELECT '.$rows.' FROM '.$tabla;
        //echo "<pre>";
        if($where != null)
            $q .= ' WHERE '.$where;
        if($order != null)
            $q .= ' ORDER BY '.$order;
        echo "<p>$q<p>";
        $query = mysqli_query($this->link, $q);
                
        if($query)
        {
            $this->numResultados = mysqli_num_rows($query);
            
            for($i = 0; $i < $this->numResultados; $i++)
            {
                $r = mysqli_fetch_array($query); // Por aqui desmadrara la cadena
                
                $key = array_keys($r); // Devuelve las claves de un arreglo
                
                for($x = 0; $x < count($key); $x++)
                {
                    // Solo llaves alfanumericas son permitidas
                    // Asignamos valores al arreglo de resultados
                    if(!is_int($key[$x]))
                    {
                        if(mysqli_num_rows($query) > 1){
                            $this->result[$i][$key[$x]] = $r[$key[$x]];
                        } else if(mysqli_num_rows($query) < 1){
                            $this->result = null;
                        } else {
                            $this->result[$key[$x]] = $r[$key[$x]];
                        }
                    }
                } // End for
            } // End for
            return true;
        }
        else
        {
            return false;
        }
    } // End select()
    
    public function update($tabla, $rows, $where)
    {
        if($this->existeTabla($tabla))
        {
            // Parse the where values
            // even values (including 0) contain the where rows
            // odd values contain the clauses for the row
//            for($i = 0; $i < count($where); $i++)
//            {
//                if($i%2 != 0)
//                {
//                    if(is_string($where[$i]))
//                    {
//                        if(($i+1) != null)
//                            $where[$i] = '"'.$where[$i].'" AND ';
//                        else
//                            $where[$i] = '"'.$where[$i].'"';
//                    }
//                }
//            } // End for
//            
//            $where = implode('',$where);

            $update = 'UPDATE '.$tabla.' SET ';
            
            $keys = array_keys($rows);
            
            for($i = 0; $i < count($rows); $i++)
            {
                if(is_string($rows[$keys[$i]]))
                {
                    $update .= $keys[$i].'="'.$rows[$keys[$i]].'"';
                }
                else
                {
                    $update .= $keys[$i].'='.$rows[$keys[$i]];
                }

                // Parse to add commas
                if($i != count($rows)-1)
                {
                    $update .= ',';
                }
            }
            $update .= ' WHERE '.$where;
            
            //echo "<p>$update</p>";
            
            $query = mysqli_query($this->link,$update);
            if($query)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    } // End update()
    
    public function call($procedimiento, $rows = NULL){
        
        $call = "CALL $procedimiento ";
        
        if($rows != NULL){
            $call.="($rows)";
        }        
        
        $query = mysqli_query($this->link, $call);
        
        echo $call;
        
        if($query)
        {
            return true;
        } else {
            return false;
        }
        
    } // End call()

    public function desconectar()
    {
        
        if($this->con)
        {
            if(@mysqli_close())
            {
                $this->con = false; 
                return true; 
            }
            else
            {
                return false; 
            }
        }
    } // End desconectar()
    
    public function cleanResult(){
        $this->result = '';
    }
    
} // End Conexion()

?>
