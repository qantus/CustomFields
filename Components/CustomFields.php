<?php
/**
 *
 *
 * All rights reserved.
 *
 * @author Okulov Anton
 * @email qantus@mail.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 24/03/15 10:29
 */

namespace Modules\CustomFields\Components;


use Modules\CustomFields\Models\CustomField;
use Modules\CustomFields\Models\CustomManager;

/**
 * Class CustomFields
 * @package Modules\CustomFields\Components
 *
 * @property int id
 * @property array|null custom_data
 */
trait CustomFields
{
    /**
     * Установка значения кастомного поля
     *
     * @param \Modules\CustomFields\Models\CustomField $field
     * @param mixed $value
     */
    public function setCustomValue($field, $value)
    {
        $valueModelClass = $field->getValueClass();
        $valueModel = $valueModelClass::objects()->get([
            'object_id' => $this->id,
            'custom_field_id' => $field->id
        ]);
        if (!$valueModel) {
            $valueModel = new $valueModelClass();
            $valueModel->object_id = $this->id;
            $valueModel->custom_field = $field;
        }
        $valueModel->value = $value;
        $valueModel->save();

        $this->setCustomJson($field, $value);
    }

    /**
     * Запись значения в массив внутри самой модели. Необходимо для вывода данных.
     * Значения будут выводится вообще без дополнительных затрат.
     * Формат - произвольный. Можно хранить что угодно - как удобно именно вам для вывода.
     * Представленный ниже формат подойдет для большинства задач.
     *
     * @param \Modules\CustomFields\Models\CustomField  $field
     * @param mixed $value
     */
    public function setCustomJson($field, $value)
    {
        $data = $this->getJsonCustomData();

        $data[$field->id] = [
            'field_name' => $field->name,
            'value' => $value,
            'human_value' => $field->humanizeValue($value)
        ];

        $this->setJsonCustomData($data);
    }

    /**
     * Получение значения кастомного поля
     *
     * @param $field
     * @return null
     */
    public function getCustomValue($field)
    {
        $id = $field;
        if (is_object($field)) {
            $id = $field->id;
        }
        $data = $this->getJsonCustomData();
        if (isset($data[$id]) && isset($data[$id]['human_value'])) {
            return $data[$id]['human_value'];
        }
        return null;
    }

    public function getJsonCustomData()
    {
        $data = $this->custom_data;
        return is_array($data) ? $data : [];
    }

    public function setJsonCustomData($data)
    {
        $this->custom_data = $data;
    }

    public static function objectsManager($instance = null)
    {
        $className = get_called_class();
        return new CustomManager($instance ? $instance : new $className);
    }

    /**
     * Очистка значений всех кастомных полей (при удалении модели)
     */
    public function clearCustom()
    {
        $valuesModels = CustomField::getValuesModels();
        foreach ($valuesModels as $key => $model) {
            $model::clearObject($this->id);
        }
        $this->setJsonCustomData([]);
    }

    /**
     * Очистка значения кастомного поля (при удалении поля)
     */
    public function clearField($field)
    {
        $data = $this->getJsonCustomData();
        if (isset($data[$field->id])) {
            unset($data[$field->id]);
        }
        $this->setJsonCustomData($data);
    }
} 