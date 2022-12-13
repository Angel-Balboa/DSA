<?php

namespace dsa\api\controller\profesor;

use dsa\api\model\actividad_investigacion\ActividadInvestigacion;
use dsa\api\model\actividad_academica\Actividad;
use dsa\api\model\actividad_academica\Exceptions\ActividadAcademicaException;
use dsa\api\model\actividad_academica\Exceptions\ActividadAcademicaNoExistenteException;
use dsa\api\model\planeacion_academica\Exceptions\PlaneacionAcademicaException;
use dsa\api\model\planeacion_academica\Exceptions\PlaneacionAcademicaNoExistenteException;
use dsa\api\model\planeacion_academica\PlaneacionAcademica;
use dsa\api\model\planeacion_asesoria\Exceptions\PlaneacionAsesoriaException;
use dsa\api\model\planeacion_asesoria\Exceptions\PlaneacionAsesoriaNoExisteException;
use dsa\api\model\planeacion_asesoria\PlaneacionAsesoria;
use dsa\api\model\profesor\Profesor;

class CActividadAcademica extends CProfesor
{
    public function __construct(Profesor $profesor) {
        parent::__construct($profesor);
    }

    /**
     * @param PlaneacionAcademica $planeacionAcademica
     * @param String $descripcion
     * @param int $horas
     * @param String $evidencia
     * @param String $tipoActividad
     * @param String|null $empresaReceptora
     * @return Actividad|null
     * @throws ActividadAcademicaException
     * @throws ActividadAcademicaNoExistenteException
     * @throws PlaneacionAcademicaException
     */
    public function crea_actividad(PlaneacionAcademica $planeacionAcademica, String $descripcion, int $horas, String $evidencia, String $tipoActividad="GESTION", ?String $empresaReceptora=null) : ?Actividad {
        switch ($tipoActividad) {
            case "GESTION":
            case "CAPACITACION":
                return Actividad::crea_actividad_academica($planeacionAcademica, $tipoActividad, $descripcion, "Universidad Politécnica de Victoria", $horas, $evidencia, $this->Msql);
                break;
            case "VINCULACION":
                return Actividad::crea_actividad_academica($planeacionAcademica, $tipoActividad, $descripcion, $empresaReceptora, $horas, $evidencia, $this->Msql);
                break;
            case "PROMOCION":
                return Actividad::crea_actividad_academica($planeacionAcademica, $tipoActividad, "PROMOCION", "Universidad Politécnica de Victoria", $horas, "Oficio de Comisión", $this->Msql);
                break;
            default:
                throw new ActividadAcademicaException("El tipo de actividad no es válido, verifica la documentación", -3000);
        }
    }

    /**
     * @param Actividad $actividad
     * @return bool
     * @throws ActividadAcademicaException
     */
    public function elimina_actividad(Actividad $actividad) : bool {
        $tmpActividad = Actividad::get_actividad_academica_by_id($actividad->get_data("id"), $this->Msql);
        return $tmpActividad->elimina_actividad();
    }

    /**
     * @param Actividad $actividad
     * @param array $newData
     * @return bool
     * @throws ActividadAcademicaException
     */
    public function actualiza_actividad(Actividad $actividad, array $newData) : bool {
        $tmpActividad = Actividad::get_actividad_academica_by_id($actividad->get_data("id"), $this->Msql);
        return $tmpActividad->actializa_datos($newData);
    }

    /**
     * @param PlaneacionAcademica $planeacionAcademica
     * @param int $institucionalEstancia
     * @param int $institucionalEstadia
     * @param int $empresarialEstancia
     * @param int $empresarialEstadia
     * @return PlaneacionAsesoria|null
     * @throws PlaneacionAcademicaException
     * @throws PlaneacionAsesoriaException
     * @throws PlaneacionAsesoriaNoExisteException
     */
    public function crea_planeacion_asesorias (PlaneacionAcademica $planeacionAcademica, int $institucionalEstancia, int $institucionalEstadia, int $empresarialEstancia, int $empresarialEstadia) {
        return PlaneacionAsesoria::crea_planeacion_asesoria($planeacionAcademica, $institucionalEstancia, $institucionalEstadia, $empresarialEstancia, $empresarialEstadia, $this->Msql);
    }

    /**
     * @param PlaneacionAsesoria $planeacionAsesoria
     * @param int $institucionalEstancia
     * @param int $institucionalEstadia
     * @param int $empresarialEstancia
     * @param int $empresarialEstadia
     * @return bool
     * @throws PlaneacionAsesoriaException
     * @throws PlaneacionAsesoriaNoExisteException
     */
    public function actualiza_planeacion_asesorias(PlaneacionAsesoria $planeacionAsesoria, int $institucionalEstancia, int $institucionalEstadia, int $empresarialEstancia, int $empresarialEstadia) {
        $tmpPlaneacionAsesoria = PlaneacionAsesoria::get_planeacionAsesoria_by_id($planeacionAsesoria->get_data("id"), $this->Msql);

        $newData = array("institucional_estancia" => $institucionalEstancia, "institucional_estadia" => $institucionalEstadia, "empresarial_estancia" => $empresarialEstancia, "empresarial_estadia" => $empresarialEstadia);

        return $tmpPlaneacionAsesoria->actualiza_datos($newData);
    }

    public function crea_actividad_investigacion(PlaneacionAcademica $planeacionAcademica, String $actividad, String $tipo, int $avance_actual, int $avance_esperado, \DateTime $fecha_termino) {
        return ActividadInvestigacion::crea_actividadInvestigacion($planeacionAcademica, $actividad, $tipo, $avance_actual, $avance_esperado, $fecha_termino, $this->Msql);
    }

    public function actualiza_actividad_investigacion(ActividadInvestigacion $actividadInvestigacion, array $newData) {
        $tmpActividadInvestigacion = ActividadInvestigacion::get_actividadInvestigacion_by_id($actividadInvestigacion->get_data("id"), $this->Msql);

        return $tmpActividadInvestigacion->actualiza_datos($newData);
    }

    public function elimina_actividad_investigacion(ActividadInvestigacion $actividadInvestigacion) {
        $tmpActividad = ActividadInvestigacion::get_actividadInvestigacion_by_id($actividadInvestigacion->get_data("id"), $this->Msql);
        return $tmpActividad->elimina_actividad();
    }

    /**
     * @param PlaneacionAcademica $planeacionAcademica
     * @return bool
     * @throws PlaneacionAcademicaException
     * @throws PlaneacionAcademicaNoExistenteException
     */
    public function finaliza_planeacion_academica(PlaneacionAcademica $planeacionAcademica) {
        $tmpPlaneacion = PlaneacionAcademica::get_PlaneacionAcademica_by_id($planeacionAcademica->get_data("id"), $this->Msql);
        $newData = array("estado" => "finalizada");
        return $tmpPlaneacion->actualiza_datos($newData);
    }


}