<?php

namespace Delevery\Processor;

/* Для расчета конечной стоимости доставки */
class Calculator {

    private float  $basic_price;
    private object $api;

    /** Конструктор класса
     * @var api object // ссылка на объект для взаимодействия с API ТК
     */
    public function __construct (
    
        object &$api
    
    ) {

        $this->api = $api;
        $this->basic_price = 0.00;

    }

    /** Установить базовую стоимость доставки (используется при расчете медленной доставки)
     * @var price float
     * @return boolean
     */
    public function set_basic_price (

        float $price = 0.00

    ) {

        $this->basic_price = floatval( $price );
        return true;

    }

    /** Расчитывает итоговую стоимость доставки
     * @var company_id  int    // id транспортной компании
     * @var type        string // тип доставки
     * @var sourceKladr string // кладр откуда везем
     * @var targetKladr string // кладр куда везем
     * @var weight      float  // вес отправления в кг
     * @return json
     */
    public function calculate ( 
        
        int    $company_id  = 0,
        string $type        = '',
        string $sourceKladr = '',
        string $targetKladr = '',
        float  $weight      = 0.0
    
    ) {
       
        $this->api->set_id( $company_id );

        switch ( $type ) {

            case 'express':
                $tc_params = json_decode(
                    $this->api->get_express(
                        $sourceKladr,
                        $targetKladr,
                        $weight
                    )
                );
                return json_encode(
                    ( object ) [
                        'price' => $tc_params->price,
                        'date'  => date( 'Y-m-d', time() + $tc_params->period * 86400 ),
                        'error' => $tc_params->error
                    ]
                );

            case 'standart':
                $tc_params = json_decode(
                    $this->api->get_standart(
                        $sourceKladr,
                        $targetKladr,
                        $weight
                    )
                );
                return json_encode(
                    ( object ) [
                        'price' => round( $this->basic_price * $tc_params->coefficient, 2 ),
                        'date'  => $tc_params->date,
                        'error' => $tc_params->error
                    ]
                );
            
            default:
                return json_encode(
                    ( object ) [
                        'price' => 0.0,
                        'date'  => '0000-00-00',
                        'error' => 'Invalid delevery type'
                    ]
                );

        }

    }

}

?>