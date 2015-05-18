<!DOCTYPE>
<html lang="es">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="author" content="PGJEZ-ISRAEL">

        <link rel="icon" href="img/favicon.ico" type="image/x-icon" />
        
        <title>
            <?php
                if (!empty($GLOBALS['TEMPLATE']['title']))
                {
                    echo $GLOBALS['TEMPLATE']['title'];
                }
            ?>
        </title>
        
        <!-- CSS -->
        <link rel="stylesheet" href="css/estilos.css">

        <!-- JavaScript -->
        <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="js/functions.js"></script>
        
    </head>
    <body>
        <div>
            <?php echo theHeader() ?>
            <div id="contenido">
              <?php
              
                    if (!empty($GLOBALS['TEMPLATE']['content']))
                        echo $GLOBALS['TEMPLATE']['content'];
                    
                    echo theSider();
                    
                    echo "<div class='cajaElemento'>";
                        echo ($_SESSION['esAdmin'] <> 1) ? theActividad() : theTicket();
                    echo "</div>";
                    
              ?>
            </div>
        </div>
    </body>
</html>