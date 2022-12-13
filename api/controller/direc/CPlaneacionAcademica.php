<?php

namespace dsa\api\controller\direc;


use dsa\api\model\planeacion_academica\Exceptions\DatosDePlaneacionAcademicaIncorrectosException;
use dsa\api\model\planeacion_academica\Exceptions\PlaneacionAcademicaException;
use dsa\api\model\planeacion_academica\Exceptions\PlaneacionAcademicaNoExistenteException;
use dsa\api\model\profesor\Exceptions\LlaveDeBusquedaIncorrectaException;
use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\profesor\Exceptions\ProfesorNoExisteException;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\planeacion_academica\PlaneacionAcademica;
use dsa\api\model\carrera;

class CPlaneacionAcademica extends Director
{
    public function __construct(Usuario $usuario)
    {
        parent::__construct($usuario);
    }

    /**
     * @param Profesor $profesor
     * @param int $periodo
     * @param int $anio
     * @return PlaneacionAcademica|null
     * @throws DatosDePlaneacionAcademicaIncorrectosException
     * @throws PlaneacionAcademicaException
     * @throws ProfesorException
     */
    public function solicitar_planeacion_academica(Profesor $profesor, int $periodo, int $anio) {
        return PlaneacionAcademica::crea_nueva_PlaneacionAcademica($profesor, $periodo, $anio, $this->Msql);
    }

    /**
     * @param int $periodo
     * @param int $anio
     * @return void
     * @throws DatosDePlaneacionAcademicaIncorrectosException
     * @throws PlaneacionAcademicaException
     * @throws ProfesorException
     * @throws LlaveDeBusquedaIncorrectaException
     * @throws ProfesorNoExisteException
     */
    public function solicitar_planeacion_academica_PTCs(int $periodo, int $anio) {
        $PTCs = Profesor::get_all(["carrera_adscripcion" => $this->carrera, "tipo_contrato" => "P.T.C"]);

        foreach ($PTCs as $PTC) {
            $tmpProfesor = Profesor::get_profesor_by_id($PTC["id"]);

            PlaneacionAcademica::crea_nueva_PlaneacionAcademica($tmpProfesor, $periodo, $anio, $this->Msql);
        }
    }

    /**
     * @param PlaneacionAcademica $planeacionAcademica
     * @return bool
     * @throws PlaneacionAcademicaException
     * @throws PlaneacionAcademicaNoExistenteException
     */
    public function aceptar_planeacion(PlaneacionAcademica $planeacionAcademica) {

        if ($planeacionAcademica->get_data("estado") == "finalizada") {
            $newData = array("estado" => "aceptada");
            $tmpPlaneacion = PlaneacionAcademica::get_PlaneacionAcademica_by_id($planeacionAcademica->get_data("id"), $this->Msql);
            return $tmpPlaneacion->actualiza_datos($newData);
        } else {
            throw new PlaneacionAcademicaException("La planeación Académica no pueder ser aceptada, no ha sido finalizada", -8000);
        }

    }
}