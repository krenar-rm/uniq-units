<?php

declare(strict_types=1);

namespace App;

/**
 * Единица
 */
class Unit
{

    /**
     * Идентификатор
     */
    public $id;

    /**
     * Наименование
     */
    public $name;

    /**
     * Статус
     */
    public $status;

    /**
     * Сумма
     */
    public $sum;

    /**
     * Данные
     */
    public $data;

    /**
     * Дата обновления
     */
    public $updated_at;

    /**
     * Уникализирует объекты $units по набору полей $fieldNames
     *
     * @param Unit[]   $units
     * @param string[] $fieldNames
     *
     * @return Unit[]
     *
     * @throws \RuntimeException
     */
    public static function uniq(array $units, array $fieldNames): array
    {
        self::validateUnits($units);
        self::validateFieldNames($fieldNames);

        // Получение нормализованных единиц согласно указанным полям
        $normalizedUnits = \array_map(function ($unit) use ($fieldNames) {
            return self::normalizeUnit($unit, $fieldNames);
        }, $units);

        // Сериализуем и убираем дублирующиеся данные
        $uniqueUnits = \array_unique(
            \array_map(
                "serialize",
                $normalizedUnits
            )
        );

        // Достаем исходные объекты из $units согласно уникальным нормализованным значениям $uniqueUnits
        $result = \array_intersect_key(
            $units,
            $uniqueUnits
        );

        return \array_values($result);
    }

    /**
     * Нормализация единицы согласно указанным полям в $fieldNames
     * Если не указаны поля, то берем все поля из объекта
     *
     * @param Unit  $unit
     * @param array $fieldNames
     *
     * @return array
     */
    private static function normalizeUnit(Unit $unit, array $fieldNames = []): array
    {
        $unitArray = (array) $unit;

        return empty($fieldNames) ? $unitArray : \array_intersect_key($unitArray, array_flip($fieldNames));
    }

    /**
     * Проверка массива объектов Unit
     *
     * @param array $units
     *
     * @return void
     */
    private static function validateUnits(array $units)
    {
        foreach ($units as $unit) {
            if (!$unit instanceof Unit) {
                throw new \RuntimeException('Ошибка типа во входящем массиве Unit[]');
            }
        }
    }

    /**
     * Проверка полей для уникализации
     *
     * @param array $fieldNames
     *
     * @return void
     */
    private static function validateFieldNames(array $fieldNames)
    {
        if (empty($fieldNames)) {
            return;
        }

        // Получение свойств класса Unit, как ключей массива
        $expectedFieldNames = array_keys((array) new Unit());

        if ($arrDiff = \array_diff($fieldNames, $expectedFieldNames)) {
            throw new \RuntimeException(
                sprintf('Поля "%s" отсутствуют в Unit', \implode(', ', $arrDiff))
            );
        }
    }
}
