
    var counterDate = 1;
    var counterNumber = 1;
    var counterRadioButton = 1;
    var counterSelect = 1;
    var counterText = 1;
    var counterTextArea = 1;
    var counterArchivo = 1;

    function addAllInputs(divName,inputType){

    	var newdiv = document.createElement('div');
        var name = $('#name').val();
        var id=$('#mySelect').val();

        if(name==""){
            alert("Por favor revise, el nombre del input no puede ser vacío.");
            $('#name').focus();         //Enfoca el campo #name
        }else{
            switch(inputType) {   

                case 'date':
                //alert("fecha si")
                    newdiv.innerHTML=name+'<br><input type="date" name="fecha'+counterDate+'" id="fecha'+counterDate+'">';
                    counterDate++;
                    break;
                case 'file':
                    newdiv.innerHTML =name+'<br><input type="file" name="myFile" id="myFile" size="40" >';
                    select.remove(1); // Elimina del select la opción "Archivo"
                    break;  

                case 'number':      // Ok
                    newdiv.innerHTML =name+'<br><input type="number" name="myNumber'+counterNumber+'" min="1" max="2999999999" id="myNumber'+counterNumber+'" placeholder="Ingrese '+name+'">';
                    counterNumber++;
                    break;
                /*    
                  case 'checkbox':

                       newdiv.innerHTML = "Entry " + (counterCheckBox + 1) + " <br><input type='checkbox' name='myCheckBoxes[]'>";

                       counterCheckBox++;

                       break;

                case 'radio':

                       newdiv.innerHTML = "Entry " + (counterRadioButton + 1) + " <br><input type='radio' name='myRadioButtons[]'>";

                       counterRadioButton++;

                       break;
                */
                case 'select':  //ok    
                    var selection =$('#div_select').html();
                    var nombre_select =$('#nombre_select').val();
                    if(nombre_select==""){
                        alert("El nombre del select no puede estar vacío. Revise por favor")
                    }else{
                        newdiv.innerHTML = nombre_select +"<br>"+ selection;
                        $('#cambia').html("");
                        $('#select').focus();
                            
                        counterSelect++;
                    }

                    break;   
               
                case 'text': //ok
                    newdiv.innerHTML = name + "<br><input type='text' name='texto"+counterText+"' id='texto"+counterText+"' placeholder='Ingrese "+name+"' class='input_text'>";
                    counterText++;
                    break;
                 case 'textarea': //ok
                    newdiv.innerHTML = name + "<br><textarea rows='4' cols='50' name='textarea"+counterText+"' id='textarea"+counterText+"' placeholder='Ingrese "+name+"' class='input_textarea'>";
                    counterText++;
                    break;    

                /*  case 'textarea':

                    newdiv.innerHTML = "Entry " + (counterTextArea + 1) + " <br><textarea name='myTextAreas[]'>type here...</textarea>";

                       counterTextArea++;
                       break;
                */       
            }

             document.getElementById("para_fabricar").appendChild(newdiv);
             $('#name').val('');
             $('#name').focus();
        }
    }
    function cambia_select(){
    	var valor=$('#select').val();
   			$('#name').focus();
    	
    	switch(valor){
            case 'date':
                $('#cambia').html('<input type="button" value="Agregar Input Fecha" onClick="addAllInputs(\'estediv01\',\'date\');">')
                break;
            case 'file':
                $('#cambia').html('<input type="button" value="Agregar Input FILE" onClick="addAllInputs(\'estediv01\',\'file\')">')
                break;
    		case 'number':
    			$('#cambia').html('<input type="button" value="Agregar Input Numero" onClick="addAllInputs(\'estediv01\',\'number\');">')
    			break;
            case 'select':
                $('#cambia').html('Titulo del input Select<br><input type="text" id="nombre_select"><div id="div_select"><select id="mySelect'+counterSelect+'" class="select_opciones" name="mySelect'+counterSelect+'" class="input_select"><option>-- Seleccione Opcion -- </option> </select><br></div><button type="button" onclick="agrega_select()">Inserte Opcion del select antes de selección</button><input type="button" value="Agregar Input Select" onClick="addAllInputs(\'estediv01\',\'select\');">')  
                break;  
            case 'text':
                $('#cambia').html('<input type="button" value="Agregar Input Texto" onClick="addAllInputs(\'estediv01\',\'text\');">')
                break;
            case 'textarea':
                $('#cambia').html('<input type="button" value="Agregar Input Textarea" onClick="addAllInputs(\'estediv01\',\'textarea\');">')
                break;    
    	}
    }
    function agrega_select() {
	    var x = document.getElementById("mySelect"+counterSelect);
	    var name= $('#name').val(); // Captura nombre del select
        if(name==""){
            alert("Por favor revise, la opción del select no puede ser vacía.");
            $('#name').focus();         //Enfoca el campo #name
        }else{
            if (x.selectedIndex >= 0) {
                var option = document.createElement("option");
                option.text = name;
                var sel = x.options[x.selectedIndex];  

                if(x.selectedIndex){        //Si tiene seleccionado algun select lo ingresa sobre el seleccionado
                    x.add(option, sel);
                }else{                      //Si esta seleccionado "--Seleccione Opcion--"      
                    x.add(option);
                }
                $('#name').val('');         //Borra el campo #name
                $('#name').focus();         //Enfoca el campo #name
            }
        }
	}
	function muestra_formulario(){
		var formulario=$('#para_fabricar').html();
		alert(formulario)
	}