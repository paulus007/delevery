<?php

namespace Delevery\IO;

/* Для использования БД */
class DB {

    private object $connection;

    /** Конструктор класса
     * @var _conf array // массив с параметрами для подключения к БД (хранится в './conf/config.php')
     */
    public function __construct (
    
        array $_conf = [
            'host'     => 'localhost',
            'user'     => 'root',
            'password' => '',
            'db'       => '',
            'port'     => null,
            'socket'   => null
        ]
    
    ) {
        
        try {

            $this->connection = new \mysqli(
                $_conf['host'],
                $_conf['user'],
                $_conf['password'],
                $_conf['db']
            );

        } catch ( mysqli_sql_exception $exception ) {

            error_log( $exception->getMessage() );
            die( 'Database connection error.' );

        }

    }

    /** Закрыть подключение к БД
     * @return boolean
     */
    public function close ( ) {
            
        return $this->connection->close();

    }

    /** Включить / выключить autocommit
     * @var state boolean
     * @return boolean
     */
    public function autocommit ( 
        
        bool $state = true

    ) {
        
        return $this->connection->autocommit( $state );

    }

    /** Подтверждение транзакции
     * @return boolean
     */
    public function commit ( ) {
        
        return $this->connection->commit();

    }

    /** Откат транзакции
     * @return boolean
     */
    public function rollback ( ) {
        
        return $this->connection->rollback();

    }

    /** Экранирование спец. символов в строке для SQL-запроса
     * @var string string
     * @return boolean
     */
    public function escape ( 

        string $string
    
    ) {

        return $this->connection->escape_string( $string );

    }

    /** Выполнить запрос к БД
     * @var query string
     * @return object|array
     */
    public function query (
        
        string $query       = '',
        string $result_type = 'object'
        
    ) {

        if ( is_string( $query ) && mb_strlen( $query ) ) {

            $query_result = $this->connection->query( $query );
            
            if ( !$query_result ) {

                return false;

            }

            $result = [];

            while ( $row = $query_result->fetch_assoc() ) {

                switch ( $result_type ) {

                    case 'object':
                        $result[] = ( object ) $row;
                        break;
                    default:
                        $result[] = $row;
                        break;

                }

            }

            $query_result->free();

            return $result;

        } else {
        
            return false;

        }
        

    }

}

?>
