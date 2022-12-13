var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
    return false;
};

var post_data = function post_data(url_target, serialized_data, report=false) {
  var result = null;
  var obj_result = {done: false, message: "", data:null};
  $.ajax({
      url: url_target,
      type: 'post',
      data: serialized_data,
      dataType: 'json',
      async: false,
      cache: false,
      success: function (data) {
          result = data;
      }
  }).fail(function () {
      Notiflix.Report.failure('Error', 'No se logrado establecer comunicación con el servidor');
  });

  if (!result.exito) {
      if (report) {
          Notiflix.Report.failure("Error: " + result.respuesta_error.codigo_error, result.respuesta_error.mensaje_error, 'Aceptar');
      } else {
          Notiflix.Notify.failure(result.respuesta_error.mensaje_error);
      }
  } else {
      obj_result.done = true;
      obj_result.message = result.respuesta_exito.mensaje_exito;
      obj_result.data = result.respuesta_exito.datos;
  }

  return obj_result;
};

var get_data = function get_data(url_target, report=false) {
    var result = null;
    var obj_result = {done: false, data:null};
    $.ajax({
        url: url_target,
        type: 'get',
        dataType: 'json',
        async: false,
        cache: false,
        success: function (data) {
            result = data;
        }
    }).fail(function() {
        Notiflix.Report.failure('Error', 'No se logrado establecer comunicación con el servidor');
    });

    if (!result.exito) {
        if (report) {
            Notiflix.Report.failure("Error: " + result.respuesta_error.codigo_error, result.respuesta_error.mensaje_error, 'Aceptar');
        } else {
            Notiflix.Notify.failure(result.respuesta_error.mensaje_error);
        }
    } else {
        obj_result.done = true;
        obj_result.data = result.respuesta_exito.datos;
        obj_result.msj = result.respuesta_exito.mensaje_exito;
    }

    return obj_result;
};

var get_data_raw = function get_data_raw(url_target) {
  var result = null;

    $.ajax({
        url: url_target,
        type: 'get',
        dataType: 'json',
        async: false,
        cache: false,
        success: function (data) {
            result = data;
        }
    }).fail(function() {
        Notiflix.Report.failure('Error', 'No se logrado establecer comunicación con el servidor');
    });

    return result;
};

var ssg = function ssg(var_name, json_parsed=true) {
  var value = null;
  if (sessionStorage.getItem(var_name) !== null) {
      if (json_parsed) {
          value = JSON.parse(sessionStorage.getItem(var_name));
      } else {
          value = sessionStorage.getItem(var_name);
      }
  }
  return value;
};

var sss = function sss(var_name, value, json_parsed=true) {
    if (json_parsed) {
        sessionStorage.setItem(var_name, JSON.stringify(value));
    } else {
        sessionStorage.setItem(var_name, value);
    }
};

var nfy = function (message, type='e') {
  if (type === 's') {
      Notiflix.Notify.success(message);
  } else if (type === 'w') {
      Notiflix.Notify.warning(message);
  } else if (type === 'i') {
      Notiflix.Notify.info(message);
  } else {
      Notiflix.Notify.failure(message);
  }
};

var rep = function rep(title, message, type='e', btn_msg='Aceptar') {
  if (type === 's') {
      Notiflix.Report.success(title, message, btn_msg);
  } else if (type === 'w') {
      Notiflix.Report.warning(title, message, btn_msg);
  } else if (type === "i") {
      Notiflix.Report.info(title, message, btn_msg);
  } else {
      Notiflix.Report.failure(title, message, btn_msg);
  }
};

var get_txt_periodo = function (int_periodo, short=false) {
    var txtPeriodo = null;

    switch (int_periodo) {
        case 1:
            txtPeriodo = short ? "Ene-Abr" : "Enero - Abril";
            break;
        case 2:
            txtPeriodo = short ? "May-Ago" : "Mayo - Agosto";
            break;
        case 3:
            txtPeriodo = short ? "Sep-Dic" : "Septiembre - Diciembre";
            break;
        default:
            rep("Error", "El periodo no es válido, verifica", 'e', "Cerrar");
            break;
    }
    return txtPeriodo;
};

var get_txt_cuatrimestre = function (int_cuatrimestre, short=false) {
    var txtCuatrimestre = "";

    switch (parseInt(int_cuatrimestre)) {
        case 1:
            txtCuatrimestre = short ? "1er" : "Primer";
            break;
        case 2:
            txtCuatrimestre = short ? "2do" : "Segundo";
            break;
        case 3:
            txtCuatrimestre = short ? "3er" : "Tercer";
            break;
        case 4:
            txtCuatrimestre = short ? "4to" : "Cuarto";
            break;
        case 5:
            txtCuatrimestre = short ? "5to" : "Quinto";
            break;
        case 6:
            txtCuatrimestre = short ? "6to" : "Sexto";
            break;
        case 7:
            txtCuatrimestre = short ? "7mo" : "Septimo";
            break;
        case 8:
            txtCuatrimestre = short ? "8vo" : "Octavo";
            break;
        case 9:
            txtCuatrimestre = short ? "9no" : "Noveno";
            break;
        case 10:
            txtCuatrimestre = short ? "10mo" : "Décimo";
            break;
    }

    return txtCuatrimestre;
};

function get_object_from_serializedArray(serialized_array_data) {
    var data = {};
    serialized_array_data.map(function(x) {data[x.name] = x.value; });

    return data;
}

sessionStorage.clear();