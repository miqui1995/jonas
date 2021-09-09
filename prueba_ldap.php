 <?php 

/**
 * INFORMACION ACTIVE DIRECTORY 
 */
// Nombre o IP del servidor de autenticacion LDAP
$ldapServer = 'SrvADRDOM01.adr.Incoder.local';
// Cadena de busqueda en el servidor.
//usuario en da es "OrfeoAD"
$cadenaBusqLDAP = 'DC=adr,DC=Incoder,DC=local';
// Campo seleccionado (variable LDAP) para realizar la autenticacion.
$campoBusqLDAP = 'mail';
//Usuario AD para realizar bind con el servidor AD. SOLO para W2K3 o superior. //samaccountname=>orfeoservicio
$usrLDAP = "CN=Orfeo Servicio,OU=Cuentas Administrativas,OU=Oficina de Tecnologias de la Información,OU=Presidencia,OU=ADR_Users,DC=adr,DC=Incoder,DC=local";
//Contrasena del usuario anterior.
$pwdLDAP = 'E@02P%aM';



function checkldapuser($username, $password, $ldapServer) {
    $retorno = "";
    define('ADODB_LANG', 'es');
    require dirname(__FILE__) . "/config.php";  // Esto está en la línea 6 a la 16 de éste archivo.
    require dirname(__FILE__) . "/adodb/adodb-exceptions.inc.php";
    require dirname(__FILE__) . "/adodb/adodb.inc.php";

    $username = strtolower($username);
    try {
        $ldap = NewADOConnection('ldapdnp');
		$ldap->SetFetchMode(ADODB_FETCH_ASSOC);
		$rsLdap = $ldap->Connect($ldapServer, $usrLDAP, $pwdLDAP, $cadenaBusqLDAP);
		if ($rsLdap) {
			$tmpUsr = (strpos($username, "@") !== FALSE) ? substr($username, 0, strpos($username, "@")) : $username;
			$filter = "(mail=$username)";
			$row = $ldap->Execute( $filter );
			if ($row->RecordCount() > 0) {
				$dnUserDA = $row->fields['distinguishedName'][0];
				$rsLdap = $ldap->Connect($ldapServer, $dnUserDA, $password, $cadenaBusqLDAP);
				if ($rsLdap) {
	
				} else {
					$retorno = "CREDENCIALES ERRONEAS.";
				}
			} else {
				$retorno = "USUARIO NO HALLADO EN LDAP.";
			}
		} else {
			$retorno = "ERROR AUTENTICACI&Oacute;N ORFEOAD";
		}
		@$ldap->Close();
    } catch (exception $e) {
        $retorno = str_replace("!! LDAPDNP LDAPDNP: ", "", $e->msg);
    }

    return $retorno;
}

  ?>
