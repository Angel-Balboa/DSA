(function($) {

	"use strict";

	$(".toggle-password").click(function() {

  $(this).toggleClass("fa-eye fa-eye-slash");
  var input = $($(this).attr("toggle"));
  if (input.attr("type") == "password") {
    input.attr("type", "text");
  } else {
    input.attr("type", "password");
  }
});

})(jQuery);

/* GESTIÓN ACADÉMICA */
let row_id_gestion = 1;

/* METODO PARA AÑADIR UNA NUEVA CELDA PARA LA GESTIÓN ACADÉMICA*/
function add_field_gestion(){
  row_id_gestion++;
  var template = '<div id="'+row_id_gestion+'-gestion" class="row"><div class="col-lg-4"><input type="text" class="form-control"></div><div class="col-lg-3"><select class="custom-select"><option value="1" selected>1</option><option value="5">5</option><option value="10">10</option><option value="15">15</option><option value="20">20</option></select></div><div class="col-lg-4"><input type="text" class="form-control"></div></div>';
  minusButton = '<span onclick="remove_field_gestion('+row_id_gestion+')" class="btn btn-danger glyphicon glyphicon-remove delete-field-capacitacion">-</span>';

  var temp = $(template).insertBefore(document.getElementById("help-block"));
  temp.append(minusButton);
}

/* METODO PARA REMOVER CELDAS DE GESTIÓN ACADÉMICA*/
function remove_field_gestion(id){
  let body = document.getElementById("gestion-academica")
  let row = document.getElementById(id+"-gestion");
  body.removeChild(row);
}


/* CAPACITACIÓN Y DESARROLLO PERSONAL */
let row_id_capacitacion = 1;

/* METODO PARA AÑADIR UNA NUEVA CELDA PARA LA CAPACITACIÓN Y DESARROLLO PERSONAL */
function add_field_capacitacion(){
  row_id_capacitacion++;
  var template = '<div id="'+row_id_capacitacion+'-capacitacion" class="row"><div class="col-lg-4"><input type="text" class="form-control"></div> <div class="col-lg-3"><select class="custom-select"><option value="1" selected>1</option><option value="5">5</option><option value="10">10</option><option value="15">15</option><option value="20">20</option></select></div><div class="col-lg-4"><select class="custom-select"><option value="constancia">Constancia de Participación</option><option value="diploma">Diploma</option></select></div></div>' ;
  minusButton = '<span onclick="remove_field_capacitacion('+row_id_capacitacion+')" class="btn btn-danger glyphicon glyphicon-remove">-</span>';

  var temp = $(template).insertBefore(document.getElementById("help-block-capacitacion"));
  temp.append(minusButton);
}

/* METODO PARA REMOVER CELDAS DE CAPACITACIÓN Y DESARROLLO PERSONAL */

function remove_field_capacitacion(id){
  let body = document.getElementById("capacitacion")
  let row = document.getElementById(id+"-capacitacion");
  body.removeChild(row);
}


/* VINCULACIÓN */
let row_id_vinculacion = 1;

/* METODO PARA AÑADIR UNA NUEVA CELDA PARA VINCULACIÓN */

function add_field_vinculacion(){
  row_id_vinculacion++;
  var template = '<div id="'+row_id_vinculacion+'-vinculacion" class="row"> <div class="col-lg-3"><input type="text" class="form-control"></div><div class="col-lg-3"><input type="text" class="form-control"></div><div class="col-lg-2"><select class="custom-select"><option value="5">5</option><option value="5">10</option><option value="5">15</option><option value="5">20</option></select></div><div class="col-lg-3"><select class="custom-select"><option values="">Publicaciones sobre la actividad</option><option value="2">Informe</option></select></div></div>'
  minusButton = '<span onclick="remove_field_vinculacion('+row_id_vinculacion+')" class="btn btn-danger glyphicon glyphicon-remove">-</span>';

  var temp = $(template).insertBefore(document.getElementById("help-block-vinculacion"));
  temp.append(minusButton);
}

/* METODO PARA REMOVER CELDAS DE VINCULACIÓN */
function remove_field_vinculacion(id){
  let body = document.getElementById("vincu")
  let row = document.getElementById(id+"-vinculacion");
  body.removeChild(row);
}


/* INVESTIGACIÓN */
let row_id_investigacion = 1;

/* METODO PARA AÑADIR UNA NUEVA CELDA PARA INVESTIGACION */
function add_field_investigacion(){
  row_id_investigacion++;
  var template = '<div id="'+row_id_investigacion+'-investigacion" class="row"> <div class="col-lg-3"><input type="text" class="form-control"></div><div class="col-lg-2"><select class="custom-select"><option value="1">Artículo</option><option value="2">Tesis Maestría</option><option value="3">Patente</option><option value="4">Prototipo</option></select></div><div class="col-lg-2"><select class="custom-select"><option value="0">0</option><option value="0">10</option><option value="0">20</option><option value="0">30</option><option value="0">40</option><option value="0">50</option><option value="0">60</option><option value="0">70</option><option value="0">80</option><option value="0">90</option><option value="0">100</option></select></div><div class="col-lg-2"><select class="custom-select"><option value="0">0</option><option value="0">10</option><option value="0">20</option><option value="0">30</option><option value="0">40</option><option value="0">50</option><option value="0">60</option><option value="0">70</option><option value="0">80</option><option value="0">90</option><option value="0">100</option></select></div><div class="col-lg-2"><input type="date" class="form-control"></div></div>';
  minusButton = '<span onclick="remove_field_investigacion('+row_id_investigacion+')" class="btn btn-danger glyphicon glyphicon-remove">-</span>';

  var temp = $(template).insertBefore(document.getElementById("help-block-investigacion"));
  temp.append(minusButton);
}

/* METODO PARA REMOVER CELDA DE INVESTIGACION */
function remove_field_investigacion(id){
  let body = document.getElementById("investigation")
  let row = document.getElementById(id+"-investigacion");
  body.removeChild(row);
}