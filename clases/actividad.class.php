<?php

class actividad {
   
    public $campos;
    
    public function __construct() {        
        $this->campos = array(            
            'id'=>'null',
            'fecha'=>date('Y-m-d H:i:s'),
            'descripcion'=>'',
            'id_usuario'=>$_SESSION['idUsuario'],
            'id_estatus'=>1,
            'creada'=>date('Y-m-d H:i:s'),
            'editada'=>date('Y-m-d H:i:s'),
            'enviada'=>date('Y-m-d H:i:s'),
            'editada_admin'=>'',
            'es_ticket'=>0
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
    
    public function enviarAJefe(){
        
        $db = new DB();        
        $query = "CALL sp_enviar_actividad_jefe($this->id,'$this->descripcion')";
        
        echo ($db->query($query)) ? 1 : 0;
        
    } // End enviarAJefe()

    public function getById()
    {
        
        $db = new DB();
        
        $AND = ($_SESSION['esAdmin'] <> 1) ? "AND idUsu = {$_SESSION['idUsuario']}" : "";
        
        $query = "SELECT * FROM v_datos_actividad WHERE idAct = {$this->id} $AND";
                        
        $res = $db->row($query);
        
        if($res){
            
            if($res['idStAct'] < 4){
                if($_SESSION['esAdmin'] == 1){
                    $query_upd = "CALL sp_marcar_actividad_revisada($this->id)";
                    $db->query($query_upd);
                }
            }
            
            return $res;
            
        }else{
            return FALSE;
        }
        
    } // End getById()

    public function editarActividad()
    {
        
        $db = new DB();        
        $query = "CALL sp_editar_actividad($this->id,'$this->descripcion')";
        
        return ($db->query($query)) ? TRUE : FALSE;
    
    } // End editarActividad()
    
    public function insertar()
    {
        
        $db = new DB();
        
        $query = "CALL sp_insertar_actividad('$this->descripcion',$this->id_usuario,$this->es_ticket,$this->id_estatus)";
        
        return ($db->query($query)) ? TRUE : FALSE;
    
    } // End insertar()
    
    public function listado($dias,$idUsuarioMostrar)
    {
        
        $db = new DB();
        
        if($_SESSION['esAdmin'] == 1){ // Administrador
            $condUsu = ($idUsuarioMostrar > 1) ? " = $idUsuarioMostrar" : " <> 1";
            $and = "a.id_usuario $condUsu AND a.id_estatus NOT IN (1,2,6) AND";
        }else{                                              // Empleado
            $and = "a.id_usuario = $this->id_usuario  AND a.id_estatus NOT IN(6) AND";
        }
        
        $sql = "SELECT
                        a.id idAct, date_format(a.fecha,'%d/%m/%Y %h:%m %p') fecAct, SUBSTRING(a.descripcion,1,100) descAct, a.id_estatus idEstAct,
                        a.es_ticket esTicket
                        FROM actividades a
                    WHERE $and DATE_SUB(CURDATE(),INTERVAL $dias DAY) <= a.fecha
                    ORDER BY a.fecha DESC";
        
        $res = $db->query($sql);
        
        return ($res) ? $res : FALSE;
        
    } // End listado()
    
    public function modificarFecha()
    {
        
        $con = new conexion();
        $con->conectar();
        
        $res = $con->update("actividades", 
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
    
    public function getLastId()
    {
        $db = new DB();        
        return $db->single("SELECT max(id) FROM actividades");
    } // End getLastId()

    public function puedeAgregar()
    {
        
        $con = new conexion();
        $con->conectar();
        
        $res = $con->select("actividades a", 
                "count(a.id) numAct", 
                "a.id_usuario = {$_SESSION['idUsuario']} AND a.fecha = '{$this->fecha}' ");
        
        if($res){
            return $con->getResult();            
        }else{
            return FALSE;
        }
        
        $con->desconectar();
        
    } // End puedeAgregar()
    
    function __destruct() {
        //echo "actividad destruido";
    }
    
} // End class

?>
