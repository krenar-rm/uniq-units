<?php

declare(strict_types=1);

namespace App\Tests;

use App\Unit;
use PHPUnit\Framework\TestCase;

/**
 * Тестирование класса App\Unit
 */
class UnitTest extends TestCase
{
    /**
     * Поставщик данных для self::testUniq
     *
     * @return array
     */
    public function provideUniqData(): array
    {
        $unit = $this->getUnitPrototype();

        $unitAnotherStatus = $this->getUnitPrototype();
        $unitAnotherStatus->status = 'some another status';

        $unitAnotherSum = $this->getUnitPrototype();
        $unitAnotherSum->sum = 'some another sum';

        $unitCloneUnit = $this->getUnitPrototype();

        $unitDateTime = $this->getUnitPrototype();
        $unitDateTime->updated_at = new \DateTime();

        return [
            [
                [
                    $unit,
                    $unitAnotherStatus,
                    $unitAnotherSum,
                    $unitCloneUnit,
                    $unitDateTime,
                ],
                [
                    'status',
                    'name',
                ],
                [
                    $unit,
                    $unitAnotherStatus,
                ],
            ],
            [
                [
                    $unit,
                    $unitAnotherStatus,
                    $unitAnotherSum,
                    $unitCloneUnit,
                    $unitDateTime,
                ],
                [
                    'status',
                    'sum',
                ],
                [
                    $unit,
                    $unitAnotherStatus,
                    $unitAnotherSum,
                ],
            ],
            [
                [
                    $unit,
                    $unitAnotherStatus,
                    $unitAnotherSum,
                    $unitCloneUnit,
                    $unitDateTime,
                ],
                [],
                [
                    $unit,
                    $unitAnotherStatus,
                    $unitAnotherSum,
                    $unitDateTime,
                ],
            ],
            [
                [
                    $unit,
                    $unitAnotherStatus,
                    $unitAnotherSum,
                    $unitCloneUnit,
                    $unitDateTime,
                ],
                [
                    'sum',
                    'status',
                    'sum',
                    'updated_at',
                ],
                [
                    $unit,
                    $unitAnotherStatus,
                    $unitAnotherSum,
                    $unitDateTime,
                ],
            ],
        ];
    }

    /**
     * Проверка уникализации объектов Unit
     *
     * @param Unit[] $units
     * @param array  $fields
     * @param array  $expectedUnits
     *
     * @dataProvider provideUniqData
     */
    public function testUniq(array $units, array $fields, array $expectedUnits)
    {
        $uniqueUnits = Unit::uniq($units, $fields);
        $this->assertSame($expectedUnits, $uniqueUnits);
    }

    /**
     * Тестирование работы уникализации с комплексным типом данных
     * С учетом поля data и без
     */
    public function testUniqWithBigData()
    {
        $unit = $this->getUnitPrototype();

        $unitWithBigData = $this->getUnitPrototype();
        $unitWithBigData->data = [
            [
                'key1' => 'sddsdsdscdsfsdgadsvfsgvfsza',
                'key2' => 'sdsds',
                'key3' => [
                    $unit
                ],
            ]
        ];

        $uniqueUnits = Unit::uniq(
            [
                $unit,
                $unitWithBigData
            ],
            [
                'status'
            ]
        );
        $this->assertSame([$unit], $uniqueUnits);

        $uniqueUnits = Unit::uniq(
            [
                $unit,
                $unitWithBigData
            ],
            [
                'status',
                'data',
            ]
        );
        $this->assertSame([$unit, $unitWithBigData], $uniqueUnits);
    }

    /**
     * Тестирование уникализации с одинаковыми полями
     */
    public function testSameFields()
    {
        $unit = $this->getUnitPrototype();

        $unitAnotherStatus = $this->getUnitPrototype();
        $unitAnotherStatus->status = 'some another status';

        $uniqueUnits = Unit::uniq([$unitAnotherStatus, $unit], ['status', 'status']);
        $this->assertSame([$unitAnotherStatus, $unit], $uniqueUnits);
    }

    /**
     * Тестирование уникализации с одинаковыми полями
     */
    public function testInvalidFields()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Поля "some_wrong_status, some_wrong_data, some else" отсутствуют в Unit');

        $unit = $this->getUnitPrototype();

        $unitAnotherStatus = $this->getUnitPrototype();
        $unitAnotherStatus->status = 'some another status';

        Unit::uniq([$unitAnotherStatus, $unit], ['some_wrong_status', 'some_wrong_data', 'some else']);
    }

    /**
     * Тестирование с неправильным типом в массиве для уникализации
     */
    public function testInvalidUnits()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Ошибка типа во входящем массиве Unit[]');

        $unit = $this->getUnitPrototype();

        $unitAnotherStatus = $this->getUnitPrototype();
        $unitAnotherStatus->status = 'some another status';

        $invalidUnit = new \stdClass();
        $invalidUnit->status = 'some status';

        Unit::uniq([$unitAnotherStatus, $unit, $invalidUnit], ['status']);
    }

    /**
     * Получение прототипа объекта Unit
     *
     * @return Unit
     */
    private function getUnitPrototype(): Unit
    {
        $unit             = new Unit();
        $unit->id         = 'some id';
        $unit->name       = 'some name';
        $unit->status     = 'some status';
        $unit->sum        = 'some sum';
        $unit->data       = 'some data';
        $unit->updated_at = 'some updated at';

        return $unit;
    }
}
