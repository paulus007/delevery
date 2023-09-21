<?php

namespace Delevery\API;

/* Эмулятор API транспортных компаний */
class TC {

    private object $db;
    private int    $id;

    /** Конструктор класса
     * @var db object // ссылка на объект для взаимодействия с БД
     */
    public function __construct (
    
        object &$db
    
    ) {

        $this->db = $db;
        $this->id = 0;

    }

    /** Устанавливает id транспортной компании для API (на практике скорее будет url)
     * @var id int
     * @return boolean
     */
    public function set_id (

        int $id

    ) {

        $this->id = $this->db->escape( $id );
        return true;

    }
    
    /** Расчитывает параметры быстрой доставки
     * @var sourceKladr string // кладр откуда везем
     * @var targetKladr string // кладр куда везем
     * @var weight      float  // вес отправления в кг
     * @return json
     */
    public function get_express ( 
        
        string $sourceKladr = '',
        string $targetKladr = '',
        float  $weight      = 0.0
    
    ) {

        $sourceKladr = $this->db->escape( $sourceKladr );
        $targetKladr = $this->db->escape( $targetKladr );

        $params = $this->db->query( "
        SELECT `p`.`id`,
               `per`.`name` AS `periodicity`,
               `p`.`periodicity_value` AS `day_of_periodicity`,
               `p`.`delivery_time`,
               `p`.`price_per_kg`
        FROM `price` AS `p`
        JOIN `company` AS `c` ON `c`.`id` = `p`.`company_id`
                             AND `c`.`id` = {$this->id}
        JOIN `type` AS `t` ON `t`.`id` = `p`.`type_id`
                          AND `t`.`name` = 'express'
        JOIN `periodicity` AS `per` ON `per`.`id` = `p`.`periodicity_id`
        JOIN `cladr` AS `cl_from` ON `cl_from`.`id` = `p`.`from_cladr_id`
                                 AND `cl_from`.`code` = '{$sourceKladr}'
        JOIN `cladr` AS `cl_to` ON `cl_to`.`id` = `p`.`to_cladr_id`
                               AND `cl_to`.`code` = '{$targetKladr}'
        ORDER BY `p`.`id` ASC
        LIMIT 1;
        " );

        if ( !count( $params ) ) {

            return json_encode(
                ( object ) [
                    'price'  => 0.0,
                    'period' => 0,
                    'error'  => 'Route not found'
                ]
            );

        } else {

            $params = $params[ 0 ];

        }
        
        $now = time();

        // Для учета времени, т.к. после 18.00 заявки на быструю доставку не принимаются
        if ( intval( date( 'H', $now ) ) < 18 ) {
            
            $now -= 86400;

        }

        // Для учёта преиодичности отправок (ежедневно, по дням недели, по дням месяца)
        switch ( $params->periodicity ) {

            case 'weekly':
                $date = $now + ( $params->delivery_time + ( ( $params->day_of_periodicity - date('N', $now) ) >= 0 ? ( $params->day_of_periodicity - date('N', $now) ) : ( $params->day_of_periodicity + 7 - date('N', $now) ) ) ) * 86400;
                break; 
            case 'monthly':
                $date = $now + ( $params->delivery_time + ( ( $params->day_of_periodicity - date('d', $now) ) >= 0 ? ( $params->day_of_periodicity - date('d', $now) ) : ( $params->day_of_periodicity + 30 - date('d', $now) ) ) ) * 86400;
                break;
            
            default:
                $date = $now + $params->delivery_time * 86400;
                break;

        }

        return json_encode(
            ( object ) [
                'price'  => round( $weight * $params->price_per_kg , 2 ),
                'period' => intval( ( $date - time() ) / 86400 ),
                'error'  => ''
            ]
        );

    }

    /** Расчитывает параметры медленной доставки
     * @var sourceKladr string // кладр откуда везем
     * @var targetKladr string // кладр куда везем
     * @var weight      float  // вес отправления в кг
     * @return json
     */
    public function get_standart ( 
        
        string $sourceKladr = '',
        string $targetKladr = '',
        float  $weight      = 0.0
    
    ) {

        $sourceKladr = $this->db->escape( $sourceKladr );
        $targetKladr = $this->db->escape( $targetKladr );

        $params = $this->db->query( "
        SELECT `p`.`id`,
               `per`.`name` AS `periodicity`,
               `p`.`periodicity_value` AS `day_of_periodicity`,
               `p`.`delivery_time`,
               `p`.`basic_coeff_per_kg`
        FROM `price` AS `p`
        JOIN `company` AS `c` ON `c`.`id` = `p`.`company_id`
                             AND `c`.`id` = {$this->id}
        JOIN `type` AS `t` ON `t`.`id` = `p`.`type_id`
                          AND `t`.`name` = 'standart'
        JOIN `periodicity` AS `per` ON `per`.`id` = `p`.`periodicity_id`
        JOIN `cladr` AS `cl_from` ON `cl_from`.`id` = `p`.`from_cladr_id`
                                 AND `cl_from`.`code` = '{$sourceKladr}'
        JOIN `cladr` AS `cl_to` ON `cl_to`.`id` = `p`.`to_cladr_id`
                               AND `cl_to`.`code` = '{$targetKladr}'
        ORDER BY `p`.`id` ASC
        LIMIT 1;
        " );

        if ( !count( $params ) ) {

            return json_encode(
                ( object ) [
                    'coefficient'  => 0.0,
                    'date'         => '0000-00-00',
                    'error'        => 'Route not found'
                ]
            );

        } else {

            $params = $params[ 0 ];

        }
        
        $now = time();

        // Для учёта преиодичности отправок (ежедневно, по дням недели, по дням месяца)
        switch ( $params->periodicity ) {

            case 'weekly':
                $date = $now + ( $params->delivery_time + ( ( $params->day_of_periodicity - date('N', $now) ) >= 0 ? ( $params->day_of_periodicity - date('N', $now) ) : ( $params->day_of_periodicity + 7 - date('N', $now) ) ) ) * 86400;
                break; 
            case 'monthly':
                $date = $now + ( $params->delivery_time + ( ( $params->day_of_periodicity - date('d', $now) ) >= 0 ? ( $params->day_of_periodicity - date('d', $now) ) : ( $params->day_of_periodicity + 30 - date('d', $now) ) ) ) * 86400;
                break;
            
            default:
                $date = $now + $params->delivery_time * 86400;
                break;

        }

        return json_encode(
            ( object ) [
                // Для плавного роста зависимости коэффициента стоимости от веса используем показательную функцию
                'coefficient' => round( $weight ** $params->basic_coeff_per_kg, 4 ),
                'date'        => date( 'Y-m-d', $date ),
                'error'       => ''
            ]
        );

    }

}

?>
