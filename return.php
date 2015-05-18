<?php

session_start();

header('Content-Type: text/html; charset=ISO-8859-1');

require_once 'lib/functions.php';
spl_autoload_register('mi_autocargador');

//comprobar q estamos logueados
if($_SESSION['logueado']){
    
    $accion = $_POST['accion'];

    if($accion){
        
        switch ($accion) {
            
            case 'contar_tickets':
                
                $obTicket = new ticket();
                echo $obTicket->contarPendientes();
                
                break;
            
            case "listado_actividades":
                
                $numDias = (isset($_POST['numDias'])) ? $_POST['numDias'] : 7;
                $idUsuarioMostrar = (isset($_POST['idUsu'])) ? $_POST['idUsu'] : "";
                
                $obAct = new actividad();
                $act = $obAct->listado($numDias,$idUsuarioMostrar);
                
                listadoActividades($act);
                
                break;
                
            case 'listado_checadas':
                
                $numDias = (isset($_POST['numDias'])) ? $_POST['numDias'] : 7;
                //$idUsuarioMostrar = ($_SESSION['esAdmin'] <> 1) ? $_SESSION['idUsuario'] : $_POST['idUsu'];
                $idUsuarioMostrar = (isset($_POST['idUsu'])) ? $_POST['idUsu'] : "";
                
                $obChec = new checada();
                $checadas = $obChec->listado($numDias,$idUsuarioMostrar);
                
                listadoChecadas($checadas);
                
                break;
                
            case "listado_tickets":
                
                $numDias = (isset($_POST['numDias'])) ? $_POST['numDias'] : 7;
                $idUsuarioMostrar = (isset($_POST['idUsu'])) ? $_POST['idUsu'] : "";
                
                $obTicket = new ticket();
                
                listadoTickets($obTicket->listado($numDias, $idUsuarioMostrar));

                break;
            
            case "mostrar_actividad":
                
                $_SESSION["ultima_actividad"] = date('Y-m-d H:i:s');
                
                if(!isset($_POST['idElemento'])){
                    theActividad();
                }else{
                    $obAct = new actividad();
                    $obAct->id = (isset($_POST['idElemento'])) ? $_POST['idElemento'] : '';
               
                    $datos_actividad = $obAct->getById();
                    
                    theActividad($datos_actividad);                   
                }
                
                break;
            
            case 'mostrar_respuesta':
                
                $obTicket = new ticket();
                $obTicket->id_actividad = $_POST['idTicket'];
                
                $res = $obTicket->getRespuesta();
                
                echo ($res) ? theRespuesta($res) : "";
                
                break;
                
            case 'mostrar_ticket':
                
                if (!isset($_POST['idElemento'])){
                    theTicket ();
                } else {
                    $obTicket = new ticket();
                    $obTicket->id_actividad = $_POST['idElemento'];
                    $ticket = $obTicket->getById();
                    
                    if($_SESSION['esAdmin'] <> 1){
                        if($ticket['Leido'] == 0)                        
                        $obTicket->marcarLeido();
                    }
                    
                    theTicket($ticket);
                }
                break;
                
            case 'revisar_sesion':
                
                $fecha_actual = date('Y-m-d H:i:s');
                
                $tiempo_transcurrido = (strtotime($fecha_actual)-strtotime($_SESSION["ultima_actividad"])); 
                
                if($tiempo_transcurrido >= 1200) //1200 milisegundos = 1200/60 = 20 Minutos...
                    echo "salir";
                
                break;
                
            default:
                break;
                
        } // End switch($accion)

    } // End if accion
        
} else { //fin de si estamos logeados
    header("Location: index.php"); 
}

?>
