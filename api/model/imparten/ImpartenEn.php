<?php

namespace dsa\api\model\imparten;

use dsa\api\model\imparten\Exceptions\CarreraNoAgregadaException;
use dsa\api\model\imparten\Exceptions\ParametrosNoValidosException;
use dsa\api\model\imparten\Exceptions\ProfesorNoAgregadoException;
use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\profesor\Exceptions\ProfesorNoExisteException;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\carrera\Carrera;
use dsa\lib\conexionSQL\COperacionesSQL;
use dsa\lib\conexionSQL\Exceptions\CConnexionException;
use dsa\lib\conexionSQL\Exceptions\SQLTransactionException;

class ImpartenEn
{
    private ?array $data;
    private COperacionesSQL $SqlOp;

    /**
     * @param Carrera|null $carrera
     * @param Profesor|null $profesor
     * @param COperacionesSQL|null $cop
     * @throws ParametrosNoValidosException
     * @throws ProfesorException
     */
    private function __construct(?Carrera $carrera=null, ?Profesor $profesor=null, ?COperacionesSQL &$cop=null) {

        $this->SqlOp = COperacionesSQL::getInstance($cop);

        if (is_null($carrera) && is_null($profesor)) {
            throw new ParametrosNoValidosException("Constructur inválido, se debe proveer la carrera o el profesor", 7001);
        }

        if (!is_null($carrera)) {
            $this->data["id_carrera"] = $carrera->get_data("id");
        }

        if (!is_null($profesor)) {
            $this->data["id_profesor"] = $profesor->get_data("id");
        }
    }

    public static function impartenEn_by_carrera(Carrera $carrera, ?COperacionesSQL &$cop=null) : ImpartenEn {
        return new ImpartenEn($carrera, null, $cop);
    }

    public static function impartenEn_by_profesor(Profesor $profesor, ?COperacionesSQL &$cop=null) : ImpartenEn {
        return new ImpartenEn(null, $profesor, $cop);
    }

    /**
     * @param Profesor $profesor
     * @param String|null $fecha_creacion
     * @return bool
     * @throws ProfesorException
     * @throws ProfesorNoAgregadoException
     */
    public function agrega_profesor(Profesor $profesor, ?String $fecha_creacion=null) : bool {
        $id_profesor = $profesor->get_data("id");

        if ($this->profesor_ya_imparte_en_carrera($id_profesor, $this->data["id_carrera"])) {
            throw new ProfesorNoAgregadoException("El profesor ya imparte materias en la carrera", 7016);
        }

        if (!is_null($fecha_creacion)) {
            try {
                $dt_fecha_creacion = new \DateTime($fecha_creacion);
            } catch (\Exception $e) {
                throw new ProfesorNoAgregadoException("La fecha de creación no es válida, verificala", 7003);
            }
        } else {
            $dt_fecha_creacion = new \DateTime("now");
        }



        if (!$this->_agrega_profesor_a_carrera($id_profesor, $dt_fecha_creacion)) {
            throw new ProfesorNoAgregadoException("No fue posible agregar al profesor para impartir en la carrera, vuelve a intentarlo", 7002);
        }

        return true;
    }

    /**
     * Método que elimina la signación del profesor a la carrera
     * @param Profesor $profesor
     * @return bool
     * @throws CarreraNoAgregadaException
     * @throws ProfesorException
     * @throws ProfesorNoAgregadoException
     */
    public function quita_profesor(Profesor $profesor) : bool {
        $id_profesor = $profesor->get_data("id");

        if (!$this->profesor_ya_imparte_en_carrera($id_profesor, $this->data["id_carrera"])) {
            throw new ProfesorNoAgregadoException("El profesor $profesor no imparte materias en la carrera", 7094);
        } elseif ($profesor->get_data("carrera_adscripcion") == $this->data["id_carrera"]) {
            throw new ProfesorException("No es posible eliminar la asignación de la carrera de adscripción al profesor", 7097);
        } else {
            if (!$this->_quitar_asignacion_profesor_carrera($id_profesor, $this->data["id_carrera"])) {
                throw new CarreraNoAgregadaException("No fue posible eliminar la asignación", 7103);
            }
        }

        return true;
    }

    /**
     * Método que quita la asignación de la carrera al profesor
     * @param Carrera $carrera
     * @return bool
     * @throws CarreraNoAgregadaException
     * @throws ProfesorException
     * @throws ProfesorNoAgregadoException
     * @throws ProfesorNoExisteException
     */
    public function quita_carrera(Carrera $carrera) : bool {
        $id_carrera = $carrera->get_data("id");

        if (!$this->profesor_ya_imparte_en_carrera($this->data["id_profesor"], $id_carrera)) {
            throw new ProfesorNoAgregadoException("La carrera no es parte de las asignaciones del profesor", 7116);
        } elseif ($id_carrera == Profesor::get_profesor_by_id($this->data["id_profesor"])->get_data("carrera_adscripcion")) {
            throw new ProfesorException("No es posible eliminar la asignación del profesor a la carrera", 7118);
        } else {
            if (!$this->_quitar_asignacion_profesor_carrera($this->data["id_profesor"], $id_carrera)) {
                throw new CarreraNoAgregadaException("No fue posible eliminar la asignación", 7123);
            }
        }
        return true;
    }

    /**
     * Método que agrega una carrera una carrera en donde el profesor puede impartir materias
     * @param Carrera $carrera
     * @param String|null $fecha_creacion
     * @return bool
     * @throws CarreraNoAgregadaException
     * @throws ProfesorNoAgregadoException
     */
    public function agrega_carrera(Carrera $carrera, ?String $fecha_creacion=null) : bool {
        $id_carrera = $carrera->get_data("id");

        if ($this->profesor_ya_imparte_en_carrera($this->data["id_profesor"], $id_carrera)) {
            throw new CarreraNoAgregadaException("El profesor ya imparte materias en la carrera", 7016);
        }

        if (!is_null($fecha_creacion)) {
            try {
                $dt_fecha_creacion = new \DateTime($fecha_creacion);
            } catch (\Exception $e) {
                throw new ProfesorNoAgregadoException("La fecha de creación no es válida, verificala", 7003);
            }
        } else {
            $dt_fecha_creacion = new \DateTime("now");
        }

        if (!$this->_agrega_carrera_a_profesor($id_carrera, $dt_fecha_creacion)) {
            throw new CarreraNoAgregadaException("No fue posible agregar la carrera al profesor, verifique los datos", 7004);
        }

        return true;

    }

    public function profesor_ya_imparte_en_carrera(int $id_profesor, int $id_carrera) : bool {
        $sqlquery = "SELECT COUNT(*) AS imparte_en FROM imparten WHERE id_profesor = ? AND id_carrera = ?";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "ii", [$id_profesor, $id_carrera])[0]["imparte_en"] > 0;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    /**
     * Método que obtiene la lista de profesores que imparten materias en la Carrera
     * @return array
     * @throws ParametrosNoValidosException
     */
    public function get_profesores() : array {
        if (!isset($this->data["id_carrera"])) {
            throw new ParametrosNoValidosException("No se puede obtener los datos, debe obtener una instancia a partir una una Carrera", 7005);
        }
        return $this->_get_profesores_de_db();
    }

    /**
     * Método que obtiene las carreras en donde el profesor imparte materias
     * @return array
     * @throws ParametrosNoValidosException
     */
    public function get_carreras() : array {
        if (!isset($this->data["id_profesor"])) {
            throw new ParametrosNoValidosException("No se puede obtener los datos, debe obtener una instancia a partir de un profesor", 7006);
        }

        return $this->_get_carreras_de_db();
    }

    private function _agrega_profesor_a_carrera(int $id_profesor, \DateTime $fecha) : bool {
        $ban = false;
        $sqlquery = "INSERT INTO imparten (id_carrera, id_profesor, fecha_creacion) VALUES (?, ?, ?)";

        try {
            $ban = ($this->SqlOp->exec($sqlquery, "iis", [$this->data["id_carrera"], $id_profesor, $fecha->format("Y-m-d H:i:s")]) == 1);
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    /**
     * Método que elimina la asignación de un CProfesor que imparte en una Carrera
     * @param int $id_profesor identificador único del profesor
     * @return bool
     */
    private function _quitar_asignacion_profesor_carrera(int $id_profesor, int $id_carrera) : bool {
        $ban = false;
        $sqlquery = "DELETE FROM imparten WHERE id_profesor = ? AND id_carrera = ?";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "ii", [$id_profesor, $id_carrera]) == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    private function _agrega_carrera_a_profesor(int $id_carrera, \DateTime $fecha) : bool {
        $ban = false;
        $sqlquery = "INSERT INTO imparten (id_carrera, id_profesor, fecha_creacion) VALUES (?, ?, ?)";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "iis", [$id_carrera, $this->data["id_profesor"], $fecha->format("Y-m-d H:i:s")]) == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    /**
     * Método que obtiene los ids de los profesores que imparten materias en la carrera
     * @return array
     */
    private function _get_profesores_de_db() : array {
        $id_profs = array();
        $sqlquery = "SELECT imparten.id_profesor as id FROM imparten WHERE id_carrera = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "i", [$this->data["id_carrera"]]);

            foreach ($res as $item) {
                $id_profs[] = $item["id"];
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $id_profs = array();
        }

        return $id_profs;
    }

    /**
     * Método que obtiene los ids de las carreras en las que imparte el profesor
     * @return array
     */
    private function _get_carreras_de_db() : array {
        $id_carreras = array();
        $sqlquery = "SELECT imparten.id_carrera as id FROM imparten WHERE id_profesor = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "i", [$this->data["id_profesor"]]);

            foreach ($res as $item) {
                $id_carreras[] = $item["id"];
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $id_carreras = array();
        }

        return $id_carreras;
    }
}