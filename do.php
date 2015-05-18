<?php

session_start();

header('Content-Type: text/html; charset=UTF-8');  

require_once 'lib/functions.php';    
spl_autoload_register('mi_autocargador');

//comprobar q estamos logueados
if($_SESSION['logueado']){
    
    $accion = ($_POST['accion']);

    if($accion){
        
        $_SESSION["ultima_actividad"] = date('Y-m-d H:i:s');

        switch ($accion) {

            case "agregar_actividad":
                
                $fechaAct = date('Y-m-d H:i:s', strtotime($_POST['txtFecha'])); // Conversión de fecha
                $descripcion = utf8_decode($_POST['txtDescripcion']);
                
                if($fechaAct <> '' AND $descripcion <> ''){
                    
                    $obAct = new actividad(); // Creamos el objeto
                    
                    $obAct->fecha = $fechaAct;
                    $obAct->descripcion = $descripcion;                    
                    
                    echo ($obAct->insertar()) ? TRUE : FALSE;
                    
                } // Fin validación

                break;
            
            case "agregar_ticket":
                
                $idUsu = $_POST['cboUsuarios'];
                $descripcion = utf8_decode($_POST['txtDescripcion']);
                
                if($idUsu > 0 AND $descripcion <> '')
                {
                    
                    $obAct = new actividad();
                
                    $obAct->id_usuario = $idUsu;
                    $obAct->descripcion = $descripcion;
                    $obAct->es_ticket = 1;
                    $obAct->id_estatus = 6;
                    
                    if($obAct->insertar()){
                        $obTicket = new ticket();
                        $obTicket->id_actividad = $obAct->getLastId();
                        echo ($obTicket->insertar()) ? TRUE : FALSE;
                    }
                    
                }

                break;
            
            case "editar_actividad":
                
                $obAct = new actividad();
                
                $obAct->id = $_POST['txtID'];
                $obAct->descripcion = utf8_decode($_POST['txtDescripcion']);                
                
                echo ($obAct->editarActividad()) ? TRUE : FALSE;
                
                break;
            
            case "enviar_actividad_jefe":
                
                $act = $_POST['actividad'];
                
                $obAct = new actividad();
                
                $obAct->id = $act['id'];
                $obAct->descripcion = utf8_decode($act['descripcion']);
                $obAct->id_estatus = 3;
                $obAct->editada_admin = ($_SESSION == 1) ? 1 : 0;
                
                echo ($obAct->enviarAJefe()) ? TRUE : FALSE;
                
                break;     
            
            case 'ingresar_checada_anterior':                
                
                $idCh = $_POST['txtID'];
                $horaSal = $_POST['txtHoraChecada'];
                
                if($idCh > 0 AND $horaSal <> ''){
                    $obChec = new checada();
                    
                    $obChec->hora_salida = $horaSal;
                    $obChec->id = $idCh;
                    
                    $res = $obChec->ingresarChecadaAnterior();
                    echo $res;
                }
                
                break;
            
            case 'ingresar_checadas':                
                
                $horaHoy = date('H:i:s', strtotime($_POST['txtHoraHoy'])); // Conversión de fecha                
                
                if($horaHoy <> ''){
                    
                    $obChec = new checada();
                    
                    // Registro de entrada
                    $obChec->hora_entrada = $horaHoy;
                    $res_hoy = ($obChec->ingresarChecadaHoy()) ? TRUE : FALSE;
                    
                    // Registro de salida                    
                    if($res_hoy){
                        if(isset($_POST['txtHoraAnterior'])){
                            
                            $idChAnt = $_POST['txtIdChecadaAnterior'];
                            $horaAnterior = date('H:i:s', strtotime($_POST['txtHoraAnterior'])); // Conversión de fecha
                        
                            if($idChAnt > 0 AND $horaAnterior <> ''){
                                $obChec->id = $idChAnt;
                                $obChec->hora_salida = $horaAnterior;
                                echo ($obChec->ingresarChecadaAnterior()) ? TRUE : FALSE;
                            } else {
                                echo FALSE;
                            }
                        }else{
                            echo TRUE;
                        }                        
                    }
                    
                } // Fin validacion
                
                break;
                
            case 'modificar_fecha_actividad':
                
                $obAct = new actividad();
                
                $obAct->id = $_POST['actividad'];
                $obAct->fecha = $_POST['nuevaFecha'];
                
                $puedeAgregar = $obAct->puedeAgregar();
        
                if($puedeAgregar['numAct'] > 0){
                    echo 1;
                }else{                
                    $res = $obAct->modificarFecha();
                    if($res){
                        echo 2;
                    }else{
                        echo 3;
                    }
                }
                
                break;
            
            case 'responder_ticket':
                
                $obTicket = new ticket();
                $obTicket->id_actividad = $_POST['idTicket'];
                $obTicket->respuesta = utf8_decode($_POST['respuesta']);
                
                $fechaAt = date('Y-m-d H:i:s', strtotime($_POST['fechaAtencion'])); // Conversión de fecha
                
                echo ($obTicket->responder($fechaAt)) ? 1 : 0;
                
                break;

            default:
                break;
        }

    } // End if accion
        
} else { //fin de si estamos logeados
    //si no estamos logeados mandamos este error
    header("Location: index.php"); 
}

?>
