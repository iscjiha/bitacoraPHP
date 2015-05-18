$(function(){
    
    function revisarSesion(){
        $.ajax({
            url: "return.php",
            data: {accion:'revisar_sesion'},
            method: "POST",
            success: function(res){
                if(res === 'salir'){
                    alert('El sistema se cerrara por tiempo de inactividad.');
                    window.location = 'logout.php';
                }
            }
        });
    } // End revisarSesion()
    
    function mostrarElemento(idElem){
        
        var tipoElem = $('#opcBtnPpalSidebar').val();
        var opc;
        
        if(tipoElem === 'actividades') opc = 'actividad';
        if(tipoElem === 'tickets'){
            opc = 'ticket';
            $('#iconInfTicket'+idElem).hide();
        }
        
        //alert('Mostrare formulario de ' + opc + ' de elemento ' + idElem);
        
        $('.cajaElemento').html("<img src='img/loading.gif'>");
        $.ajax({
            url: "return.php",
            data: {accion:'mostrar_'+opc, idElemento:idElem},
            method: "POST",
            success: function(elemento){
                //console.log(elemento);
                $('.cajaElemento').html(elemento);
            }
        }); 
            
    } // End mostrarElemento()
    
    function listaElementos(nd,idUsu){
        revisarSesion();
        
        var tipoElemento = $('#opcBtnPpalSidebar').val();
        //alert('Mostraremos '+tipoElemento+' entre '+nd+' dias y del usuario '+idUsu);
        $('.listadoElementos').html("<img src='img/loading.gif'>");
        $.ajax({
            url: "return.php",
            data: {accion:'listado_'+tipoElemento,numDias:nd,idUsu:idUsu},
            method: "POST",
            success: function(elementos){
                //console.log(elementos);
                $('.listadoElementos').html(elementos);
            }
        });
    } // End listaElementos()
    
    function mostrarNumTicketsPendientes(){
        $.ajax({
            url: 'return.php',
            method:'POST',
            data:{accion:'contar_tickets'},
            success: function(num){
                if(parseInt(num) > 0)
                    $('.contadorTickets').text('('+num+')');
                    $('#contadorTickets').val(num);
            }
        });        
    } // End mostrarNumTicketsPendientes()
    
    listaElementos();
    mostrarNumTicketsPendientes();
    setInterval(mostrarNumTicketsPendientes,10000);
    
    // Elemento de listado
    $(document).on("click", '.elemListado', function(){
        revisarSesion();
        var idElem = $(this).attr('id');
        if(idElem > 0) {
            $('.elemListado').removeClass('elementoActivo');
            $(this).addClass('elementoActivo');
            mostrarElemento(idElem);
        }        
    }); // End elemListado.click()
    
    // Edición de fecha
    $(document).on("click","#btnEditarFecha",function(){
        
        revisarSesion();
        
        $(this).hide();
        
        var fechaEd = $('#txtFechaActividadEdicion');
        var fechaActual = fechaEd.val();
        var idAct = $(this).attr('idAct');
        
        $('#lblFechaActiVista').hide();
        $('#btnConfirmarEdicionFecha').show();
        $('#btnCancelarEdicionFecha').show();
        
        fechaEd.show();
        
        $(document).on("click",'#btnConfirmarEdicionFecha',function(){
            
            var fechaNueva = fechaEd.val();
            
            if(fechaNueva !== fechaActual){
            
                var r = confirm('¿Confirma el cambio de fecha de la actividad?');

                if(r){
                    
                    $.ajax({
                        url: 'do.php',
                        method:'POST',
                        data:{accion:'modificar_fecha_actividad',actividad:idAct,nuevaFecha:fechaNueva},
                        success: function(res){
                            if(res === '1')
                                $('#msjFrm').html("<span class='msjAlerta'>Ya tiene una actividad con esa fecha.</span>").fadeOut(3000);
                            if(res === '2')
                                $('#msjFrm').html("<span class='msjConfirmacion'>Fecha modificada.!!</span>").fadeOut(3000);
                                ocultarElementosEdicionFecha();
                                listaElementos($('#cboFiltroDesde').val(),$('#cboUsuarios').val());
                        }
                    });
                
                } else {
                    ocultarElementosEdicionFecha();
                }
            }else{
                $('#msjFrm').html("<span class='msjAlerta'>La nueva fecha es igual a la fecha anterior..!!</span>").fadeOut(3000);
            }
            
        }); // End btnConfirmarEdicionFecha.click
        
        $(document).on("click","#btnCancelarEdicionFecha",function(){
            revisarSesion();
            ocultarElementosEdicionFecha();
        });
        
        function ocultarElementosEdicionFecha(){
            fechaEd.val(fechaActual).hide();
            $('#btnCancelarEdicionFecha').hide();
            $('#btnConfirmarEdicionFecha').hide();
            $('#lblFechaActiVista').show();
            $('#btnEditarFecha').show();
        }
        
    }); // End btnEditarFecha.click()
    
    // Combo Filtro de actividad
    $('#cboFiltroDesde').change(function(){
        revisarSesion();
        var opc = $(this).val();
        
        if(opc === "pers")
            $('#cajaBuscarRangoFechas').show();
        else
            listaElementos(opc,$('#cboUsuarios').val());
        
    }); // End cboFiltroDesde.change()
    
    // Botón de menu principal
    $('.btnMenuPpal').click(function(){
        
        revisarSesion();
        $('.elemListado').removeClass('elementoActivo');
        
        var opc = $(this).text();
        
        switch(opc){
            
            case 'Agregar Actividad':
                $('#opcBtnPpalSidebar').val('actividades');
                mostrarElemento();
                break;
            
            case 'Nuevo Ticket':
                $('#opcBtnPpalSidebar').val('tickets');
                mostrarElemento();
                break;
            
            default:
                
        } // End switch
        
        $('#txtDescripcion').focus();
        
    }); // End btnMenuPpal.click()
    
    // Formulario Agregar Editar Actividad
    $(document).on("submit","#frmActividad",function(event){
        event.preventDefault();
        var url = $(this).attr('action');
        var datos = $(this).serialize();
        $('#msjFrm').html("<img src='img/loading.gif'>")
        $.post(url, datos, function(result) {
            console.log(result);
            if(parseInt(result) === 1){
                
                $('#msjFrm') .html("<span class='msjConfirmacion'>Correcto.!!</span>").fadeOut(4000);
                $('#btnGuardar').hide();
                $('#opcBtnPpalSidebar').val('actividades');
                $('.btnPpalSidebar').removeClass('btnPpalSidebarActivo');
                $('#btnPpalSidebarActividades').addClass('btnPpalSidebarActivo');
               
                listaElementos($('#cboFiltroDesde').val());
            }else {
                $('#msjFrm').html("<span class='msjAlerta'>Hubo un problema con el envio, revice los datos.!!</span>").fadeOut(4000);                
            }
        });
    }); // frmActividad.submit()
    
    // Botón Enviar al Jefe
    $(document).on("click","#btnEnviarJefe",function(){
        
        var r = confirm('¿Está seguro de enviar la actividad al Jefe? Al enviarla no podrá modificarla.');
        
        if(r){
            
            var actividad = {
                id:$(this).attr('idAct'),
                descripcion:$('#txtDescripcion').val()
            }
            
            $.ajax({
                url: 'do.php',
                method:'POST',
                data:{accion:'enviar_actividad_jefe',actividad:actividad},
                success: function(res){
                    //console.log(res);
                    if(parseInt(res) === 1){
                        $('#btnEnviarJefe').fadeOut();
                        $('#btnEditarActividad').fadeOut();
                        $('#msjFrm').html("<span class='msjConfirmacion'>Actividad enviada.!!</span>").fadeOut(3000);
                        listaElementos($('#cboFiltroDesde').val());
                        mostrarElemento(actividad.id);
                    }else {
                        $('#msjFrm').html("<span class='msjAlerta'>No se envió al jefe la actividad.!!</span>").fadeOut(3000);
                    }
                }
            });
        }
        
    }); // End btnEnviarJefe.click()
    
    // Formulario insertar checada
    $('#frmInsertarChecada').submit(function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        var datos = $(this).serialize();
        $('#msgInforma').html("<img src='img/loading.gif'>");
        $.post(url, datos, function(result) {
            //console.log(result);
            if(parseInt(result) === 1){
                $('#dialogoChecado').slideUp();
            }else {
                $('#msgInforma').text('Revice los datos');
            }
        });
    }); // End frmInsertarChecada.submit()
    
    // Combo usuarios
    $('#cboUsuarios').change(function(){
        revisarSesion();
        var idUsu = $(this).val();
        listaElementos($('#cboFiltroDesde').val(),idUsu);
    });
    
    // Boton principal de side bar
    $('.btnPpalSidebar').click(function(){
        
        $('.btnPpalSidebar').removeClass('btnPpalSidebarActivo');
        $(this).addClass('btnPpalSidebarActivo');
        
        $('#opcBtnPpalSidebar').val($(this).attr('title'));
        listaElementos();
    }); // End btnPpalSidebar.click()
    
    $(document).on('click','#btnResponderTicket',function(){
        
        var acc = $(this).text();
        
        if(acc === 'Responder'){
            $(this).text('Cancelar Respuesta').css('color','#F8E0E0');
            $('#cajaRespuestaTicket').slideDown();
            $('#txtRespuestaTicket').focus();
        }else{
            $(this).text('Responder').css('color','#BDBDBD');;
            $('#cajaRespuestaTicket').slideUp();
            $('#txtRespuestaTicket').val('');
        }
        
        $('#btnEnviarRespuestaTicket').click(function(){
            
            var n = $('#txtRespuestaTicket').val().length;
            var f = $('#txtFechaAtencion').val();
            
            if(n > 20){
                if(f){
                    var idT = $('#txtID').val();
                
                    $.ajax({
                        type: "POST",
                        url: 'do.php',
                        data: {accion:'responder_ticket',idTicket:idT,respuesta:$('#txtRespuestaTicket').val(),fechaAtencion:f},
                        success: function(res){
                            //console.log(res);
                            if(parseInt(res)){
                                var numT = parseInt($('#contadorTickets').val());
                                var cont = numT - 1;
                                (cont > 0) ? $('.contadorTickets').text('('+cont+')') : $('.contadorTickets').hide();
                                $('#btnResponderTicket').css('color','#BCF5A9').text('Respuesta enviada correctamente.!').fadeOut(3500);
                                $('#cajaRespuestaTicket').slideUp();
                                listaElementos($('#cboFiltroDesde').val());
                            }
                        }
                    });
                    
                }else{
                    alert('Ingrese la fecha en que atendio el ticket.');
                    $('#txtFechaAtencion').focus();
                }
            }else{
                alert('Ingrese una respuesta con mas detalle.');
                $('#txtRespuestaTicket').focus();
            }
            
        }); // End btnEnviarRespuestaTicket.click()
        
    }); // End btnResponderTicket.click()
    
    $(document).on('click','#btnVerRespuesta',function(){
        var idT = $(this).attr('idTicket');
        $('#cajaRespuesta').show().html("<img src='img/loading.gif'>");
        if(idT){
             $.ajax({
                 type: "POST",
                 url: 'return.php',
                 data: {accion:'mostrar_respuesta',idTicket:idT},
                 success: function(res){
                    $('#cajaRespuesta').html(res);
                }
            });
        }
    });
    
    // Boton ingresar hora de salida
    
    $(document).on('click', '.btnIngresarChecada', function(){
        
        $(this).hide();
        
        var idChecada = $(this).attr('idChecada');
        
        $('#cajaIngresarSalidaTabla_'+idChecada).html("<input type='time' id='horaSalida' value='16:00'><input type='button' id='btnOK' value='Guardar'>");
        
        $('#btnOK').click(function(){
            
            var horaSalida = $('#horaSalida').val();
            
            $.ajax({
                 type: "POST",
                 url: 'do.php',
                 data: {accion:'ingresar_checada_anterior',txtID:idChecada,txtHoraChecada:horaSalida},
                 success: function(res){
                     if(parseInt(res) === 1){
                        alert('Se guardo correctamente la hora de salida.');
                        listaElementos($('#cboFiltroDesde').val());
                     }
                }
            });
            
        });
        
    }); // End btnIngresarChecada.click()
    
}); // End function()