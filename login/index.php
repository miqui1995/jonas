<?php 
/* Archivo invocado por ajax funciones_login.js - funcion entra().
 recibe user_j y passd_j por POST */
if(!isset($_SESSION)){
    session_start();
}
include ("conexion2.php");

$fecha_especial = $_POST['festivo'];
$passd_j        = $_POST['passd_j'];
$user_j         = $_POST['user_j'];
$codigo_entidad = $_POST['cod_ent'];
$version_jonas  = $_POST['ver'];

switch ($codigo_entidad) {
    case 'AV1':
        $caracteres_depend      = "3"; // Cantidad de caracteres por dependencia configurados en el sistema
        $entidad                = "Alcaldia Villeta";
        break;

    case 'EJC':
    case 'EJEC':
        $caracteres_depend      = "5"; // Cantidad de caracteres por dependencia configurados en el sistema
        $entidad                = "Ejercito Nacional";
        break;

    case 'GC1':
        $caracteres_depend      = "4"; // Cantidad de caracteres por dependencia configurados en el sistema
        $entidad                = "Gamma Corp SAS";
        break;

    case 'JBB':
        $caracteres_depend      = "4"; // Cantidad de caracteres por dependencia configurados en el sistema
        $entidad                = "Jardín Botánico de Bogotá";
        break;
    
    default:
        $caracteres_depend      = "4"; // Cantidad de caracteres por dependencia configurados en el sistema
        $entidad                = "Gamma Corp SAS";
        break;
}
$_SESSION['fechas_especiales'] = $fecha_especial;

$isql ="select * from usuarios where login = trim(upper('$user_j')) and pass = md5('$passd_j')";

$resultado = pg_query($conectado,$isql);

if($resultado==false){
    echo "false";
}else{
    if(pg_num_rows ($resultado)>0){

        /* Se guardan las variables de sesión correspondientes a permisos y datos del usuario que ingresa */
        $linea = pg_fetch_array($resultado);    

        $administrador_sistema      = $linea['administrador_sistema'];
        $cargo_usuario              = $linea['cargo_usuario'];
        $codigo_dependencia         = $linea['codigo_dependencia'];
        $creacion_expedientes       = $linea['creacion_expedientes'];
        $cuadro_clasificacion       = $linea['cuadro_clasificacion'];
        $estado                     = $linea['estado'];
        $id_usuario                 = $linea['id_usuario'];
        $imagen                     = $linea['path_foto'];
        $inventario                 = $linea['inventario'];
        $jefe_dependencia           = $linea['jefe_dependencia'];
        $modificar_radicado         = $linea['modificar_radicado'];
        $nivel                      = $linea['nivel_seguridad'];
        $nombre                     = $linea['nombre_completo'];
        $nuevo                      = $linea['usuario_nuevo'];
        $perfil                     = $linea['perfil'];
        $prestamo_documentos        = $linea['prestamo_documentos'];
        $radicacion_interna         = $linea['radicacion_interna'];
        $radicacion_normal          = $linea['radicacion_normal'];
        $radicacion_resoluciones    = $linea['radicacion_resoluciones'];
        $radicacion_salida          = $linea['radicacion_salida'];
        $scanner                    = $linea['scanner'];
        $ubicacion_topografica      = $linea['ubicacion_topografica'];
        $usuario                    = $linea['login'];
        $ventanilla_radicacion      = $linea['ventanilla_radicacion'];
        // $path_firma                 = $linea['path_firma'];

        $_SESSION['administrador_sistema']      = $administrador_sistema;
        $_SESSION['caracteres_depend']          = $caracteres_depend;
        $_SESSION['cargo_usuario']              = $cargo_usuario;
        $_SESSION['codigo_entidad']             = $codigo_entidad;
        $_SESSION['creacion_expedientes']       = $creacion_expedientes;
        $_SESSION['cuadro_clasificacion']       = $cuadro_clasificacion;
        $_SESSION['dependencia']                = $codigo_dependencia;
        $_SESSION['entidad']                    = $entidad;
        $_SESSION['id_usuario']                 = $id_usuario;
        $_SESSION['imagen']                     = $imagen; 
        $_SESSION['inventario']                 = $inventario;
        $_SESSION['jefe_dependencia']           = $jefe_dependencia; 
        $_SESSION['login']                      = $usuario;  
        $_SESSION['modificar_radicado']         = $modificar_radicado;
        $_SESSION['nivel']                      = $nivel;   
        $_SESSION['nombre']                     = $nombre;
        $_SESSION['perfil']                     = $perfil;
        $_SESSION['prestamo_documentos']        = $prestamo_documentos;
        $_SESSION['radicacion_interna']         = $radicacion_interna; 
        $_SESSION['radicacion_normal']          = $radicacion_normal; 
        $_SESSION['radicacion_resoluciones']    = $radicacion_resoluciones; 
        $_SESSION['radicacion_salida']          = $radicacion_salida; 
        $_SESSION['scanner']                    = $scanner; 
        $_SESSION['ubicacion_topografica']      = $ubicacion_topografica;
        $_SESSION['usuario_nuevo']              = $nuevo;
        $_SESSION['ventanilla_radicacion']      = $ventanilla_radicacion; 
        $_SESSION['version_jonas']              = $version_jonas; 
        // $_SESSION['path_firma']                 = $path_firma; 

        $_SESSION['ultimo_ingreso']             = date("Y-n-j H:i:s"); 
    
        if($estado=="INACTIVO"){
            echo "inactivo";
        }else{
            echo "Bienvenido a Jonas";     
        }
    }else{
        echo "";
    }          
}
?>
