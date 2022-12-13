/*
 =========================================================
 * Cargar Imagen 
 =========================================================
 */

var botonPerfil = document.getElementById('boton-perfil');
var path = "../assets/img/faces/marc.jpg";

//botonPerfil.addEventListener("click", cambiarImagen, false);

function cambiarImagen() {
    document.getElementById('getImagen').click();
    /*document.getElementById("foto-perfil").src = ("C:/Users/USUARIO_01/Downloads/PAISAJES/AMARILLO/" + path);*/
}

function getPath(archivo) {
    path = archivo;
}

