<?php

session_start();
header('Cache-control: private');
ob_start();

require_once 'lib/functions.php';
spl_autoload_register('mi_autocargador');

//comprobar q estamos logueados
if($_SESSION['logueado']){
    
    $GLOBALS['TEMPLATE']['title'] = 'Bit&aacute;cora';
    
    if($_SESSION['esAdmin'] <> 1){
    
        $obChec = new checada();
        $checo_hoy = $obChec->verificarChecadaHoy();
        
        if(!$checo_hoy)
        {
            $ch_sal = $obChec->verificarChecadaAnterior();            
            theChecada($ch_sal);
        }
    
    } // End si no es administrador
        
} else { //fin de si estamos logeados
    //si no estamos logeados mandamos este error
    header("Location: login.php");
    exit();
}

$form = ob_get_clean();
$GLOBALS['TEMPLATE']['content'] = $form;

//mandamos la pagina con el template
require_once './templates/template.php';

?>
