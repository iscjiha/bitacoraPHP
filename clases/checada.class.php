<?php

class checada {
    
    public $campos;
    public $numChecadas;

    public function __construct() {        
        $this->campos = array(            
            'id'=>'null',
            'fecha'=>date('Y-m-d'),
            'hora_entrada'=>'',
            'hora_salida'=>'',
            'id_usuario'=>$_SESSION['idUsuario'],
            'capturada'=>date('Y-m-d H:i:s')
            );
    }
    
    //para obtener los valores de los campos
    public function __get($campos){
        return $this->campos[$campos];
    } // End __get()
    
    //para establecer los valores de los campos
    public function __set($campo, $valor){
        if(array_key_exists($campo, $this->campos)){
            $this->campos[$campo]= $valor;
        }
    } // End __set()
    
    public function ingresarChecadaAnterior()
    {
        
        $con = new conexion();
        $con->conectar();
        
        $db = new DB();
        $query = "UPDATE checadas SET hora_salida = '$this->hora_salida', capturada = '$this->capturada' WHERE id = $this->id ";        
        
        $res = $db->query($query);
        
        return ($res) ? TRUE : FALSE;
        
    } // End ingresarChecadasHoy()
    
    public function ingresarChecadaHoy()
    {
        
        $db = new DB();
        $query = "INSERT INTO checadas(fecha, hora_entrada, id_usuario, capturada)
                        VALUES ('$this->fecha', '$this->hora_entrada', $this->id_usuario, '$this->capturada')";
        
        return ($db->query($query)) ? TRUE : FALSE;
        
    } // End ingresarChecadasHoy()

    public function verificarChecadaAnterior()
    {
        
        $db = new DB();
        $sql = "SELECT
                        id idCh, DATE_FORMAT(ch.fecha,'%d/%m/%Y') fecha
                    FROM checadas ch 
                    WHERE ch.fecha < '{$this->fecha}' AND ch.hora_salida IS NULL AND ch.id_usuario = {$this->id_usuario}";                    
        
        $res = $db->row($sql);
        
        return ($res) ? $res : FALSE;
        
    } // End verificarChecadaAnterior()
    
    public function verificarChecadaHoy()
    {
        
        $db = new DB();
        
        $query = "SELECT count(ch.id) checoHoy FROM checadas ch WHERE ch.fecha = '{$this->fecha}' AND ch.id_usuario = {$this->id_usuario}";
        $res = $db->single($query);        
        
        return ($res);
        
    } // End verificarChecadaHoy()
    
    public function listado($dias,$idUsuarioMostrar)
    {
        $db = new DB();
        
        if($_SESSION['esAdmin'] == 1){ // Administrador            
            $condUsu = ($idUsuarioMostrar > 1) ? " = $idUsuarioMostrar" : " NOT IN(1)";
            $and = "ch.id_usuario $condUsu";
        }else{                                              // Empleado
            $and = "ch.id_usuario = $this->id_usuario";
        }
        
        if($_SESSION['esAdmin'])
            $query = "SELECT
                                ch.id idCh, DATE_FORMAT(ch.fecha,'%d/%m/%Y') fec, DATE_FORMAT(ch.hora_entrada,'%h:%i %p') horEn,
                                if(ch.hora_salida IS NULL,'',DATE_FORMAT(ch.hora_salida,'%h:%i %p')) horSal, u.nombre Usu
                            FROM checadas ch
                                INNER JOIN usuarios u
                                ON u.id = ch.id_usuario
                            WHERE $and AND DATE_SUB(CURDATE(),INTERVAL $dias DAY) <= ch.fecha
                            ORDER BY ch.fecha DESC";
        else
            $query = "SELECT
                                ch.id idCh, DATE_FORMAT(ch.fecha,'%d/%m/%Y') fec, DATE_FORMAT(ch.hora_entrada,'%h:%i %p') horEn,
                                IF(ch.hora_salida IS NULL,'',DATE_FORMAT(ch.hora_salida,'%h:%i %p')) horSal
                            FROM checadas ch
                            WHERE $and AND DATE_SUB(CURDATE(),INTERVAL $dias DAY) <= ch.fecha
                            ORDER BY ch.fecha DESC";       
                
        $res = $db->query($query);
        
        return ($res) ? $res : FALSE;
        
    } // End listado()
            
    function __destruct() {
        //echo "checada destruido";
    }
    
} // End class

?>
