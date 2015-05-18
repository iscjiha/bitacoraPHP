<?php

function mi_autocargador($clase)
{
    require_once "clases/$clase.class.php";
} // End mi_autocargador()

function comboUsuarios($usuarios)
{
    echo "<select name='cboUsuarios' id='cboUsuarios'>";
        echo "<option value=''>Todos los empleados..</option>";
        foreach ($usuarios as $usuario) {
            echo "<option value='{$usuario['idUsu']}'>". utf8_encode($usuario['nomUsu']) ." (".utf8_encode($usuario['Cat']).")</option>";
        }
    echo "</select>";    
} // End comboUsuarios()

function theChecada($ch_salida)
{
    
    ?>
    <div id="dialogoChecado">
        <div id="cajaPreguntaChecado">
            
            <span style="font-size: 22px; font-weight: bold; color: #4eba55">Buen d&iacute;a <?php echo $_SESSION['nombreUsuario'] ?></span>
            
            <div style=" margin-top: 15px">
                <form name="frmInsertarChecada" id="frmInsertarChecada" action="do.php" method="POST">
                    
                    <input type="hidden" name="accion" value="ingresar_checadas">
                    <input type="hidden" name="txtIdChecadaAnterior" value="<?php echo $ch_salida['idCh']?>">
                    
                    <span style="font-size: 14px; font-weight: bold;">¿A que hora checho <font color='red'>HOY</font>?</span>                    
                    <input type="time" id="txtHoraHoy" name="txtHoraHoy" class="inputHora" value="08:30" autofocus required>
                    <br><br>
                    <?php
                    if($ch_salida){
                        ?>
                        <span style="font-size: 14px; font-weight: bold;">¿A que hora checho <font color='red'>SALIDA</font> el d&iacute;a <font color='green'><?php echo $ch_salida['fecha'] ?></font>?</span>                    
                        <input type="time" id="txtHoraAnterior" name="txtHoraAnterior" class="inputHora" value="16:00" required>
                    
                        <br><br>
                        <?php
                    }
                    ?>
                    
                    <input type="submit" id="btnGuardarChecadas" name="btnGuardarChecadas" class="btnVerde" value="Guardar">
                    
                    <div id="msgInforma"></div>
                    
                </form>                
            </div>
            
        </div>
    </div>
    <?php
} // End theChecada()

function listadoActividades($actividades)
{
    if(empty($actividades))
        echo "<p style='font-style:italic; padding:15px'>No se encontraron actividades..</p>";
    else
        foreach ($actividades as $actividad) {

            $img_pen_env = ($actividad['idEstAct'] < 3 ) ? 
                    "<span class='actividadPendiente'>Pendiente</span>" : 
                    "<img src='img/confirm_double.png' title='Enviado'>";
            
            $img_es_ticket = ($actividad['esTicket'] == 1) ? "<img src='img/icon_t.png' style='margin-right:3px' title='Ticket'>" :"";

            echo "<div class='elemListado' id='{$actividad['idAct']}' fecha='{$actividad['fecAct']}'>";
                echo "<span class='fechaElemLista'>{$actividad['fecAct']}</span>";
                echo "<span class='iconListado'>$img_pen_env</span>";
                echo "<span class='iconListado'>$img_es_ticket</span>";
                echo "<p>{$actividad['descAct']}</p>";
            echo "</div>";
            
        }
} // End listadoActividades()

function listadoChecadas($checadas)
{
    
    if(empty($checadas))
        echo "<p style='font-style:italic; padding:15px'>No se encontraron chedas..</p>";
    else

    ?>
    <table id="tablaChecadas">
        <thead>
            <tr>
                <?php if($_SESSION['esAdmin'] == 1) echo "<th>Usuario</th>" ?>
                <th>Fecha</th>
                <th>Entrada</th>
                <th>Salida</th>
            </tr>
        </thead>
        <tbody>
            <?php
            
            foreach ($checadas as $checada) {
                
                $btn_checar_salida = (!$checada['horSal']) ?
                        ($_SESSION['esAdmin'] == 1) ? 
                           "<span style='color:#F78181'>No registrada</span>" : 
                            "<span id='cajaIngresarSalidaTabla_{$checada['idCh']}'><input type='button' class='btnIngresarChecada' idChecada='{$checada['idCh']}' value='Registrar Salida'></span>" :
                        $checada['horSal'];
                        
                echo "<tr>";
                    if($_SESSION['esAdmin'] == 1)
                        echo "<td>{$checada['Usu']}</td>";
                        echo "<td>{$checada['fec']}</td>";
                        echo "<td>{$checada['horEn']}</td>";
                        echo "<td>$btn_checar_salida</td>";
                echo "</tr>";
            }
            
            ?>
        </tbody>
    </table>

    <?php
  
} // End listadoChecadas()

function listadoTickets($tickets)
{
    //var_dump($tickets);
    if(empty($tickets))
        echo "<p style='font-style:italic; padding:15px'>No se encontraron tickets pendientes..</p>";
    else{
        foreach ($tickets as $ticket) {
            echo "<div class='elemListado' id='{$ticket['idAct']}' leida='{$ticket['ticketLeido']}'>";
                echo "<span class='fechaElemLista'>{$ticket['fecAct']}</span>";
                echo ($ticket['ticketLeido'] == 1) ? "" : "<img id='iconInfTicket{$ticket['idAct']}' class='iconInforma' src='img/unread.png' title='Sin leer'>";
                echo "<p>{$ticket['descAct']}</p>";
            echo "</div>";
        }
    }
} // End listadoTickets()

function noramalizar_fecha($date)
{
    if(!empty($date)){
        $var = explode('/',str_replace('-','/',$date));
        return "$var[2]-$var[1]-$var[0]";
    }   
} // End noramalizar_fecha()

//function theActividad($acc = 'agregar_actividad', $fecha = "", $desc = "", $est = 0, $idAct = "")
function theActividad($actividad = "")
{   
    
    ?>

    <form id="frmActividad" name="frmActividad" method="post" action="do.php">
        
        <input type="hidden" name="accion" id="accion" value="<?php echo ($actividad) ? "editar_actividad" : "agregar_actividad" ?>">        
        
        <b>Fecha de Actividad:</b>
        
        <?php
        if($actividad){
            
            //var_dump($actividad);
            
            echo "<input type='hidden' name='txtID' value='{$actividad['idAct']}'>";
            
            $est = $actividad['idStAct'];            
            
            $btnEdFec = "";
            
//            if($_SESSION['esAdmin'] == 1) {
//                $btnEdFec = "<img src='img/edit_icon.png' id='btnEditarFecha' class='icon xs' title='Modificar Fecha' idAct='{$actividad['idAct']}'>";
//            }else if($est == 1 OR $est == 2){
//                $btnEdFec = "<img src='img/edit_icon.png' id='btnEditarFecha' class='icon xs' title='Modificar Fecha' idAct='{$actividad['idAct']}'>";
//            }
            
            echo "<label id='lblFechaActiVista'>{$actividad['fecAct']}</label> $btnEdFec";
            
            $atributo = ($est > 2) ? 'disabled' : '';
            
            echo "<input type='date' name='txtFechaActividadEdicion' id='txtFechaActividadEdicion' value='".noramalizar_fecha($actividad['fecAct'])."' style=' display: none' >";
            echo "<img src='img/confirm_icon.png' id='btnConfirmarEdicionFecha' class='icon xs' title='Confirmar edici&oacute;n' style='display: none'>";
            echo "<img src='img/cancel_icon.png' id='btnCancelarEdicionFecha' class='icon xs' title='Cancelar edici&oacute;n' style='display: none'>";
            
            echo ($actividad['esTicket'] == 1) ?
                "<span id='btnVerRespuesta' idTicket={$actividad['idAct']} class='icon' style='float: right'><img src='img/response.png' title='Ver Respuesta' width='18'></span>" : "";
                
            if($_SESSION['esAdmin'] == 1){
                echo "<span style='float:right; margin-right:5px; color:green'><font color='gray'>Por: </font>{$actividad['Empleado']}</span>";
            }
            
            echo "<div id='cajaRespuesta'></div>";
                    
        }else{
            echo "<input type='datetime-local' name='txtFecha' id='txtFecha' value='".  date('Y-m-d') . "T" . date('H:i') . ":00" . "' required>";
            //1996-12-19T16:39:57
            $atributo = 'autofocus';
            $est = 0;
        }
            
        ?> 
        
        <div id='msjFrm'></div>
        
        <textarea name="txtDescripcion" id="txtDescripcion" required style="<?php echo ($est > 2) ? "height:94%" : "height:90%" ?>" placeholder="Ingrese aqu&iacute; los detalles de su actividad, solo se permite una actividad por d&iacute;a..." <?php echo $atributo ?> ><?php echo ($actividad) ? $actividad['descAct'] : "" ?></textarea>
        
        <?php  
        if($est == 0){
            ?><input type="submit" id="btnGuardar" style="float: right" class="btnVerde" value="Guardar Actividad"><?php
        }
        if($est == 1 OR $est == 2){
            if($_SESSION['esAdmin'] <> 1){
                ?>
                <input type="button" id="btnEnviarJefe" style=" float: right" class="btnNaranja" value="Enviar a Jefe" idAct="<?php echo $actividad['idAct'] ?>">
                <input type="submit" id="btnEditarActividad" style=" float: left" class="btnAzul" value="Editar Actividad">
                <?php
            }
        }
        ?>
                
    </form>
    
    <?php
    
} // End theActividad()

function theHeader()
{
?>
    <div id="header">
        <div id="tituloSistema">Control de Bit&aacute;cora</div>
        
        <?php 
        
        if(isset($_SESSION['logueado']) && $_SESSION['logueado']){
            
            echo "<ul id='menuPrincipal'>";
                echo ($_SESSION['esAdmin'] == 1) ? 
                    "<li class='btnMenuPpal' title='Nuevo Ticket'>Nuevo Ticket</li>" : 
                    "<li class='btnMenuPpal' title='Agregar actividad'>Agregar Actividad</li>";
                echo "<a href='logout.php' title='Cerrar sesi&oacute;n'><li class='btnMenuPpal'>Cerrar sesi&oacute;n</li></a>";
            echo "</ul>";
            
        } // End si esta logueado
        
        ?>
        <div id="nombreUsuario">
            <?php echo utf8_encode($_SESSION['nombreUsuario']) . " <font color='gray' size='1'>(". utf8_encode($_SESSION['categoria']).")</font>" ?>
        </div>
    </div>
<?php

} // End theHeader()

function theTicket($ticket = "")
{
    //var_dump($ticket);
    ?>    
    <form id="frmActividad" name="frmActividad" method="post" action="do.php">
        
        <input type="hidden" name="accion" id="accion" value="<?php echo ($ticket) ? "editar_ticket" : "agregar_ticket" ?>">
        
        <div style="background: #848484; padding: 2px; color: white;  height: <?php echo ($_SESSION['esAdmin']) ? 'auto' : '20px' ?>">
            <?php 

            if($ticket){
                if ($_SESSION['esAdmin'] <> 1){
                    echo (!$ticket['respAct']) ? "<span id='btnResponderTicket'>Responder</span>" : "";
                }
                ?>
                
                <input type="hidden" name="txtID" id="txtID" value="<?php echo $ticket['idAct'] ?>" >
                <label style="float: right">Fecha de Env&iacute;o: <?php echo "<label style='color:#E3F6CE; font-weight:bold'>{$ticket['fecEnvio']}</label>" ?></label>
                <?php            
                if($_SESSION['esAdmin'] == 1){
                    echo "Empleado: <span style='color:#E3F6CE; font-weight:bold'>{$ticket['nomEmp']}</span>";
                    echo "<div>Enviado por: <span style='color:#E3F6CE; font-weight:bold'>{$ticket['nomEmisor']}</span></div>";
                }            
            }else{
                $obUsu = new usuario();
                comboUsuarios($obUsu->getNombresUsuarios());
            }

            ?>
        </div>
        <div id="cajaRespuestaTicket" style=" display: none">
            Fecha de Atenci&oacute;n: <input type="datetime-local" id="txtFechaAtencion" value="<?php echo date('Y-m-d').'T'.date('H:i').':00' ?>" >            
            <textarea id="txtRespuestaTicket" placeholder="Ingrese la resupuesta al ticket.."></textarea>
            <input type="button" id="btnEnviarRespuestaTicket" value="Enviar" class="btnVerde" style=" vertical-align: top; float: right">            
        </div>
        <div id='msjFrm'></div>
        
        <textarea name="txtDescripcion" id="txtDescripcion" required style="<?php echo ($ticket) ? "height:94%" : "height:89%" ?>" placeholder="Ingrese aqu&iacute; los detalles del ticket..." <?php echo ($ticket) ? "disabled" : "autofocus"; ?>><?php echo ($ticket) ? $ticket['descAct'] : "" ?></textarea>
        
        <?php
        if(!$ticket){
            ?><input type="submit" id="btnGuardar" style="float: right" class="btnVerde" value="Enviar Ticket"><?php
        }
        ?>
            
    </form>    
    <?php
} // End theTicket()

function theRespuesta($respuesta = ""){
    echo "Fecha de Respuesta: {$respuesta['fec']} <br>";
    echo $respuesta['resp'];
} // End theRespuesta()

function theSider()
{
    
    ?>

    <div class="sidebar">
        
        <input type="hidden" id="opcBtnPpalSidebar" value="<?php echo ($_SESSION['esAdmin'] == 1) ? 'actividades' : 'tickets' ?>">
        
        <label id="btnPpalSidebarActividades" class="btnPpalSidebar <?php echo ($_SESSION['esAdmin'] == 1) ? 'btnPpalSidebarActivo' : '' ?>" title="actividades">
            Actividades
        </label>
        <label id="btnPpalSidebarTickets" class="btnPpalSidebar <?php echo ($_SESSION['esAdmin'] <> 1) ? 'btnPpalSidebarActivo' : '' ?>" title="tickets">
            Tickets
                <?php if($_SESSION['esAdmin'] <> 1){ ?>
                    <span class="contadorTickets" title="Tickets Pendientes"></span>
                    <input type="hidden" id="contadorTickets" value="">
                <?php }?>
        </label>
        <label id="btnPpalSidebarChecadas" class="btnPpalSidebar" title="checadas">Checadas</label>
        <?php
        if($_SESSION['idUsuario'] == 1){
            ?><label id="btnPpalSidebarReportes" class="btnPpalSidebar" title="reportes">Reportes</label><?php
        }
        ?>
        
        <div class="opcListado">
            <select id="cboFiltroDesde" title="Seleccione para filtrar el listado" style="float: right">
                <option value="0">Hoy</option>
                <option value="1">Ayer</option>
                <option value="7" selected>7 d&iacute;as</option>
                <option value="30">30 d&iacute;as</option>
                <option value="90">3 meses</option>
                <option value="365">1 a&nacute;o</option>
                <option value="pers">Rango</option>
            </select>
            <span style="float: right; padding: 1px; font-size: .8em; color: gray">Desde:</span>
        </div>
        
        <div id="cajaBuscarRangoFechas" style=" display: none">
            <input type="date" id="txtFechaDesde" title="Ingrese la fecha de inicio">
            <input type="date" id="txtFechaHasta" placeholder="Ingrese la fecha de fin">
            <input type="button" id="btnBuscarRangoFechas" value="Buscar">
        </div>
        <br><br>
        <div class="listadoElementos">Seleccione una opción</div>
        
        <?php
            if($_SESSION['esAdmin'] == 1){
                ?>
                <div id="footSider">
                    Informaci&oacute;n de:
                    <?php
                        $obUsu = new usuario();
                        comboUsuarios($obUsu->getNombresUsuarios());
                    ?>
                </div>
                 <?php
            }
        ?>
        
    </div> <!-- End sidebar -->
        
    <?php
    
} // End theSider()

?>