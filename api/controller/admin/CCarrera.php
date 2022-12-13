<?php

namespace dsa\api\controller\admin;

use dsa\api\model\carrera\Carrera;
use dsa\api\model\carrera\Exceptions\CarreraException;
use dsa\api\model\carrera\Exceptions\CarreraNoExistenteException;
use dsa\api\model\carrera\Exceptions\UsuarioDirectorYaAsignadoACarreraException;
use dsa\api\model\imparten\Exceptions\CarreraNoAgregadaException;
use dsa\api\model\imparten\Exceptions\ProfesorNoAgregadoException;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\usuario\Exceptions\UsuarioException;
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;
use dsa\api\model\usuario\Exceptions\UsuarioNoHabilitadoComoDirectorException;
use dsa\api\model\usuario\Usuario;

class CCarrera extends Admin
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param Usuario $director
     * @param String $nombre
     * @param String $clave
     * @param String $nivel
     * @return Carrera|null
     * @throws CarreraException
     * @throws CarreraNoExistenteException
     * @throws UsuarioDirectorYaAsignadoACarreraException
     * @throws UsuarioNoHabilitadoComoDirectorException
     */
    public function crea_nueva_carrera(Usuario $director, String $nombre, String $clave, String $nivel="Ing"): ?Carrera
    {
        return Carrera::crear_nueva_carrera($director, $nombre, $clave, $nivel, $this->Msql);
    }

    /**
     * @param Usuario $nuevo_director
     * @param Carrera $carrera
     * @return bool
     * @throws CarreraException
     * @throws UsuarioDirectorYaAsignadoACarreraException
     * @throws UsuarioException
     * @throws UsuarioNoExistenteException
     */
    public function actualiza_director_de_carrera(Usuario $nuevo_director, Carrera $carrera) : bool {
        $ban = false;
        try {
            $tmpCarrera = Carrera::get_carrera_by_director($nuevo_director);
            unset($tmpCarrera);
            throw new UsuarioDirectorYaAsignadoACarreraException("El usuario $nuevo_director ya esta asignado como director a otra carrera", 12005);
        } catch (CarreraNoExistenteException $e) {
            $tmpDirector = Usuario::get_usuario_by_id($nuevo_director->get_data("id"), $this->Msql);
            if (!$tmpDirector->es_director()) {
                $newData = ["tipo" => "director"];
                $tmpDirector->actualiza_datos_de_usuario($newData);
            }

            try {
                $newData = array("id_director" => $tmpDirector->get_data("id"));
                $ban = $carrera->actualiza_datos_de_carrera($newData);
            } catch (UsuarioNoHabilitadoComoDirectorException $e) { }
        }
        return $ban;
    }

    /**
     * @param Carrera $carrera
     * @param array $new_data
     * @return bool
     * @throws CarreraException
     * @throws UsuarioNoHabilitadoComoDirectorException
     */
    public function actualiza_datos(Carrera $carrera, array $new_data) : bool {
        return $carrera->actualiza_datos_de_carrera($new_data);
    }

    /**
     * @param Carrera $carrera
     * @param Profesor $profesor
     * @return bool
     * @throws CarreraException
     * @throws CarreraNoExistenteException
     * @throws ProfesorNoAgregadoException
     * @throws ProfesorException
     */
    public function agrega_profesor_para_impartir(Carrera $carrera, Profesor $profesor) : bool {
        $tmpCarrera = Carrera::get_carrera_by_id($carrera->get_data("id"), $this->Msql);
        return $tmpCarrera->agrega_profesor_para_impartir($profesor);
    }

    /**
     * @param Carrera $carrera
     * @param Profesor $profesor
     * @return bool
     * @throws CarreraException
     * @throws CarreraNoExistenteException
     * @throws ProfesorException
     * @throws ProfesorNoAgregadoException
     * @throws CarreraNoAgregadaException
     */
    public function quita_profesor_de_imparticion(Carrera $carrera, Profesor $profesor) : bool {
        $tmpCarrera = Carrera::get_carrera_by_id($carrera->get_data("id"), $this->Msql);
        return $tmpCarrera->quita_profesor_de_imparticion($profesor);
    }
}