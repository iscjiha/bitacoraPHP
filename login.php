<?php

    session_start();
    
    require_once 'lib/functions.php';
    spl_autoload_register('mi_autocargador');
    
    $res = "Ingrese sus datos"; 
    
    if(isset($_POST['txtUsuario']) && isset($_POST['txtContrasena'])){
        
        $usu  = ( isset($_POST['txtUsuario']) ) ? $_POST['txtUsuario'] : '';
        $pwd = ( isset($_POST['txtContrasena']) ) ? $_POST['txtContrasena'] : '';
    
        $obUsu = new usuario();
        
        $obUsu->usuario = stripslashes($usu);
        $obUsu->contrasena = stripslashes($pwd);
                
        $res = $obUsu->checarDatos();
        
    } // End si usuario y password
    
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="icon" href="img/favicon.ico" type="image/x-icon" />
        
        <title>Bit&aacute;cora</title>
        <link rel="stylesheet" href="css/estilo_login.css" />
    </head>
    <body>
        
        <center>
            <div id="tituloSistema">
                Control de Bit&aacute;cora
            </div>
            <form action='login.php' method='post'>                
                <div>
                <table cellpadding='2' cellspacing='1' class="tablaLogin">
                    <thead>                        
                        <tr>
                            <th>
                                <div id="alerta_login">
                                    <?php 
                                    
                                    if(isset($_GET['msj'])){
                                        $msg = $_GET['msj'];
                                        echo ($msg == 1) ? "No ha iniciado sesion" : "";
                                    }else{
                                        echo (isset($res)) ? $res:'';
                                    }
                                    
                                    ?>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td style="height: 5px"></td></tr>
                        <tr>                                    
                            <td>
                                <input type='text' name='txtUsuario' maxlength='15' value="<?php echo (isset($us)) ? $us : ''; ?>" placeholder="Usuario" autofocus required/>
                            </td>
                        </tr>
                        <tr>                                    
                            <td>
                                <input type='password' name='txtContrasena' maxlength='15' placeholder="Contrase&ntilde;a" required />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <br>
                                <input type='submit' value='Ingresar'/><br />
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </form>
        </center>
    </body>
</html>
