<?php

namespace dsa\api\model\prestamo;


use DateTime;
use dsa\api\model\carrera\Carrera;
use dsa\api\model\carrera\Exceptions\CarreraException;
use dsa\api\model\carrera\Exceptions\CarreraNoExistenteException;
use dsa\api\model\imparten\Exceptions\ProfesorNoAgregadoException;
use dsa\api\model\imparten\ImpartenEn;
use dsa\api\model\prestamo\Exceptions\SolicitudNoEncontradaException;
use dsa\api\model\prestamo\Exceptions\SolicitudPrestamoException;
use dsa\api\model\prestamo\Exceptions\SolicitudPreviamenteAceptadaException;
use dsa\api\model\profesor\Exceptions\LlaveDeBusquedaIncorrectaException;
use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\profesor\Exceptions\ProfesorNoExisteException;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\usuario\Exceptions\UsuarioException;
use dsa\api\model\usuario\Exceptions\UsuarioNoExistenteException;
use dsa\api\model\usuario\Usuario;
use dsa\lib\conexionSQL\COperacionesSQL;
use dsa\lib\conexionSQL\Exceptions\CConnexionException;
use dsa\lib\conexionSQL\Exceptions\SQLTransactionException;
use dsa\lib\Exceptions\FormatoDeFechaException;
use dsa\lib\Utils\DataChecker;
use Exception;

class SolicitudPrestamo
{
    private ?array $data;

    private bool $is_new;
    private ?array $tmp_data;
    private COperacionesSQL $SqlOp;


    /**
     * @param int|null $id
     * @param bool $is_new
     * @param array|null $data
     * @param COperacionesSQL|null $cop
     * @throws FormatoDeFechaException
     * @throws ProfesorNoExisteException
     * @throws SolicitudNoEncontradaException
     * @throws SolicitudPrestamoException
     * @throws UsuarioException
     * @throws UsuarioNoExistenteException
     */
    private function __construct(?int $id=null, bool $is_new=false, ?array $data=null, ?COperacionesSQL &$cop=null) {
        $this->SqlOp = COperacionesSQL::getInstance($cop);
        $this->is_new = $is_new;

        $this->data = null;

        if (!$is_new) {
            if (!is_null($id)) {
                if (!$this->_get_solicitud_by_id($id)) {
                    throw new SolicitudNoEncontradaException("La solicitud con id $id, no existe, verificala", 8001);
                }
            }
        }
        else {
            if ($this->_check_solicitud_pendiente($data["id_director_solicitante"], $data["id_director_receptor"], $data["id_profesor_solicitado"])) {
                throw new SolicitudPrestamoException("Ya existe una solicitud enviada pendiente.", 8021);
            } else {
                $this->tmp_data = $data;
            }
        }
    }

    /**
     * @param int $id
     * @return SolicitudPrestamo
     * @throws FormatoDeFechaException
     * @throws ProfesorNoExisteException
     * @throws SolicitudNoEncontradaException
     * @throws SolicitudPrestamoException
     * @throws UsuarioException
     * @throws UsuarioNoExistenteException
     */
    public static function get_solicitudDePrestamo_by_id(int $id) : SolicitudPrestamo {
        return new SolicitudPrestamo($id, false);
    }

    /**
     * @param array|null $filtro
     * @return array
     * @throws CConnexionException
     * @throws LlaveDeBusquedaIncorrectaException
     * @throws SQLTransactionException
     * @throws SolicitudPrestamoException
     */
    public static function get_all(?array $filtro=null) : array {
        $tmpSolicitud = new SolicitudPrestamo(null, false, null);
        return $tmpSolicitud->_get_all_solicitudes($tmpSolicitud->_create_sqlquery($filtro));
    }

    /**
     * @param String $query
     * @return array
     * @throws CConnexionException
     * @throws SQLTransactionException
     */
    private function _get_all_solicitudes(String $query) : array {
        $ids = array();
        $res = $this->SqlOp->exec($query);

        foreach ($res as $r) {
            $ids[] = $r["id"];
        }

        return $ids;
    }

    private function _create_sqlquery(?array $filtro) : String {
        $sqlquery = "SELECT solicitud_prestamo.id FROM solicitud_prestamo";
        $allowed_keys = ["director_solicitante", "director_receptor", "profesor_solicitado", "aceptada"];

        if (!is_null($filtro)) {
            if (!DataChecker::isAssoc($filtro)) {
                throw new SolicitudPrestamoException("El formato de los filtros no es válido, verifica la documentación", 8095);
            } else {
                $sqlquery .= " WHERE ";
                $tmpArray = array();
                foreach (array_keys($filtro) as $key) {
                    if (!in_array($key, $allowed_keys)) throw new LlaveDeBusquedaIncorrectaException("La llave \"$key\" no es permitida, verifica la documentación", 8101);

                    switch ($key) {
                        case "director_solicitante":
                        case "director_receptor":
                            $tmpArray[] = $this->_block_query_director($filtro[$key], $key);
                            break;
                        case "profesor_solicitado":
                            $tmpArray[] = $this->_block_query_profesorSolicitado($filtro["profesor_solicitado"]);
                            break;
                        case "aceptada":
                            $tmpArray[] = $this->_block_query_aceptada($filtro["aceptada"]);
                            break;
                        default:
                            echo "$key aún no implementada";
                            break;
                    }
                }
                $sqlquery .= implode(" AND ", $tmpArray);
            }
        }
        return $sqlquery;
    }

    private function _block_query_director($block, String $str_attr) : String {
        if (is_numeric($block)) {
            $str_val = "$str_attr = $block";
        } elseif (DataChecker::check_instance_of($block, "Usuario")) {
            $str_val = "$str_attr = " . $block->get_data("id");
        } else {
            throw new LlaveDeBusquedaIncorrectaException("La llave \"" . strval($block) . "\" no es válida, verificala", 8134);
        }
        return $str_val;
    }

    private function _block_query_profesorSolicitado($block) : String {
        if (is_numeric($block)) {
            $str_val = "profesor_solicitado = $block";
        } elseif (DataChecker::check_instance_of($block, "CProfesor")) {
            $str_val = "profesor_solicitado = " . $block->get_data("id");
        } else {
            throw new LlaveDeBusquedaIncorrectaException("La llave \"" . strval($block) . "\" no es válida, verificala", 8143);
        }

        return $str_val;
    }

    private function _block_query_aceptada($block) : String {
        if (!is_bool($block)) throw new LlaveDeBusquedaIncorrectaException("El Valor " . strval($block) . " no es correcto, verificalo", 8126);

        $str_val = ($block) ? "TRUE" : "FALSE";
        return "aceptada = $str_val";
    }

    /**
     * @param Usuario $director_solicitante
     * @param Usuario $director_receptor
     * @param Profesor $profesor_solicitado
     * @param COperacionesSQL|null $cop
     * @return SolicitudPrestamo
     * @throws CarreraNoExistenteException
     * @throws FormatoDeFechaException
     * @throws ProfesorNoExisteException
     * @throws SolicitudNoEncontradaException
     * @throws SolicitudPrestamoException
     * @throws UsuarioException
     * @throws UsuarioNoExistenteException
     * @throws CarreraException
     * @throws ProfesorException
     */
    public static function crea_nueva_solicitudDePrestamo(Usuario $director_solicitante, Usuario $director_receptor, Profesor $profesor_solicitado, COperacionesSQL &$cop=null) : SolicitudPrestamo {

        $carreraSolicitante = Carrera::get_carrera_by_director($director_solicitante);
        $id_carreraSolicitante = $carreraSolicitante->get_data("id");
        $id_profesorSolicitado = $profesor_solicitado->get_data("id");
        $imparte = ImpartenEn::impartenEn_by_carrera($carreraSolicitante);

        if ($imparte->profesor_ya_imparte_en_carrera($id_profesorSolicitado, $id_carreraSolicitante)) {
            throw new SolicitudPrestamoException("El profesor ya imparte materias en la carrera", 8017);
        }

        $tmp_data_solicitud = array("id_director_solicitante" => $director_solicitante->get_data("id"), "id_director_receptor" => $director_receptor->get_data("id"), "id_profesor_solicitado" => $profesor_solicitado->get_data("id"));

        $tmpSolicitud = new SolicitudPrestamo(null, true, $tmp_data_solicitud, $cop);

        $id_nuevaSolicitud = $tmpSolicitud->_save();

        if (is_null($id_nuevaSolicitud)) {
            throw new SolicitudPrestamoException("No se ha podido generar la nueva solicitud de prestamo", 8018);
        }
        else {
            $tmpSolicitud = SolicitudPrestamo::get_solicitudDePrestamo_by_id($id_nuevaSolicitud);
        }

        return $tmpSolicitud;
    }

    /**
     * Método que cambia el estatus de la solicitud a ACEPTADA y agrega al profesor para impartir en la carrera.
     * @throws SolicitudPrestamoException
     * @throws SolicitudPreviamenteAceptadaException
     */
    public function aceptar_solicitud() {
        if ($this->data["aceptada"]) {
            throw new SolicitudPreviamenteAceptadaException("La solicitud ya ha sido aceptada", 8003);
        }

        $carrera = Carrera::get_carrera_by_director(Usuario::get_usuario_by_id($this->data["id_director_solicitante"]));
        $imparten = ImpartenEn::impartenEn_by_carrera($carrera);

        if (!$imparten->agrega_profesor(Profesor::get_profesor_by_id($this->data["id_profesor_solicitado"]))) {
            throw new ProfesorNoAgregadoException("No se ha podido agregar al profesor a la carrera para impartición de materias", 8008);
        }
        elseif (!$this->_aceptar_solicitud()) {
            throw new SolicitudPrestamoException("Se agregó el profesor a la carrera. No fue posible cambiar el status de la solicitud", 8004);
        } else {
            $this->data["aceptada"] = true;
        }
    }

    public function esta_aceptada() : bool {
        return $this->data["aceptada"];
    }

    public function get_data($filtro=null) {
        $allowed_keys = ["id", "id_director_solicitante", "director_solicitante", "id_director_receptor", "director_receptor", "id_profesor_solicitado", "profesor_solicitado", "fecha_solicitud", "fecha_aceptada", "aceptada"];
        $dataReturn = null;
        if (!is_null($filtro)) {
            if (!is_array($filtro)) {
                if (!in_array($filtro, $allowed_keys)) {
                    throw new SolicitudPrestamoException("La llave $filtro no es válida, verifica la documentación.", 8169);
                } else {
                    $dataReturn = $this->_data_return($filtro);
                }
            } else {
                $dataReturn = array();
                foreach ($filtro as $key) {
                    if (!in_array($key, $allowed_keys)) {
                        throw new SolicitudPrestamoException("La llave $key no es válida, verifica la documentación.", 8177);
                    } else {
                        $dataReturn[$key] = $this->_data_return($key);
                    }
                }
            }
        } else {
            $dataReturn = $this->data;
            $dataReturn["fecha_solicitud"] = $this->_data_return("fecha_solicitud");
            $dataReturn["fecha_aceptada"] = $this->_data_return("fecha_aceptada");
        }

        return $dataReturn;
    }

    /**
     * @throws ProfesorNoExisteException
     * @throws UsuarioException
     * @throws UsuarioNoExistenteException
     * @throws FormatoDeFechaException
     */
    private function _get_solicitud_by_id(int $id) : bool {
        $ban = false;
        $sqlquery = "SELECT solicitud_prestamo.director_solicitante as id_director_solicitante, solicitud_prestamo.director_receptor as id_director_receptor, solicitud_prestamo.profesor_solicitado as id_profesor_solicitado, solicitud_prestamo.fecha_solicitud as fecha_solicitud, solicitud_prestamo.aceptada as aceptada, solicitud_prestamo.fecha_aceptada as fecha_aceptada FROM solicitud_prestamo WHERE solicitud_prestamo.id = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "i", [$id]);

            if (count($res) == 1) {
                try {
                    $dt_fecha_solicitud = new DateTime($res[0]["fecha_solicitud"]);
                    $dt_fecha_aceptada = is_null($res[0]["fecha_aceptada"]) ? null : new DateTime($res[0]["fecha_aceptada"]);
                } catch (Exception $e) {
                    throw new FormatoDeFechaException("El formato de fechas no es correcto, verificalas", 8003);
                }

                $this->_asigna_campos_privados($id, $res[0]["id_director_solicitante"], $res[0]["id_director_receptor"], $res[0]["id_profesor_solicitado"], $dt_fecha_solicitud, $res[0]["aceptada"], $dt_fecha_aceptada);
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    /**
     * @param int $id
     * @param int $id_director_solicitante
     * @param int $id_director_receptor
     * @param int $id_profesor_solicitado
     * @param DateTime $dt_fecha_solicitud
     * @param bool $aceptada
     * @param DateTime|null $dt_fecha_aceptada
     * @throws ProfesorNoExisteException
     * @throws UsuarioException
     * @throws UsuarioNoExistenteException
     */
    private function _asigna_campos_privados(int $id, int $id_director_solicitante, int $id_director_receptor, int $id_profesor_solicitado, DateTime $dt_fecha_solicitud, bool $aceptada, ?DateTime $dt_fecha_aceptada) {
        $this->data["id"] = $id;
        $this->data["id_director_solicitante"] = $id_director_solicitante;
        $this->data["id_director_receptor"] = $id_director_receptor;
        $this->data["id_profesor_solicitado"] = $id_profesor_solicitado;
        $this->data["fecha_solicitud"] = $dt_fecha_solicitud;
        $this->data["aceptada"] = $aceptada;
        $this->data["fecha_aceptada"] = $dt_fecha_aceptada;
    }

    private function _aceptar_solicitud() : bool {
        $ban = false;
        $sqlquery = "UPDATE solicitud_prestamo SET aceptada = TRUE, solicitud_prestamo.fecha_aceptada = NOW() WHERE solicitud_prestamo.id = ?";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "i", [$this->data["id"]]) == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = true;
        }

        return $ban;
    }

    private function _save() : ?int {
        return ($this->is_new) ? $this->_registra_nueva_solicitud() : null;
    }

    private function _registra_nueva_solicitud() : ?int {
        $insertId = null;
        $sqlquery = "INSERT INTO solicitud_prestamo(director_solicitante, director_receptor, profesor_solicitado) VALUES (?, ?, ?)";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "iii", [$this->tmp_data["id_director_solicitante"], $this->tmp_data["id_director_receptor"], $this->tmp_data["id_profesor_solicitado"]]) == 1;
            if ($ban) $insertId = $this->SqlOp->obtenerUltimoIdInsertado();
        } catch (CConnexionException | SQLTransactionException $e) {
            $insertId = null;
        }

        return $insertId;
    }

    private function _check_solicitud_pendiente(int $id_director_solicitante, int $id_director_receptor, int $id_profesor_solicitado) : bool {
        $ban = false;
        $sqlquery = "SELECT COUNT(*) as pendiente FROM solicitud_prestamo WHERE solicitud_prestamo.director_solicitante = ? AND solicitud_prestamo.director_receptor = ? AND solicitud_prestamo.profesor_solicitado = ? AND solicitud_prestamo.aceptada = FALSE";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "iii", [$id_director_solicitante, $id_director_receptor, $id_profesor_solicitado])[0]["pendiente"] > 0;
        } catch (CConnexionException | SQLTransactionException $e) {
            return $ban;
        }

        return $ban;
    }

    private function _data_return($key) {
        $dataReturn = null;
        if ($key == "id_director_solicitante" || $key == "director_solicitante") {
            $dataReturn = $this->data["id_director_solicitante"];
        } elseif ($key == "id_director_receptor" || $key == "director_receptor") {
            $dataReturn = $this->data["id_director_receptor"];
        } elseif ($key == "id_profesor_solicitado" || $key == "profesor_solicitado") {
            $dataReturn = $this->data["id_profesor_solicitado"];
        } elseif ($key == "fecha_solicitud" || $key == "fecha_aceptada") {
            $dataReturn = (!is_null($this->data[$key])) ? $this->data[$key]->format("Y/m/d") : "";
        } else {
            $dataReturn = $this->data[$key];
        }
        return $dataReturn;
    }
}