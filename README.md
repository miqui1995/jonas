Este es el README de Jonas

Para tener en cuenta. 
/******************************************************************************************************/
1. En la terminal inicia con el comando (psql) para iniciar por consola el postgres.
2. Ingresa el comando (psql -U postgres -h localhost)(Si pide contraseña es "postgres") y allí empieza a parametrizar el usuario y tablas por consola.
3. Ingresa el comando (CREATE USER admin WITH PASSWORD ='orfeo';) no olvidar el punto y coma antes de pulsar la tecla "enter"; 
4. Luego da los permisos  al usuario "admin" con los siguientes comandos: (GRANT USAGE ON SCHEMA public TO admin;)(GRANT ALL ON ALL TABLES IN SCHEMA public TO admin ;)(GRANT ALL ON ALL SEQUENCES IN SCHEMA public TO admin ;)(GRANT ALL ON ALL FUNCTIONS IN SCHEMA public TO admin ;)

5. Abre el archivo base_de_datos_jonas.sql, copia y pega en consola o en el gestor phppgadmin o pgAdminIII y ejecuta create database jonas_log2 del "Primer Bloque". 
6. Verificar que no tenga tablas por defecto. Debe estar completamente vacía la base de datos jonas_log2.
7. Abre el archivo base_de_datos_jonas.sql, copia y pega en gestor phppgadmin o pgAdminIII y ejecuta create database jonas2 del "Primer Bloque".
8. Verificar que no tenga tablas por defecto. Debe estar completamente vacía la base de datos jonas2.
9. Entra a la bd jonas_log2 y ejecuta la segunda parte de "Primer Bloque" (Crear tablas).
10. Entra a la bd jonas2 y adjunta el archivo base_de_datos_jonas.sql y ejecuta (Crear tablas).
11. Si está instalando en XAMPP por defecto va a arrojar el error de "Uncaught Error: Call to undefined function pg_connect()" para lo que tiene que buscar en el archivo php.ini y descomenta( remover el ; simbolo al inicio ) de las siguientes lineas:
	*	extension=php_pdo_pgsql.dll
	*	extension=php_pgsql.dll
10.1 Si está instalando en Linux (Probado en Ubuntu 16_04 LTS) por defecto va a arrojar el error de "Uncaught Error: Call to undefined function pg_connect()" para lo que tiene que buscar en el archivo php.ini y descomenta( remover el ; simbolo al inicio ) de las siguientes lineas:
	*	extension=php_pdo_pgsql.dll   
	*	extension=php_pgsql.dll
	Luego debe ejecutar en la terminal como root php -v y si tiene por ejemplo la version php (PHP 7.0.22-0ubuntu0.16.04.1 (cli) ( NTS )) ejecuta en la terminal el comando (apt-get install php7.0-pgsql)	
/******************************************************************************************************/

Parametrizacion		
1. Solicita organigrama de la entidad especificando las dependencias activas y funcionales para crear dependencias por interfaz con dependencia padre respectiva (Se necesita permiso de administrador de sistema por defecto "administrador" password "123")(En este paso se crean las dependencias de la empresa o entidad). 
2. Solicita usuarios por dependencia (Se necesita permiso de administrador de sistema) especificando cual de ellos va a tener permiso de 
	- Administrador de Sistema (Uno solo por dependencia).
	- Jefe de la dependencia (Vo.Bo.) (Uno solo por dependencia).
	- Distribuidor de la dependencia (Uno solo por dependencia).
	- Jefe de Archivo (Uno solo por dependencia)
	- Auxiliar Archivo 
	- Usuario
(En este paso se crean los usuarios del sistema)	
3. Definir tipos de documento que tengan retención definida para radicación de entrada (Se necesita permiso de administrador de sistema). Por ejemplo Tutelas (2 dias habiles)
4. Definir tipos de documento para PQR (Peticiones, Quejas, Radicados) vienen precargados Peticiones, Quejas y Reclamos con respectivos terminos. 	
5. Definir tipos de radicado para el sistema en general. Vienen precargados Entrada(1), Salida(2) , Normal(3) e Interna(4).
6. Definir de cual dependencia van a desprender los consecutivos por cada una. (Se necesita permiso de administrador de sistema). 
7. En el navegador Google Chrome ni en explorer hay problema, pero en firefox la tecla "backspace" (<-) devuelve a la pagina anterior complicando la interaccion jquery cuando digitan algun valor mal. Para corregirlo, en la barra de direcciones del navegador se escribe "about:config" y se modifica el valor de "browser.backspace_action" (0 la tecla backspace regresa a la pagina anterior, 1 la tecla backspace hace pasar a la pagina siguiente del documento, 2 la tecla backspace está desactivada.)
	En caso que requiera para google chrome se puede utilizar BackStop una aplicacion de navegador chrome. Hay que habilitarla para que funcione en modo incognito pero es muy sencillo. Tutorial(https://www.laptopmag.com/articles/disable-backspace-chrome-ie-firefox)
8. Para el logo del sticker hay que poner en la ruta "imagenes/iconos/logo_largo.png" con fondo transparente o si lo pone con extensión ".png" debe modificar la ruta de la imagen en el archivo "radicacion/radicacion_entrada/sticker.php". El tamaño promedio es de 506.9 kB (506,904 bytes)
9. Para el logo de la empresa, hay que poner en la ruta "imagenes/iconos/logo_largo.png". El tamaño promedio es de 22.2 kB (22,248 bytes). El logo de la empresa con fondo transparente. Si lo pone con otra extensión(jpg, etc) debe modificar la ruta de la imagen en los archivos:
10. En el archivo login/validar_inactividad.php hay que modificar la variable $entidad(Jardin Botanico Bogota) y codigo_entidad(JBB)
11. En el archivo login/index.php hay que modificar la variable ($caracteres_depend = "4"; // Cantidad de caracteres por dependencia configurados en el sistema)
12. En el archivo index.php hay que modificar el nombre de la entidad luego de (Software de Gestión Documental)[Jardín Botánico Bogotá]
13. Configurar las TRD

