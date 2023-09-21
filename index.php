<?php

namespace Delevery;

include './conf/config.php';
include './classes/io.php';
include './classes/ui.php';

use Delevery\IO;
use Delevery\UI;

$db = new IO\DB( $config->db );

$forms = new UI\Forms( $db );

?>
<!DOCTYPE html>
<html>

<head>

    <title>Главная</title>
    <link rel="stylesheet" href="/css/styles.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="/js/scripts.js"></script>

</head>

<body>
    
    <form id="calc-form" action="action.php" method="post" enctype="multipart/form-data">
        <div class="form-row">
            <label for="cladrFrom">Откуда (*):</label>
            <?php echo $forms->get_cladrs_list( 'cladrFrom' ) ?>
        </div>
        <div class="form-row">
            <label for="cladrTo">Куда (*):</label>
            <?php echo $forms->get_cladrs_list( 'cladrTo' ) ?>
        </div>
        <div class="form-row">
            <label for="type">Тип доставки (*):</label>
            <?php echo $forms->get_types_list( 'type' ) ?>
        </div>
        <div class="form-row">
            <label for="company">Предпочитаемая ТК:</label>
            <?php echo $forms->get_companies_list( 'company' ) ?>
        </div>
        <div class="form-row">
            <label for="weight">Вес отправления, кг (*):</label>
            <input type="text" name="weight" id="weight" value="" placeholder="Введите значение.." required>
        </div>
        <div class="form-row">
            <input type="submit" value="Запросить стоимость">
        </div>
    </form>

    <div id="results"></div>

</body>

</html>
