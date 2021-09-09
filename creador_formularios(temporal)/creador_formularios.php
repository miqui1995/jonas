    <script type="text/javascript" src="include/js/jquery.js"></script>
    <script src="addInput.js" language="Javascript" type="text/javascript"></script>

    <form method="POST">

         <div id="estediv01">
            <select id="select" onchange="cambia_select()" class='select_opciones'>
                <option value="default">-- Seleccione Opcion de Input --</option>                             
                <option value="file" id="input_file">Archivo</option>                 
                <option value="textarea">Area de Texto</option>
                <option value="date">Fecha</option>
                <option value="number">Numero</option>
                <option value="select">Select</option>                                                                      
                <option value="text">Texto</option>
            </select><br>
              <div id="cambia">
                <input type="button" value="Agregar Texto" onClick="addAllInputs('estediv01','text');">
              </div>
                 Nombre de cada input<br>
                <input type="text" name="myInputs[]" id="name" >
              <hr>
              <div id="para_fabricar"></div>
              <input type="button" value="Formulario Listo" onClick="muestra_formulario();">
         </div>

    </form>


 <?php
?>