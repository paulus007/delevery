-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Сен 21 2023 г., 17:54
-- Версия сервера: 11.1.2-MariaDB
-- Версия PHP: 8.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `delivery`
--
CREATE DATABASE IF NOT EXISTS `delivery` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `delivery`;

-- --------------------------------------------------------

--
-- Структура таблицы `cladr`
--

CREATE TABLE `cladr` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `cladr`
--

INSERT INTO `cladr` (`id`, `code`, `name`) VALUES
(1, '1100600002600', 'Кошки Деревня '),
(2, '1100600002300', 'Княжпогост Село'),
(3, '1100600005200', 'Тракт Поселок'),
(4, '1100600006600', 'Ляли Деревня');

-- --------------------------------------------------------

--
-- Структура таблицы `company`
--

CREATE TABLE `company` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `company`
--

INSERT INTO `company` (`id`, `name`) VALUES
(1, 'Наша ТК'),
(2, 'Тестовая ТК');

-- --------------------------------------------------------

--
-- Структура таблицы `periodicity`
--

CREATE TABLE `periodicity` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `periodicity`
--

INSERT INTO `periodicity` (`id`, `name`, `description`) VALUES
(1, 'daily', 'Ежедневно'),
(2, 'weekly', 'Еженедельно');

-- --------------------------------------------------------

--
-- Структура таблицы `price`
--

CREATE TABLE `price` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL COMMENT 'id транспортной компании',
  `type_id` int(11) NOT NULL COMMENT 'id типа доставки',
  `from_cladr_id` int(11) NOT NULL COMMENT 'откуда (id)',
  `to_cladr_id` int(11) NOT NULL COMMENT 'куда (id)',
  `periodicity_id` int(11) NOT NULL COMMENT 'с какой периодичностью (id)',
  `periodicity_value` int(11) DEFAULT NULL COMMENT 'значение периодичности (день недели, день месяца)',
  `delivery_time` int(11) NOT NULL COMMENT 'время доставки, дней (больше нуля)',
  `coeff_per_kg` float DEFAULT NULL COMMENT 'коэффициент для расчета коэффициента стоимости медленной доставки (в показательной функции)',
  `price_per_kg` float DEFAULT NULL COMMENT 'цена за кг при быстрой доставке',
  `comment` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `price`
--

INSERT INTO `price` (`id`, `company_id`, `type_id`, `from_cladr_id`, `to_cladr_id`, `periodicity_id`, `periodicity_value`, `delivery_time`, `coeff_per_kg`, `price_per_kg`, `comment`) VALUES
(1, 1, 1, 1, 2, 2, 6, 2, NULL, 70, 'Автомобиль кат. B. Ежедневно.'),
(2, 1, 2, 1, 2, 2, 3, 2, 0.17, NULL, 'Автомобиль кат. C.\r\nЕженедельно по средам.'),
(3, 1, 1, 4, 3, 1, NULL, 3, NULL, 50, ''),
(4, 2, 1, 4, 3, 1, NULL, 2, NULL, 65, ''),
(5, 2, 2, 4, 2, 2, 3, 1, 0.15, NULL, ''),
(6, 2, 2, 3, 2, 2, 5, 1, 0.15, NULL, ''),
(7, 2, 2, 2, 3, 1, NULL, 1, 0.2, NULL, ''),
(8, 2, 1, 2, 3, 1, NULL, 1, NULL, 30, ''),
(11, 1, 1, 2, 2, 1, NULL, 1, NULL, 20, 'Доставка по Княжпогост Село'),
(12, 2, 2, 2, 2, 1, NULL, 1, 0.1, NULL, 'Доставка по Княжпогост Село');

-- --------------------------------------------------------

--
-- Структура таблицы `type`
--

CREATE TABLE `type` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `type`
--

INSERT INTO `type` (`id`, `name`, `description`) VALUES
(1, 'express', 'Быстрая доставка'),
(2, 'standart', 'Медленная доставка');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `cladr`
--
ALTER TABLE `cladr`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cladr_code__uq` (`code`);

--
-- Индексы таблицы `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `periodicity`
--
ALTER TABLE `periodicity`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `price`
--
ALTER TABLE `price`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id__fk` (`company_id`),
  ADD KEY `type_id__fk` (`type_id`),
  ADD KEY `from_cladr_id__fk` (`from_cladr_id`),
  ADD KEY `to_cladr_id__fk` (`to_cladr_id`),
  ADD KEY `periodicity_id__fk` (`periodicity_id`);

--
-- Индексы таблицы `type`
--
ALTER TABLE `type`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `cladr`
--
ALTER TABLE `cladr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `company`
--
ALTER TABLE `company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `periodicity`
--
ALTER TABLE `periodicity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `price`
--
ALTER TABLE `price`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `type`
--
ALTER TABLE `type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `price`
--
ALTER TABLE `price`
  ADD CONSTRAINT `company_id__fk` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `from_cladr_id__fk` FOREIGN KEY (`from_cladr_id`) REFERENCES `cladr` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `periodicity_id__fk` FOREIGN KEY (`periodicity_id`) REFERENCES `periodicity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `to_cladr_id__fk` FOREIGN KEY (`to_cladr_id`) REFERENCES `cladr` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `type_id__fk` FOREIGN KEY (`type_id`) REFERENCES `type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
