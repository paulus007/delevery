<?php

namespace Delevery\UI;

/* Для вывода html (чтобы не перегружать индексный файл кодом) */
class Forms {

    private object $db;

    /** Конструктор класса
     * @var db object // ссылка на объект для взаимодействия с БД
     */
    public function __construct (
    
        object &$db
    
    ) {

        $this->db = $db;

    }

    /** Получить тег <select> со списком пунктов отправки / доставки
     * @var name string // аттрибуты name и id для тега
     * @return string
     */
    public function get_cladrs_list (

        string $name = ''

    ) {

        $cladrs = $this->db->query( '
            SELECT `c`.`id`,
                   `c`.`code`,
                   `c`.`name`
            FROM `cladr` as `c`
            ORDER BY `c`.`name` ASC
        ' );

        $options = "<option value=\"\">Выберите из списка..</option>\n";

        foreach ( $cladrs as $cladr ) {

            $options .= "<option value=\"{$cladr->code}\">{$cladr->name}</option>";

        }

        return "
            <select class=\"cladrs-list\" name=\"{$name}\" id=\"{$name}\" required>
                {$options}
            </select>
        ";

    }

    /** Получить тег <select> со списком типов доставки
     * @var name string // аттрибуты name и id для тега
     * @return string
     */
    public function get_types_list (

        string $name = ''

    ) {

        $types = $this->db->query( '
            SELECT `t`.`id`,
                `t`.`name`,
                `t`.`description`
            FROM `type` as `t`
            ORDER BY `t`.`id` ASC
        ' );

        $options = "<option value=\"\">Выберите из списка..</option>\n";

        foreach ( $types as $type ) {

            $options .= "<option value=\"{$type->name}\">{$type->description}</option>";

        }

        return "
            <select class=\"types-list\" name=\"{$name}\" id=\"{$name}\" required>
                {$options}
            </select>
        ";

    }

    /** Получить тег <select> со списком транспортных компаний
     * @var name string // аттрибуты name и id для тега
     * @return string
     */
    public function get_companies_list (

        string $name = ''

    ) {

        $companies = $this->db->query( '
            SELECT `c`.`id`,
                   `c`.`name`
            FROM `company` as `c`
            ORDER BY `c`.`name` ASC
        ' );

        $options = "<option value=\"\">Выберите из списка..</option>\n";

        foreach ( $companies as $company ) {

            $options .= "<option value=\"{$company->id}\">{$company->name}</option>";

        }

        return "
            <select class=\"companies-list\" name=\"{$name}\" id=\"{$name}\">
                {$options}
            </select>
        ";

    }

}

?>
