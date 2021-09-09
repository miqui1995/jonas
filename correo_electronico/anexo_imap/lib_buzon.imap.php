<?php
/*****************************************************************************************
	* imap_open(): Abrir un flujo IMAP a un buzón. Devuelve un flujo IMAP en caso de éxito o FALSE en caso de error.
	* imap_search(): Array de numeros de mensajes que coinciden con el criterio de búsqueda dado o FALSE si es erronea la consulta. EJEMPLO = Array([0] => 1 [1] => 2 [2] => 3 [3] => 4 [4] => 5 [5] => 6 ).
	* imap_uid(): Devuelve el UID del número de secuencia del mensaje dado. Un UID es un identificador único.
	* imap_fetch_overview(): Array de objetos que describen una cabecera de mensaje cada uno. El objeto sólo definirá una propiedad si ésta existe. Las propiedades posibles son: "subject - el sujeto del mensaje","from - quién lo envió","to - destinatario","date - cuándo fue enviado","message_id - ID del mensaje","references - es una referencia a este id de mensaje","in_reply_to - es una respueste a este id de mensaje","size - tamaño en bytes","uid - UID del mensaje que está en el buzón","msgno - número de secuencia de mensaje en el buzón","recent - este mensaje está marcado como reciente","flagged - este mensaje está marcado","answered - este mensaje está marcado como respondido","deleted - este mensaje está marcado para su eliminación","seen - este mensaje está marcado como ya leído","draft - este mensaje está marcado como borrador"
	* imap_mime_header_decode(): Decodifica las extensiones de la cabecera del mensaje MIME que no son texto ASCII. Los elementos decodificados son devueltos como un array de objetos, donde cada objeto tiene dos propiedades, charset y text. Si el elemento no ha sido codificado, y en otras palabras está en US-ASCII plano, la propiedad charset de ese elemento está establecida a default. Ejemplo =Charset: ISO-8859-1, Texto: Keld Jørn Simonsen, Charset: default, Texto:  <keld@example.com>"
	* imap_fetchheader(): Devuelve la cabecera de un mensaje sin filtrar, con formato » RFC2822 del mensaje especificado. Devuelve la cabecera del mensaje especificado como una cadena de texto.
	* imap_rfc822_parse_headers(): Analizar cabeceras de correo desde una cadena. Devuelve un objeto con las banderas y otras propiedades que vienen del servidor IMAP.
	* imap_fetchstructure(): Leer la estructura de un mensaje en particular. Devuelve un objeto que incluye la envoltura, información interna, tamaño, banderas y cuerpo de la estructura además de un objeto similar para cada adjunto mime. La estructura de los objetos devueltos es como sigue:'Objetos devueltos para imap_fetchstructure()','type	Tipo de cuerpo principal','encoding	Codificación de la transferencia del cuerpo','ifsubtype	TRUE si hay una cadena subtipo','subtype	Subtipo MIME','ifdescription	TRUE si hay una cadena de descripción','description	Contenido de la cadena de descripción','ifid	TRUE si hay una cadena de identificación','id	Cadena de identificación','lines	Número de líneas','bytes	Número de bytes','ifdisposition	TRUE si hay una cadena de disposición','disposition	Cadena de disposición','ifdparameters	TRUE si el array dparameters existe','dparameters	Un array de objetos donde cada objeto tiene una propiedad "attribute" y "value" correspondientes a los parámetros de la cabecera MIME Content-disposition.','ifparameters	TRUE si el array de parámetros existe','parameters	Una array de objetos donde cada objeto tiene una propiedad "attribute" y "value".','parts	Un array de objetos idéntico en estructura al objeto de más alto nivel, cada uno correspondiendo una parte del cuerpo MIME.
	* imap_fetchbody():  Traer una sección en particular del cuerpo del mensaje, Traer una sección en particular del cuerpo de los mensajes especificados. Las partes del cuerpo no son decodificadas por esta función. Devuelve una sección en particular del cuerpo de los mensajes especificados como una cadena de texto
	* imap_body(): Leer el cuerpo del mensaje, devuelve el cuerpo del mensaje, numerado por msg_number en el buzón actual. Devuelve el cuerpo del mensaje especificado, como cadena.
/*****************************************************************************************
/*****************************************************************************************
	Estructura de consultar y mostrar los correos electronicos en el buzon, insertar los archivos anexos en carpeta seleccionada(no funccionando) Documentado y modificado por Gilberto Contreras para el software Jonas creditos a Licencia MIT Copyright "2018 Ican Bachors"(licencia de libre uso, copia, modificacion, fusion, publicacion, distribucion, sublicenciar o vender copias del Software) creditos a la biblioteca IMAP creada por Kiril Kirkov
/*****************************************************************************************/

class Imap {
    private $imapStream;
    private $plaintextMessage;
    private $htmlMessage;
    private $emails;
    private $errors = array();
    private $attachments = array();
    private $attachments_dir = 'attachments';
    public function connect($parte1, $parte2, $parte3) {
        $connection = imap_open($parte1, $parte2, $parte3);
        if (!preg_match("/Resource.id.*/", (string) $connection)) {
            return $connection; //return error message
        }
        $this->imapStream = $connection;
        return true;
    }
    public function getMessages($type = 'text', $limite_buzon, $invertido) {
        $this->attachments_dir = rtrim($this->attachments_dir, '/');
        $stream = $this->imapStream;
        
            $emails = imap_search($stream, 'ALL');
        
        
        $messages = array();
        if ($emails) {
            $this->emails = $emails;
            $limitador_pruebas = 1;
            foreach ($emails as $email_number) {
                $this->attachments = array();
                $uid = imap_uid($stream, $email_number);
                $messages[] = $this->loadMessage($uid, $type);
                if($limite_buzon != 0){
                    if ($limitador_pruebas++ == $limite_buzon) break;
                }
            }
        }
        if($limite_buzon != 0){
            return array(
                "status" => "success",
                "data" => $messages
            );
        }else{
            return array(
                "status" => "success",
                "data" => array_reverse($messages)
            );
        }
		
    }
    private function loadMessage($uid, $type) {
        $overview = $this->getOverview($uid);
        $array = array();
        $array['uid'] = $overview->uid;
        $array['subject'] = isset($overview->subject) ? $this->decode($overview->subject) : '';
        $array['date'] = date('Y-m-d h:i:sa', strtotime($overview->date));
        $headers = $this->getHeaders($uid);
        $array['from'] = isset($headers->from) ? $this->processAddressObject($headers->from) : array('');
        $structure = $this->getStructure($uid);
        if (!isset($structure->parts)) { // not multipart
            $this->processStructure($uid, $structure);
        } else { // multipart
            foreach ($structure->parts as $id => $part) {
                $this->processStructure($uid, $part, $id + 1);
            }
        }
        $array['message'] = $type == 'text' ? $this->plaintextMessage : $this->htmlMessage;
        $array['attachments'] = $this->attachments;
        return $array;
    }
    private function processStructure($uid, $structure, $partIdentifier = null) {
        $parameters = $this->getParametersFromStructure($structure);
        if ((isset($parameters['name']) || isset($parameters['filename'])) || (isset($structure->subtype) && strtolower($structure->subtype) == 'rfc822')
        ) {
            if (isset($parameters['filename'])) {
                $this->setFileName($parameters['filename']);
            } elseif (isset($parameters['name'])) {
                $this->setFileName($parameters['name']);
            }
            $this->encoding = $structure->encoding;
            $result_save = $this->saveToDirectory($uid, $partIdentifier);
            $this->attachments[] = $result_save;
        } elseif ($structure->type == 0 || $structure->type == 1) {
            $messageBody = isset($partIdentifier) ?
                    imap_fetchbody($this->imapStream, $uid, $partIdentifier, FT_UID | FT_PEEK) : imap_body($this->imapStream, $uid, FT_UID | FT_PEEK);
            $messageBody = $this->decodeMessage($messageBody, $structure->encoding);
            if (!empty($parameters['charset']) && $parameters['charset'] !== 'UTF-8') {
                if (function_exists('mb_convert_encoding')) {
                    if (!in_array($parameters['charset'], mb_list_encodings())) {
                        if ($structure->encoding === 0) {
                            $parameters['charset'] = 'US-ASCII';
                        } else {
                            $parameters['charset'] = 'UTF-8';
                        }
                    }
                    $messageBody = mb_convert_encoding($messageBody, 'UTF-8', $parameters['charset']);
                } else {
                    $messageBody = iconv($parameters['charset'], 'UTF-8//TRANSLIT', $messageBody);
                }
            }
            if (strtolower($structure->subtype) === 'plain' || ($structure->type == 1 && strtolower($structure->subtype) !== 'alternative')) {
                $this->plaintextMessage = '';
                $this->plaintextMessage .= trim(htmlentities($messageBody));
                $this->plaintextMessage = nl2br($this->plaintextMessage);
            } elseif (strtolower($structure->subtype) === 'html') {
                $this->htmlMessage = '';
                $this->htmlMessage .= $messageBody;
            }
        }
        if (isset($structure->parts)) {
            foreach ($structure->parts as $partIndex => $part) {
                $partId = $partIndex + 1;
                if (isset($partIdentifier))
                    $partId = $partIdentifier . '.' . $partId;
                $this->processStructure($uid, $part, $partId);
            }
        }
    }
    private function setFileName($text) {
        $this->filename = $this->decode($text);
    }
    private function saveToDirectory($uid, $partIdentifier) { //save attachments to directory
		$array = array();
		$array['part'] = $partIdentifier;
		$array['file'] = $this->filename;
		$array['encoding'] = $this->encoding;
        return $array;
    }
    private function decodeMessage($data, $encoding) {
        if (!is_numeric($encoding)) {
            $encoding = strtolower($encoding);
        }
        switch (true) {
            case $encoding === 'quoted-printable':
            case $encoding === 4:
                return quoted_printable_decode($data);
            case $encoding === 'base64':
            case $encoding === 3:
                return base64_decode($data);
            default:
                return $data;
        }
    }
    private function getParametersFromStructure($structure) {
        $parameters = array();
        if (isset($structure->parameters))
            foreach ($structure->parameters as $parameter)
                $parameters[strtolower($parameter->attribute)] = $parameter->value;
        if (isset($structure->dparameters))
            foreach ($structure->dparameters as $parameter)
                $parameters[strtolower($parameter->attribute)] = $parameter->value;
        return $parameters;
    }
    private function getOverview($uid) {
        $results = imap_fetch_overview($this->imapStream, $uid, FT_UID);
        $messageOverview = array_shift($results);
        if (!isset($messageOverview->date)) {
            $messageOverview->date = null;
        }
        return $messageOverview;
    }
    private function decode($text) {
        if (null === $text) {
            return null;
        }
        $result = '';
        foreach (imap_mime_header_decode($text) as $word) {
            $ch = 'default' === $word->charset ? 'ascii' : $word->charset;
            $result .= iconv($ch, 'utf-8', $word->text);
        }
        return $result;
    }
    private function processAddressObject($addresses) {
        $outputAddresses = array();
        if (is_array($addresses))
            foreach ($addresses as $address) {
                if (property_exists($address, 'mailbox') && $address->mailbox != 'undisclosed-recipients') {
                    $currentAddress = array();
                    $currentAddress['address'] = $address->mailbox . '@' . $address->host;
                    if (isset($address->personal)) {
                        $currentAddress['name'] = $this->decode($address->personal);
                    }
                    $outputAddresses = $currentAddress;
                }
            }
        return $outputAddresses;
    }
    private function getHeaders($uid) {
        $rawHeaders = $this->getRawHeaders($uid);
        $headerObject = imap_rfc822_parse_headers($rawHeaders);
        if (isset($headerObject->date)) {
            $headerObject->udate = strtotime($headerObject->date);
        } else {
            $headerObject->date = null;
            $headerObject->udate = null;
        }
        $this->headers = $headerObject;
        return $this->headers;
    }
    private function getRawHeaders($uid) {
        $rawHeaders = imap_fetchheader($this->imapStream, $uid, FT_UID);
        return $rawHeaders;
    }
    private function getStructure($uid) {
        $structure = imap_fetchstructure($this->imapStream, $uid, FT_UID);
        return $structure;
    }
    public function __destruct() {
        if (!empty($this->errors)) {
            foreach ($this->errors as $error) {
                //SAVE YOUR LOG OF ERRORS
            }
        }
    }
    public function archivos_anexos_guardar($usuario){
        $stream = $this->imapStream;
        $emails = imap_search($stream, 'ALL');
        if($emails){
            $limitador_pruebas = 1;
            $count = 1;
            rsort($emails);
            foreach($emails as $email_number){
                $overview = imap_fetch_overview($stream,$email_number,0);
                $message = imap_fetchbody($stream,$email_number,2);
                $structure = imap_fetchstructure($stream, $email_number);
                $attachments = array();
                if(isset($structure->parts) && count($structure->parts)){
                    for($i = 0; $i < count($structure->parts); $i++){
                        $attachments[$i] = array(
                           'is_attachment' => false,
                           'filename' => '',
                           'name' => '',
                           'attachment' => ''
                        );
                        if($structure->parts[$i]->ifdparameters){
                            foreach($structure->parts[$i]->dparameters as $object){
                                if(strtolower($object->attribute) == 'filename'){
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['filename'] = $object->value;
                                }
                            }
                        }
                        if($structure->parts[$i]->ifparameters){
                            foreach($structure->parts[$i]->parameters as $object){
                                if(strtolower($object->attribute) == 'name'){
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['name'] = $object->value;
                                }
                            }
                        }
                        if($attachments[$i]['is_attachment']){
                            $attachments[$i]['attachment'] = imap_fetchbody($stream, $email_number, $i+1);
                            if($structure->parts[$i]->encoding == 3){ 
                                $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                            }elseif($structure->parts[$i]->encoding == 4){ 
                                $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                           }
                       }
                    }
                    if(isset($_POST['limite'])){
                        if ($limitador_pruebas++ == $_POST['limite']) break;
                    }
                }
                foreach($attachments as $attachment){
                    if($attachment['is_attachment'] == 1){
                        $filename = $attachment['name'];
                        if(empty($filename)) $filename = $attachment['filename'];
                        if(empty($filename)) $filename = time() . ".dat";
                        $folder = "../bodega_pdf/correo_electronico/baul/$usuario";
                        if(!is_dir($folder)){
                            mkdir($folder);
                        }
                        $fp = fopen("./". $folder ."/" . $filename, "w+");
                        fwrite($fp, $attachment['attachment']);
                        fclose($fp);
                    }
                }
            }
        }
        imap_close($stream);
    }
}