<?php

include_once ("init.php");

use dsa\api\controller\sesion\Sesion;

$sesion = Sesion::getInstance();
$sesion->destroy();
header("Location: login.php");
exit();