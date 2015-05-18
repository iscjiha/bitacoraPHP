<?php

class ticket {
   
    public $campos;
    
    public function __construct() {
        $this->campos = array(
            'id_actividad'=>'',
            'id_usuario_creo'=>$_SESSION['idUsuario'],
            'leido_usuario'=>date('Y-m-d H:i:s'),
            'respuesta'=>'',
            'fecha_respuesta'=>date('Y-m-d H:i:s'),
            'estatus'=>'E'
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
    
    public function contarPendientes()
    {
        $db = new DB();        
        $query = "SELECT sp_contar_tickets_pendientes({$_SESSION['idUsuario']})";
        
        $num_tickets = $db->single($query);
        
        return $num_tickets;
        
    } // End contarPendientes()

    public function getById()
    {
        
        $db = new DB();
        
        $AND = ($_SESSION['esAdmin'] <> 1) ? "AND idUsu = {$_SESSION['idUsuario']}" : "";
        
        $query = "SELECT * FROM v_datos_ticket WHERE idAct = $this->id_actividad $AND";
        
        $res = $db->row($query);        
        
        return ($res) ? $res : FALSE;
        
    } // End getTicketById()    
    
    public function getRespuesta(){
        
        $db = new DB();
        
        $query = "SELECT * FROM v_respuestas_tickets WHERE idTicket = $this->id_actividad";
        
        $res = $db->row($query);
        
        return ($res) ? $res : FALSE;
        
    } // End getRespuesta()

    public function insertar(){
        
        $db = new DB();     
        $query = "CALL sp_insertar_ticket($this->id_actividad, $this->id_usuario_creo)";
                              
        return ($db->query($query)) ? TRUE : FALSE;
    
    } // End insertar()
    
    public function listado($dias,$idUsuarioMostrar)
    {
       
        $db = new DB();
        
        if($_SESSION['esAdmin'] == 1){ // Administrador
            $condUsu = ($idUsuarioMostrar > 1) ? " = $idUsuarioMostrar" : " <> 1";
            $and = "AND idUsu $condUsu";
        }else{                                              // Empleado
            $and = "AND idUsu = {$_SESSION['idUsuario']}";
        }
        
        $query = "SELECT * FROM v_listado_tickets 
                            WHERE DATE_SUB(CURDATE(),INTERVAL $dias DAY) <= fecha  $and
                        ORDER BY fecha DESC";

        $res = $db->query($query);
        
        if($res)
            return $res;
        else
            return FALSE;
        
    } // End listado()
    
    public function marcarLeido()
    {
        
        $db = new DB();
        //$query = "UPDATE tickets SET leido_usuario = '$this->leido_usuario', estatus = 'L' WHERE id_actividad = $this->id_actividad";
        $query = "CALL sp_marcar_ticket_leido($this->id_actividad)";        
        return ($db->query($query)) ? TRUE : FALSE;
        
    } // End marcarLeido()
    
    public function modificarFecha()
    {
        
        $con = new conexion();
        $con->conectar();
        
        $res = $con->update("instrucciones", 
                array(
                    'fecha'=>$this->fecha
                ), 
                "id={$this->id}");
                
        if($res){
            return TRUE;
        }else{
            return FALSE;
        }
        
        $con->desconectar();
        
    } // End modificarFecha()
    
    public function responder($fechaAtencion)
    {
        
        $db = new DB();        
        $query = "CALL sp_responder_ticket($this->id_actividad, '$this->respuesta', '$fechaAtencion')";  
        return ($db->query($query)) ? TRUE : FALSE;
        
    } // End responder()
            
    function __destruct() {
        //echo "Instruccion destruido";
    }
    
} // End class

?>
