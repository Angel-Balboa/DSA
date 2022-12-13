var initd = function () {
    view_pass();
    submit_login();
};

var submit_login = function () {
    $("form#frmLogin").submit(function (e) {
        e.preventDefault();
        var post_url = "api/requests/sesion/inicia_sesion.php";
        var submitData = $(this).serialize();
        $.ajax({
            url : post_url,
            type: 'POST',
            data : submitData,
            beforeSend: function (xhr) {
                Notiflix.Loading.standard("Cargando");
            }}).done(function (data) {

                console.log(data);
                if(!data.exito){
                    Notiflix.Report.warning("Advertencia", data.respuesta_error.mensaje_error, "Cerrar");
                }else{
                    window.location.href = data.respuesta_exito.datos.url;
                } 
        }).fail(function () {
            Notiflix.Notify.Failure('Error en la peticion: ' + post_url);
        }).always(function () {
            Notiflix.Loading.remove();
        });
    });
}

var view_pass = function() {

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

};