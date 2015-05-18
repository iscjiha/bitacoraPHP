<?php

class usuario{
    
    public $campos;
    
    public function __construct() {        
        $this->campos = array(            
            'id'=>'null',
            'apellido_paterno'=>'',
            'apellido_materno'=>'',
            'nombre'=>'',
            'correo'=>'',
            'id_categoria'=>'',
            'creado'=>'',
            'usuario'=>'',
            'contrasena'=>'',
            'ultimo_ingreso'=>date("Y-m-d H:i:s"),
            'activo'=>'',
            'es_administrador'=>''            
            );
    }
    
    private $categoria;

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

    /**
     * Revisamos los datos ingresados en formulario de logueo
     * @param string $usu usuario ingresado
     * @param string $pass contraseña ingresada
     * @return string Resultado
     */
    public function checarDatos()
    {
       
        if(!($this->usuario)){
            $respuesta = "Ingrese el usuario";
        }else if(!($this->contrasena)){
            $respuesta = "Ingrese la contraseña";     
        }else{
            
            // Verificamos que coincidan con datos de la base de datos
            $respuesta = $this->login();
            
            if($respuesta <> "Datos correctos"){                
                $_SESSION['logueado'] = FALSE;                
            }else{
                
                $_SESSION['idUsuario'] = $this->id;
                $_SESSION['nombreUsuario'] = $this->nombre;
                $_SESSION['categoria'] = $this->categoria;
                $_SESSION['esAdmin'] = $this->es_administrador;
                $_SESSION['logueado'] = TRUE;
                $_SESSION['ultima_actividad'] = date('Y-m-d H:i:s');                
                
                header('location: ./');
            }
            
        }
        
        return $respuesta;
        
    } // End checarDatos()

     public static function editarPasswordUsuarioById($idU,$pass)
     {
         
         $con = new Conexion();
         $link = $con->coneccion();
         
         $sql = "UPDATE usuario SET contrasena = '$pass' WHERE id = $idU ";
         
         $result = $con->consulta($sql, $link);
         
         if($result){
             echo "Se actualizo correctamente la contraseña";
         }else{
             echo "No se pudo actualizar";
         }
         
         $con->desconexion($link);
         
     } // End editarPasswordUsuarioById()
    
     public function getNombresUsuarios(){
         
         $db = new DB();
         
         $query = "SELECT
                            u.id idUsu, concat(u.nombre,' ',u.apellido_paterno,' ',u.apellido_materno) nomUsu, c.nombre Cat
                            FROM usuarios u
                            INNER JOIN categorias c
                            ON c.id = u.id_categoria
                        WHERE u.activo = 1 AND u.id <> 1
                        ORDER BY u.nombre";
         
         $res = $db->query($query);
         
         return ($res) ? $res : FALSE;
     
     } // End getAllUsuarios
     
     public function getNombresUsuarioById()
     {
         $con = new Conexion();
         $con->conectar();
         
         $con->select("usuarios u", 
                 "concat(u.nombre,' ',u.apellido_paterno,' ',u.apellido_materno) nomUsu",
                 "u.id = $this->id");
         
         $nombre_usuario = $con->getResult();
         
         return $nombre_usuario['nomUsu'];
         
         $con->desconectar();         
     }

     public static function getTablaUsuarios()
     {
         
         $con = new Conexion();
         $link = $con->coneccion();
         
         $sql = "SELECT id, usuario, nombre FROM usuario";
         
         $result = $con->consulta($sql, $link);
         
         ?>
         
        <center>
            
            <div id="dialogo_cambio_contrasena" title="Cambio de contraseña">
                <p class="validateTips"></p>
                <br>
                <form>
                    <label>Nueva contraseña</label>
                    <input type="hidden" id="txtIdUsuario" value="">
                    <input type="password" name="contrasena" id="contrasena" value="" class="text ui-widget-content ui-corner-all" />
                </form>
            </div>
            
            <table class="tablaNice">
                <thead>
                    <tr>
                        <th colspan="3">Listado de Usuarios</th>
                    </tr>
                    <tr>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    
                        while ($row = mysqli_fetch_object($result)) {
                                 echo "<tr>";
                                    echo "<td>$row->nombre</td>";
                                    echo "<td>$row->usuario</td>";
                                    echo "<td><img idUsuario='$row->id' src='img/PasswordResetIcon.png' title='Cambiar contraseña' class='icon btnCambiarPassword'></td>";
                                 echo "</tr>";
                        }
                        
                    ?>
                </tbody>
            </table>
        </center>

         <?php
         
         $con->desconexion($link);
         
     } // End getTablaUsuarios()

     /**
      * Verificamos que los datos sean correctos en base de datos
      * @param string $usu usuario
      * @param string $pass Contraseña
      * @return bool true si son correctos los datos false en caso conta
      */
     private function login()
     {   
         
         $db = new DB();
         
         $query = "SELECT
                u.id idUsu, usuario Usu, u.nombre Nom, u.es_administrador esAdm, c.nombre categoria
                FROM usuarios u
                    INNER JOIN categorias c
                    ON c.id = u.id_categoria
            WHERE u.usuario = '$this->usuario' LIMIT 1";

         $usuario = $db->row($query);
         
         $idUsuario = (isset($usuario['idUsu'])) ? $usuario['idUsu'] : "";
         $nom_usuario = (isset($usuario['Usu'])) ? $usuario['Usu'] : "";
        
        if($idUsuario AND $nom_usuario){
            
            $this->id = $idUsuario;
            $this->nombre = $usuario['Nom'];
            $this->es_administrador = $usuario['esAdm'];
            $this->categoria = $usuario['categoria'];
            
            $query_pass = "SELECT contrasena FROM usuarios WHERE id = $this->id";
            
            $passBD = $db->single($query_pass);
            
            if($passBD){
                
                $t_hasher = new PasswordHash(8, FALSE);                
                $check = $t_hasher->CheckPassword($this->contrasena,$passBD); // Revisamos que conicidan contraseñas
                
                if($check){
                    
                    $query_reg_login = "UPDATE usuarios SET ultimo_ingreso = '$this->ultimo_ingreso' WHERE id = $this->id";                    
                    $db->query($query_reg_login);
                    $respuesta = "Datos correctos";
                    
                }else{
                   $respuesta = "Contraseña incorrecta";
                }                
                
            }else{
                $respuesta = "Contraseña no encontrada";
            }
            
        }else{
            $respuesta = "Usuario no encontrado";
        }
        
        return $respuesta;        
        
    } // End login()    
    
    function __destruct() {
        //echo "usuario destruido";
    }
    
} // End class

?>