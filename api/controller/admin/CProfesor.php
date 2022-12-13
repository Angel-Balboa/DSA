<?php

namespace dsa\api\controller\admin;

use dsa\api\model\carrera\Carrera;
use dsa\api\model\carrera\Exceptions\CarreraException;
use dsa\api\model\carrera\Exceptions\CarreraNoExistenteException;
use dsa\api\model\imparten\Exceptions\CarreraNoAgregadaException;
use dsa\api\model\imparten\Exceptions\ParametrosNoValidosException;
use dsa\api\model\imparten\Exceptions\ProfesorNoAgregadoException;
use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\profesor\Exceptions\ProfesorNoExisteException;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\usuario\Usuario;
use dsa\lib\Utils\DataChecker;

class CProfesor extends Admin
{

    public function __construct()
    {
        parent::__construct();
    }

    public function crea_perfil_profesor_a_usuario(Usuario $usuario, Carrera $carrera_adcripcion, String $nivel_adscripcion="Ing.", String $tipo_contrato="P.A", String $categoria='A', String $inicio_contrato="now", ?String $fin_contrato=null) {
        return Profesor::crear_nuevo_profesor($usuario, $carrera_adcripcion, $nivel_adscripcion, $tipo_contrato, $categoria, $inicio_contrato, $fin_contrato, $this->Msql);
    }

    /**
     * Método que asigna la lista de carreras al profesor en donde imparte materias
     * @param Profesor $profesor instancia de la clase CProfesor a quien se le asignarán las materias
     * @param array $lista_carreras // array de Carrera de las carreras a asignar al profesor
     * @return bool
     * @throws CarreraException
     * @throws ProfesorException
     * @throws CarreraNoAgregadaException
     * @throws ProfesorNoAgregadoException
     * @throws ProfesorNoExisteException
     */
    public function agrega_carreras_para_impartir(Profesor $profesor, array $lista_carreras) : bool {
        $id_carreraAscripcion_profesor = $profesor->get_data("carrera_adscripcion");

        foreach ($lista_carreras as $carrera) {
            if (!DataChecker::check_instance_of($carrera, "Carrera")) {
                throw new CarreraException("Se esperaba un objeto carrera", 17022);
            }
        }

        $tmpProfesor = Profesor::get_profesor_by_id($profesor->get_data("id"), $this->Msql);

        foreach ($lista_carreras as $carrera) {
            if ($id_carreraAscripcion_profesor != $carrera->get_data("id")) {
                if (!$tmpProfesor->agrega_carrera_para_impartir($carrera)) {
                    throw new ProfesorException("No fue posible asigna la carrera $carrera al profesor");
                }
            }
        }
        return true;
    }

    public function quita_carreras_para_impartir(Profesor $profesor, array $lista_carreras) : bool {
        $id_carreraAdscripcion_profesor = $profesor->get_data("carrera_adscripcion");

        foreach ($lista_carreras as $carrera) {
            if (!DataChecker::check_instance_of($carrera, "Carrera")) {
                throw new CarreraException("Sólo se admiten instancias de la clase Carrera", 17066);
            }
        }

        $tmpProfesor = Profesor::get_profesor_by_id($profesor->get_data("id"), $this->Msql);

        foreach ($lista_carreras as $carrera) {
            if ($id_carreraAdscripcion_profesor != $carrera->get_data("id")) {
                if (!$tmpProfesor->quita_carrera_para_impartir($carrera)) {
                    throw new ProfesorException("No fue posible quitar la carrera $carrera al profesor");
                }
            }
        }
        return true;
    }

    /**
     * @param Profesor $profesor
     * @param Carrera $carrera
     * @return bool
     * @throws CarreraNoAgregadaException
     * @throws ProfesorException
     * @throws ProfesorNoAgregadoException
     * @throws ProfesorNoExisteException
     */
    public function quitar_asignacion_de_materia_a_profesor(Profesor $profesor, Carrera $carrera): bool
    {
        $tmpProfesor = Profesor::get_profesor_by_id($profesor->get_data("id"), $this->Msql);
        return $tmpProfesor->quita_carrera_para_impartir($carrera);
    }

    /**
     * @param Profesor $profesor
     * @param array $newData
     * @return bool
     * @throws CarreraNoAgregadaException
     * @throws ProfesorException
     * @throws ProfesorNoAgregadoException
     * @throws ProfesorNoExisteException
     * @throws CarreraNoExistenteException
     * @throws ParametrosNoValidosException
     */
    public function actualiza_datos(Profesor $profesor, array $newData) : bool {
        $tmpProfesor = Profesor::get_profesor_by_id($profesor->get_data("id"), $this->Msql);
        $oldCarreraAdscripcion = $tmpProfesor->get_data("carrera_adscripcion");
        if ($tmpProfesor->actualiza_datos_de_profesor($newData)) {
            $newCarreraAdscripcion = $tmpProfesor->get_data("carrera_adscripcion");

            if ($oldCarreraAdscripcion == $newCarreraAdscripcion) { // si es la misma carrera
                return true;
            } else { // si cambio de carrera de adscripción
                $profImparteEn = $tmpProfesor->get_carreras_de_imparticion(); // carreras donde actualmente imparte materias

                if (!in_array($newCarreraAdscripcion, $profImparteEn)) { // si no imparte
                    $tmpCarrera = Carrera::get_carrera_by_id($newCarreraAdscripcion);
                    return $tmpProfesor->agrega_carrera_para_impartir($tmpCarrera);
                } else {
                    return true;
                }
            }
        }
        return false;
    }

}