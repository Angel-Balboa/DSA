<?php

namespace dsa\api\controller\profesor;

use dsa\api\controller\profesor\CProfesor;
use dsa\api\model\coautor\Exceptions\ParametrosNoValidosException;
use dsa\api\model\coautor\Exceptions\ProductoNoAgregadoAProfesorException;
use dsa\api\model\coautor\Exceptions\ProfesorExisteEnProductoException;
use dsa\api\model\producto_cientifico\Exceptions\ProductoCientificoException;
use dsa\api\model\producto_cientifico\Exceptions\ProductoCientificoNoExisteException;
use dsa\api\model\profesor\Exceptions\LlaveDeBusquedaIncorrectaException;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\coautor\CoAutor;
use dsa\api\model\producto_cientifico\ProductoCientifico;

class CProductoCientifico extends CProfesor
{
    public function __construct(Profesor $profesor)
    {
        parent::__construct($profesor);
    }

    /**
     * @param array $data
     * @return bool
     * @throws ParametrosNoValidosException
     * @throws ProductoNoAgregadoAProfesorException
     * @throws ProfesorExisteEnProductoException
     * @throws ProductoCientificoException
     * @throws ProductoCientificoNoExisteException
     * @throws LlaveDeBusquedaIncorrectaException
     */
    public function crea_producto_cientifico(array $data) : bool {

        $ban = false;
        $tmpProduct = ProductoCientifico::crea_productoCientifico($data, $this->Msql);

        if (!is_null($tmpProduct)) {
            $tmpCoautor = CoAutor::CoAutor_by_profesor($this->profesor, $this->Msql);
            return $tmpCoautor->agrega_productoCientifico_a_profesor($tmpProduct, 1);
        }

        return false;
    }

    public function actualiza_producto_cientifico(ProductoCientifico $productoCientifico, array $newData) : bool {
        $tmpProducto = ProductoCientifico::get_productoCientifico_by_id($productoCientifico->get_data("id"), $this->Msql);

        return $tmpProducto->actualiza_datos($newData);
    }
}