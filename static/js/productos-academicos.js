
function cambiarTexto(texto) {
    $('#exampleModalLabel').text(texto);
}

/* Productos Académicos - Formulario dinámico de productos */
function formularioProducto(tipo) {
    if (tipo == 0) { // No se seleccionó un producto
        $('#titulo-producto').attr("class", "desactivado");
        $('#autor-producto').attr("class", "desactivado");
        $('#journal-producto').attr("class", "desactivado");
        $('#anio-producto').attr("class", "desactivado");
        $('#volumen-producto').attr("class", "desactivado");
        $('#numero-producto').attr("class", "desactivado");
        $('#paginas-producto').attr("class", "desactivado");
        $('#mes-producto').attr("class", "desactivado");
        $('#editorial-producto').attr("class", "desactivado");
        $('#serie-producto').attr("class", "desactivado");
        $('#direccion-producto').attr("class", "desactivado");
        $('#edicion-producto').attr("class", "desactivado");
        $('#how-published-producto').attr("class", "desactivado");
        $('#titulo-libro').attr("class", "desactivado");
        $('#crossref-producto').attr("class", "desactivado");
        $('#editor-producto').attr("class", "desactivado");
        $('#institucion-producto').attr("class", "desactivado");
        $('#capitulo-producto').attr("class", "desactivado");
        $('#tipo-producto').attr("class", "desactivado");
        $('#nota-producto').attr("class", "desactivado");
    }

    if (tipo == 1) { // Artículo
        // Obligatorias
        $('#titulo-producto').attr("class", "visible");
        $('#autor-producto').attr("class", "visible");
        $('#journal-producto').attr("class", "visible");
        $('#anio-producto').attr("class", "visible");
        $('#tituloProducto').prop('required', true);
        $('#autorProducto').prop('required', true);
        $('#journalProducto').prop('required', true);
        $('#yearPublication').prop('required', true);
        // Opcionales
        $('#volumen-producto').attr("class", "visible");
        $('#numero-producto').attr("class", "visible");
        $('#paginas-producto').attr("class", "visible");
        $('#mes-producto').attr("class", "visible");
        // Desactivadas
        $('#editorial-producto').attr("class", "desactivado");
        $('#serie-producto').attr("class", "desactivado");
        $('#direccion-producto').attr("class", "desactivado");
        $('#edicion-producto').attr("class", "desactivado");
        $('#how-published-producto').attr("class", "desactivado");
        $('#titulo-libro').attr("class", "desactivado");
        $('#crossref-producto').attr("class", "desactivado");
        $('#editor-producto').attr("class", "desactivado");
        $('#institucion-producto').attr("class", "desactivado");
        $('#capitulo-producto').attr("class", "desactivado");
        $('#tipo-producto').attr("class", "desactivado");
        $('#nota-producto').attr("class", "desactivado");
    }

    if (tipo == 2) { // Libro
        // Obligatorias
        $('#autor-producto').attr("class", "visible");
        $('#titulo-producto').attr("class", "visible");
        $('#editorial-producto').attr("class", "visible");
        $('#anio-producto').attr("class", "visible");
        $('#autorProducto').prop('required', true);
        $('#tituloProducto').prop('required', true);
        $('#editorial').prop('required', true);
        $('#yearPublication').prop('required', true);
        // Opcionales
        $('#volumen-producto').attr("class", "visible");
        $('#serie-producto').attr("class", "visible");
        $('#direccion-producto').attr("class", "visible");
        $('#edicion-producto').attr("class", "visible");
        $('#mes-producto').attr("class", "visible");
        // Desactivadas
        $('#journal-producto').attr("class", "desactivado");
        $('#numero-producto').attr("class", "desactivado");
        $('#paginas-producto').attr("class", "desactivado");
        $('#how-published-producto').attr("class", "desactivado");
        $('#titulo-libro').attr("class", "desactivado");
        $('#crossref-producto').attr("class", "desactivado");
        $('#editor-producto').attr("class", "desactivado");
        $('#institucion-producto').attr("class", "desactivado");
        $('#capitulo-producto').attr("class", "desactivado");
        $('#tipo-producto').attr("class", "desactivado");
        $('#nota-producto').attr("class", "desactivado");
    }

    if (tipo == 3) { // Folleto
        // Obligatorias
        $('#titulo-producto').attr("class", "visible");
        $('#tituloProducto').prop('required', true);
        // Opcionales
        $('#autor-producto').attr("class", "visible");
        $('#how-published-producto').attr("class", "visible");
        $('#direccion-producto').attr("class", "visible");
        $('#mes-producto').attr("class", "visible");
        $('#anio-producto').attr("class", "visible");
        // Desactivadas
        $('#journal-producto').attr("class", "desactivado");
        $('#volumen-producto').attr("class", "desactivado");
        $('#numero-producto').attr("class", "desactivado");
        $('#paginas-producto').attr("class", "desactivado");
        $('#editorial-producto').attr("class", "desactivado");
        $('#serie-producto').attr("class", "desactivado");
        $('#edicion-producto').attr("class", "desactivado");
        $('#titulo-libro').attr("class", "desactivado");
        $('#crossref-producto').attr("class", "desactivado");
        $('#editor-producto').attr("class", "desactivado");
        $('#institucion-producto').attr("class", "desactivado");
        $('#capitulo-producto').attr("class", "desactivado");
        $('#tipo-producto').attr("class", "desactivado");
        $('#nota-producto').attr("class", "desactivado");
    }

    if (tipo == 4) { // Conferencia
        // Obligatorias
        $('#autor-producto').attr("class", "visible");
        $('#titulo-producto').attr("class", "visible");
        $('#titulo-libro').attr("class", "visible");
        $('#anio-producto').attr("class", "visible");
        $('#autorProducto').prop('required', true);
        $('#tituloProducto').prop('required', true);
        $('#tituloLibro').prop('required', true);
        $('#yearPublication').prop('required', true);
        // Opcionales
        $('#crossref-producto').attr("class", "visible");
        $('#editor-producto').attr("class", "visible");
        $('#volumen-producto').attr("class", "visible");
        $('#serie-producto').attr("class", "visible");
        $('#paginas-producto').attr("class", "visible");
        $('#direccion-producto').attr("class", "visible");
        $('#mes-producto').attr("class", "visible");
        $('#institucion-producto').attr("class", "visible");
        $('#editorial-producto').attr("class", "visible");
        // Desactivadas
        $('#journal-producto').attr("class", "desactivado");
        $('#volumen-producto').attr("class", "desactivado");
        $('#numero-producto').attr("class", "desactivado");
        $('#paginas-producto').attr("class", "desactivado");
        $('#editorial-producto').attr("class", "desactivado");
        $('#serie-producto').attr("class", "desactivado");
        $('#edicion-producto').attr("class", "desactivado");
        $('#how-published-producto').attr("class", "desactivado");
        $('#capitulo-producto').attr("class", "desactivado");
        $('#tipo-producto').attr("class", "desactivado");
        $('#nota-producto').attr("class", "desactivado");
    }

    if (tipo == 5) { // Dentro de un libro
        // Obligatorias
        $('#autor-producto').attr("class", "visible");
        $('#titulo-producto').attr("class", "visible");
        $('#capitulo-producto').attr("class", "visible");
        $('#editorial-producto').attr("class", "visible");
        $('#anio-producto').attr("class", "visible");
        $('#autorProducto').prop('required', true);
        $('#tituloProducto').prop('required', true);
        $('#capitulo').prop('required', true);
        $('#editorial').prop('required', true);
        $('#yearPublication').prop('required', true);
        // Opcionales
        $('#volumen-producto').attr("class", "visible");
        $('#serie-producto').attr("class", "visible");
        $('#tipo-producto').attr("class", "visible");
        $('#direccion-producto').attr("class", "visible");
        $('#edicion-producto').attr("class", "visible");
        $('#mes-producto').attr("class", "visible");
        // Desactivadas
        $('#journal-producto').attr("class", "desactivado");
        $('#numero-producto').attr("class", "desactivado");
        $('#paginas-producto').attr("class", "desactivado");
        $('#how-published-producto').attr("class", "desactivado");
        $('#titulo-libro').attr("class", "desactivado");
        $('#crossref-producto').attr("class", "desactivado");
        $('#editor-producto').attr("class", "desactivado");
        $('#institucion-producto').attr("class", "desactivado");
        $('#nota-producto').attr("class", "desactivado");
    }

    if (tipo == 6) { // En una colección
        // Obligatorias
        $('#autor-producto').attr("class", "visible");
        $('#titulo-producto').attr("class", "visible");
        $('#titulo-libro').attr("class", "visible");
        $('#editorial-producto').attr("class", "visible");
        $('#anio-producto').attr("class", "visible");
        $('#autorProducto').prop('required', true);
        $('#tituloProducto').prop('required', true);
        $('#tituloLibro').prop('required', true);
        $('#editorial').prop('required', true);
        $('#yearPublication').prop('required', true);
        // Opcionales
        $('#crossref-producto').attr("class", "visible");
        $('#editor-producto').attr("class", "visible");
        $('#volumen-producto').attr("class", "visible");
        $('#serie-producto').attr("class", "visible");
        $('#tipo-producto').attr("class", "visible");
        $('#capitulo-producto').attr("class", "visible");
        $('#paginas-producto').attr("class", "visible");
        $('#direccion-producto').attr("class", "visible");
        $('#edicion-producto').attr("class", "visible");
        $('#mes-producto').attr("class", "visible");
        // Desactivadas
        $('#journal-producto').attr("class", "desactivado");
        $('#numero-producto').attr("class", "desactivado");
        $('#how-published-producto').attr("class", "desactivado");
        $('#institucion-producto').attr("class", "desactivado");
        $('#nota-producto').attr("class", "desactivado");
    }

    if (tipo == 7) { // En las actas
        // Obligatorias
        $('#autor-producto').attr("class", "visible");
        $('#titulo-producto').attr("class", "visible");
        $('#titulo-libro').attr("class", "visible");
        $('#anio-producto').attr("class", "visible");
        $('#autorProducto').prop('required', true);
        $('#tituloProducto').prop('required', true);
        $('#tituloLibro').prop('required', true);
        $('#yearPublication').prop('required', true);
        // Opcionales
        $('#crossref-producto').attr("class", "visible");
        $('#editor-producto').attr("class", "visible");
        $('#volumen-producto').attr("class", "visible");
        $('#serie-producto').attr("class", "visible");
        $('#paginas-producto').attr("class", "visible");
        $('#direccion-producto').attr("class", "visible");
        $('#mes-producto').attr("class", "visible");
        $('#institucion-producto').attr("class", "visible");
        $('#editorial-producto').attr("class", "visible");
        // Desactivadas
        $('#journal-producto').attr("class", "desactivado");
        $('#numero-producto').attr("class", "desactivado");
        $('#edicion-producto').attr("class", "desactivado");
        $('#how-published-producto').attr("class", "desactivado");
        $('#crossref-producto').attr("class", "desactivado");
        $('#capitulo-producto').attr("class", "desactivado");
        $('#tipo-producto').attr("class", "desactivado");
        $('#nota-producto').attr("class", "desactivado");
    }

    if (tipo == 8) { // Manual
        // Obligatorias
        $('#titulo-producto').attr("class", "visible");
        $('#tituloProducto').prop('required', true);
        // Opcionales
        $('#autor-producto').attr("class", "visible");
        $('#institucion-producto').attr("class", "visible");
        $('#direccion-producto').attr("class", "visible");
        $('#edicion-producto').attr("class", "visible");
        $('#mes-producto').attr("class", "visible");
        $('#anio-producto').attr("class", "visible");
        // Desactivadas
        $('#journal-producto').attr("class", "desactivado");
        $('#volumen-producto').attr("class", "desactivado");
        $('#numero-producto').attr("class", "desactivado");
        $('#paginas-producto').attr("class", "desactivado");
        $('#editorial-producto').attr("class", "desactivado");
        $('#serie-producto').attr("class", "desactivado");
        $('#how-published-producto').attr("class", "desactivado");
        $('#titulo-libro').attr("class", "desactivado");
        $('#crossref-producto').attr("class", "desactivado");
        $('#editor-producto').attr("class", "desactivado");
        $('#capitulo-producto').attr("class", "desactivado");
        $('#tipo-producto').attr("class", "desactivado");
        $('#nota-producto').attr("class", "desactivado");
    }

    if (tipo == 9 || tipo == 11) { // Proyecto de fin de carrera y tesis doctoral
        // Obligatorias
        $('#autor-producto').attr("class", "visible");
        $('#titulo-producto').attr("class", "visible");
        $('#institucion-producto').attr("class", "visible");
        $('#anio-producto').attr("class", "visible");
        $('#autorProducto').prop('required', true);
        $('#tituloProducto').prop('required', true);
        $('#institucion').prop('required', true);
        $('#yearPublication').prop('required', true);
        // Opcionales
        $('#tipo-producto').attr("class", "visible");
        $('#direccion-producto').attr("class", "visible");
        $('#mes-producto').attr("class", "visible");
        // Desactivadas
        $('#journal-producto').attr("class", "desactivado");
        $('#volumen-producto').attr("class", "desactivado");
        $('#numero-producto').attr("class", "desactivado");
        $('#paginas-producto').attr("class", "desactivado");
        $('#editorial-producto').attr("class", "desactivado");
        $('#serie-producto').attr("class", "desactivado");
        $('#edicion-producto').attr("class", "desactivado");
        $('#how-published-producto').attr("class", "desactivado");
        $('#titulo-libro').attr("class", "desactivado");
        $('#crossref-producto').attr("class", "desactivado");
        $('#editor-producto').attr("class", "desactivado");
        $('#capitulo-producto').attr("class", "desactivado");
        $('#nota-producto').attr("class", "desactivado");
    }

    if (tipo == 10) { // Miscelánea
        // Opcionales
        $('#autor-producto').attr("class", "visible");
        $('#titulo-producto').attr("class", "visible");
        $('#how-published-producto').attr("class", "visible");
        $('#mes-producto').attr("class", "visible");
        $('#anio-producto').attr("class", "visible");
        // Desactivadas
        $('#journal-producto').attr("class", "desactivado");
        $('#volumen-producto').attr("class", "desactivado");
        $('#numero-producto').attr("class", "desactivado");
        $('#paginas-producto').attr("class", "desactivado");
        $('#editorial-producto').attr("class", "desactivado");
        $('#serie-producto').attr("class", "desactivado");
        $('#direccion-producto').attr("class", "desactivado");
        $('#edicion-producto').attr("class", "desactivado");
        $('#titulo-libro').attr("class", "desactivado");
        $('#crossref-producto').attr("class", "desactivado");
        $('#editor-producto').attr("class", "desactivado");
        $('#institucion-producto').attr("class", "desactivado");
        $('#capitulo-producto').attr("class", "desactivado");
        $('#tipo-producto').attr("class", "desactivado");
        $('#nota-producto').attr("class", "desactivado");
    }

    if (tipo == 12) { // Libro de actas
        // Obligatorias
        $('#titulo-producto').attr("class", "visible");
        $('#anio-producto').attr("class", "visible");
        $('#tituloProducto').prop('required', true);
        $('#yearPublication').prop('required', true);
        // Opcionales
        $('#titulo-libro').attr("class", "visible");
        $('#editor-producto').attr("class", "visible");
        $('#volumen-producto').attr("class", "visible");
        $('#serie-producto').attr("class", "visible");
        $('#direccion-producto').attr("class", "visible");
        $('#mes-producto').attr("class", "visible");
        $('#institucion-producto').attr("class", "visible");
        $('#editorial-producto').attr("class", "visible");
        // Desactivadas
        $('#autor-producto').attr("class", "desactivado");
        $('#journal-producto').attr("class", "desactivado");
        $('#numero-producto').attr("class", "desactivado");
        $('#paginas-producto').attr("class", "desactivado");
        $('#edicion-producto').attr("class", "desactivado");
        $('#how-published-producto').attr("class", "desactivado");
        $('#capitulo-producto').attr("class", "desactivado");
        $('#tipo-producto').attr("class", "desactivado");
        $('#nota-producto').attr("class", "desactivado");
    }

    if (tipo == 13) { // Informe técnico
        // Obligatorias
        $('#autor-producto').attr("class", "visible");
        $('#titulo-producto').attr("class", "visible");
        $('#institucion-producto').attr("class", "visible");
        $('#anio-producto').attr("class", "visible");
        $('#autorProducto').prop('required', true);
        $('#tituloProducto').prop('required', true);
        $('#institucion').prop('required', true);
        $('#yearPublication').prop('required', true);
        // Opcionales
        $('#tipo-producto').attr("class", "visible");
        $('#numero-producto').attr("class", "visible");
        $('#direccion-producto').attr("class", "visible");
        $('#mes-producto').attr("class", "visible");
        // Desactivadas
        $('#journal-producto').attr("class", "desactivado");
        $('#volumen-producto').attr("class", "desactivado");
        $('#paginas-producto').attr("class", "desactivado");
        $('#editorial-producto').attr("class", "desactivado");
        $('#serie-producto').attr("class", "desactivado");
        $('#edicion-producto').attr("class", "desactivado");
        $('#how-published-producto').attr("class", "desactivado");
        $('#titulo-libro').attr("class", "desactivado");
        $('#crossref-producto').attr("class", "desactivado");
        $('#editor-producto').attr("class", "desactivado");
        $('#capitulo-producto').attr("class", "desactivado");
        $('#nota-producto').attr("class", "desactivado");
    }

    if (tipo == 14) { // No publicado
        // Obligatorias
        $('#autor-producto').attr("class", "visible");
        $('#titulo-producto').attr("class", "visible");
        $('#nota-producto').attr("class", "visible");
        $('#autorProducto').prop('required', true);
        $('#tituloProducto').prop('required', true);
        $('#nota').prop('required', true);
        // Opcionales
        $('#mes-producto').attr("class", "visible");
        $('#anio-producto').attr("class", "visible");
        // Desactivadas
        $('#journal-producto').attr("class", "desactivado");
        $('#volumen-producto').attr("class", "desactivado");
        $('#numero-producto').attr("class", "desactivado");
        $('#paginas-producto').attr("class", "desactivado");
        $('#editorial-producto').attr("class", "desactivado");
        $('#serie-producto').attr("class", "desactivado");
        $('#direccion-producto').attr("class", "desactivado");
        $('#edicion-producto').attr("class", "desactivado");
        $('#how-published-producto').attr("class", "desactivado");
        $('#titulo-libro').attr("class", "desactivado");
        $('#crossref-producto').attr("class", "desactivado");
        $('#editor-producto').attr("class", "desactivado");
        $('#institucion-producto').attr("class", "desactivado");
        $('#capitulo-producto').attr("class", "desactivado");
        $('#tipo-producto').attr("class", "desactivado");
    }  
}

/* Cargas Académicas - Visualizar periodo */
function editarPeriodo(identificador){
    var aux = identificador.id;
    //alert("Estoy dentro y el id es: " + aux);
    switch(aux){
        case 'primero21':
            $('#periodo-dinamico').text("Enero - Abril 2021 / Grupos");
            break;
        case 'segundo21':
            $('#periodo-dinamico').text("Mayo - Agosto 2021 / Grupos");
            break;
        case 'tercero21':
            $('#periodo-dinamico').text("Septiembre - Diciembre 2021 / Grupos");
            break;
        case 'primero20':
            $('#periodo-dinamico').text("Enero - Abril 2020 / Grupos");
            break;
        case 'segundo20':
            $('#periodo-dinamico').text("Mayo - Agosto 2020 / Grupos");
            break;
        case 'tercero20':
            $('#periodo-dinamico').text("Septiembre - Diciembre 2020 / Grupos");
            break;
        case 'primero19':
            $('#periodo-dinamico').text("Enero - Abril 2019 / Grupos");
            break;
        case 'segundo19':
            $('#periodo-dinamico').text("Mayo - Agosto 2019 / Grupos");
            break;
        case 'tercero19':
            $('#periodo-dinamico').text("Septiembre - Diciembre 2019 / Grupos");
            break;
    }
}

/* RRHH Listado profesor - Agregar nuevo profesor */
function agregarProfesor() {
    location.href='../../v/admin/rrhh_agregar_profesor.html'; 
}

/* Planeaciones Académicas - Agregar div */
let div_id = 1;

function agregar(){
    div_id++;
    var template = '<div class="cuadros" id="contenedor'+div_id+'"><input type="text" placeholder="text"><input type="number" placeholder="5"><input type="text" placeholder="text">';
    
    minusButton = '<button class="btn btn-danger" onclick="eliminar('+div_id+');">-</button></div>';

    var temp = $(template).insertAfter(document.getElementById("formulario-dinamico2"));
    temp.append(minusButton);
    
}

function agregar2(){
    div_id++;
    var template = '<div class="cuadros" id="contenedor'+div_id+'"><input type="text" placeholder="text"><input type="number" placeholder="5"><input type="text" placeholder="text">';
    
    minusButton = '<button class="btn btn-danger" onclick="eliminar('+div_id+');">-</button></div>';

    var temp = $(template).insertAfter(document.getElementById("formulario-dinamico3"));
    temp.append(minusButton);
    
}

function agregar3(){
    div_id++;
    var template = '<div class="cuadros" id="contenedor'+div_id+'"><input type="text" placeholder="text"><input type="text" placeholder="text"><input type="number" placeholder="5"><input type="text" placeholder="text">';
    
    minusButton = '<button class="btn btn-danger" onclick="eliminar('+div_id+');">-</button></div>';

    var temp = $(template).insertAfter(document.getElementById("formulario-dinamico4"));
    temp.append(minusButton);
}

function agregar4(){
    div_id++;
    var template = '<div class="cuadros" id="contenedor'+div_id+'"><input type="text" placeholder="text"><select name="tipo-registro" id="tipoRegistro" required><option value="0">Tipo de producto Académico</option><option value="1">Artículo</option><option value="2">Libro</option><option value="3">Folleto</option><option value="4">Conferencia</option><option value="5">Dentro de un libro</option><option value="6">En una colección</option><option value="7">En las actas</option>'+
    '<option value="8">Manual</option><option value="9">Proyecto de fin de carrera</option><option value="10">Miscelánea</option><option value="11">Tesis doctoral</option><option value="12">Libro de actas</option><option value="13">Informe técnico</option><option value="14">No publicado</option></select><input type="number" placeholder="0"><input type="number" placeholder="0"><input type="date" placeholder="text">';
    
    minusButton = '<button class="btn btn-danger" onclick="eliminar('+div_id+');">-</button></div>';

    var temp = $(template).insertAfter(document.getElementById("formulario-dinamico5"));
    temp.append(minusButton);
}

var aux;

/* Abrir según el periodo */
function abrirformulario(identificador){
    location.href='../../v/admin/edit_planeacion_academica.php';
    aux = identificador.id;
    //cambioPlaneacion(aux);
    //alert(aux);
}

/* Visualizar planeación */
function visualizarPlaneacion(identificador){
    location.href='../../v/admin/visualizar_planeacion.html'; 
    var aux = identificador.id;
    //cambioPlaneacion(aux);
    //alert(aux);
}

function cambioPlaneacion(){
    //alert("estoy dentro " + aux);
    switch(aux){
        case 'primero21':
            $('#planeacion-titulo').text("Planeación Académica Enero - Abril 2021");
            break;
        case 'segundo21':
            $('#planeacion-titulo').text("Mayo - Agosto 2021 / Grupos");
            break;
        case 'tercero21':
            $('#planeacion-titulo').text("Septiembre - Diciembre 2021 / Grupos");
            break;
        case 'primero20':
            $('#planeacion-titulo').text("Enero - Abril 2020 / Grupos");
            break;
        case 'segundo20':
            $('#planeacion-titulo').text("Mayo - Agosto 2020 / Grupos");
            break;
        case 'tercero20':
            $('#planeacion-titulo').text("Septiembre - Diciembre 2020 / Grupos");
            break;
        case 'primero19':
            $('#planeacion-titulo').text("Enero - Abril 2019 / Grupos");
            break;
        case 'segundo19':
            $('#planeacion-titulo').text("Mayo - Agosto 2019 / Grupos");
            break;
        case 'tercero19':
            $('#planeacion-titulo').text("Septiembre - Diciembre 2019 / Grupos");
            break;
        /*default:
            $('#planeacion-titulo').text("Nomás pa' ver si jala");*/
    }
  }

/* Disponibilidad - Selección del recuadro */

function disponibilidad(boton, color) {
    if (boton.style.backgroundColor == "green") {
        boton.style.backgroundColor = color;
    } else {
        boton.style.backgroundColor = "green";
    }
}

/* Productos Académicos - Visualizar producto */

