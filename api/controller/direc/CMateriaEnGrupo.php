<?php

namespace dsa\api\controller\direc;

use dsa\api\model\materia\Exceptions\MateriaNoExistenteException;
use dsa\api\model\materia_en_grupo\Exceptions\MateriaEnGrupoException;
use dsa\api\model\materia_en_grupo\Exceptions\MateriaEnGrupoNoExisteException;
use dsa\api\model\grupo\Grupo;
use dsa\api\model\materia\Materia;
use dsa\api\model\materia_en_grupo\Exceptions\ProfesorEnGrupoException;
use dsa\api\model\profesor\Exceptions\ProfesorNoExisteException;
use dsa\api\model\profesor\Profesor;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\materia_en_grupo\MateriaEnGrupo;

class CMateriaEnGrupo extends Director
{
    public function __construct(Usuario $usuario)
    {
        parent::__construct($usuario);
    }

    /**
     * @param Grupo $grupo
     * @param Materia $materia
     * @param Profesor|null $profesor
     * @param int $alumnos_estimados
     * @param int $modificador_horas
     * @return MateriaEnGrupo|null
     * @throws MateriaEnGrupoException
     * @throws MateriaEnGrupoNoExisteException
     */
    public function crea_nueva_MeG(Grupo $grupo, Materia $materia, ?Profesor $profesor, int $alumnos_estimados, int $modificador_horas): ?MateriaEnGrupo
    {
        return MateriaEnGrupo::crear_nueva_asignacion_materiaEnGrupo($grupo, $materia, $profesor, $alumnos_estimados, $modificador_horas, $this->Msql);
    }


    /**
     * @throws MateriaNoExistenteException
     * @throws ProfesorEnGrupoException
     * @throws ProfesorNoExisteException
     * @throws MateriaEnGrupoException
     * @throws MateriaEnGrupoNoExisteException
     */
    public function Actualizar_P(MateriaEnGrupo $meg, array $newData) : bool {
        $tmpMeg = MateriaEnGrupo::get_MEG_by_id($meg->get_data("id"), $this->Msql);

        return $tmpMeg->actualizar_datos_P($newData);
    }

    /**
     * @param MateriaEnGrupo $meg
     * @param array $newData
     * @return bool
     * @throws MateriaEnGrupoException
     * @throws MateriaEnGrupoNoExisteException
     * @throws MateriaNoExistenteException
     * @throws ProfesorEnGrupoException
     * @throws ProfesorNoExisteException
     */
    public function Actualizar_Datos(MateriaEnGrupo $meg, array $newData) : bool {
        $tmpMeg = MateriaEnGrupo::get_MEG_by_id($meg->get_data("id"), $this->Msql);
        return $tmpMeg->actualizar_datos($newData);
    }

    public function Eliminar_MEG(int $id_meg,MateriaEnGrupo $m){
        $tmp=MateriaEnGrupo::get_MEG_by_id($m->get_data("id"),$this->Msql);
        return $tmp->eliminar_MEG($id_meg);
    }
}


