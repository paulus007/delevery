<?php

namespace Delevery;

include './conf/config.php';
include './classes/io.php';
include './classes/api.php';
include './classes/processor.php';

use Delevery\IO;
use Delevery\API;
use Delevery\Processor;

header( 'Content-Type: application/json; charset=utf-8' );
date_default_timezone_set( 'Europe/Moscow' );

$db     = new IO\DB( $config->db );
$tc_api = new API\TC( $db );
$calc   = new Processor\Calculator( $tc_api );

// Получаем параметры из POST
$vars = ( object ) [
    'from'   => strval( $_POST['cladrFrom'] ),
    'to'     => strval( $_POST['cladrTo'] ),
    'type'   => strval( $_POST['type'] ),
    'tc'     => intval( $_POST['company'] ),
    'weight' => floatval( $_POST['weight'] )
];

// Если выбрана транспортная компания - используем её
if ( $vars->tc ) {

    $tc = [ $vars->tc ];

// Иначе получаем список всех
} else {

    $tc = $db->query( '
        SELECT `c`.`id`
        FROM `company` AS `c`
        ORDER BY `c`.`id`
    ' );

    $tc = array_map( function( $i ){ return $i->id; }, $tc );

}

$result = [];

// Устанавливаем базовую стоимость доставки
$calc->set_basic_price( 150.00 );

// Делаем расчет стоимости доставки
foreach ( $tc as $t ) {

    $result[] = json_decode(
        $calc->calculate( 
            $t,
            $vars->type,
            $vars->from,
            $vars->to,
            $vars->weight,
        )
    );

}

echo json_encode( $result );

?>
