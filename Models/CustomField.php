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
 * @date 24/03/15 09:25
 */
namespace Modules\CustomFields\Models;

use Mindy\Orm\Fields\CharField;
use Mindy\Orm\Fields\IntField;
use Mindy\Orm\Fields\JsonField;
use Mindy\Orm\Model;
use Modules\CustomFields\Components\CustomFields;
use Modules\CustomFields\CustomFieldsModule;


class CustomField extends Model
{
    const TYPE_STRING = 1;
    const TYPE_DICT = 2;
    const TYPE_INT = 3;
    const TYPE_BOOL = 4;

    public static function getFields() 
    {
        return [
            'name' => [
                'class' => CharField::className(),
                'verboseName' => CustomFieldsModule::t('Name')
            ],
            'type' => [
                'class' => IntField::className(),
                'verboseName' => CustomFieldsModule::t('Type'),
                'default' => self::TYPE_STRING,
                'choices' => [
                    self::TYPE_STRING => CustomFieldsModule::t('String'),
                    self::TYPE_DICT => CustomFieldsModule::t('Dict')
                ]
            ],
            'list' => [
                'class' => JsonField::className(),
                'verboseName' => CustomFieldsModule::t('Available options'),
                'null' => true
            ],
            'external_id' => [
                'class' => CharField::className(),
                'verboseName' => CustomFieldsModule::t('External id'),
                'null' => true
            ],
        ];
    }
    
    public function __toString() 
    {
        return (string) $this->name;
    }

    public function getValueClass()
    {
        $models = $this->getValuesModels();
        if (array_key_exists($this->type, $models)) {
            return $models[$this->type];
        }
        return null;
    }

    public static function getValuesModels()
    {
        return [
            self::TYPE_STRING => CustomValueString::className(),
            self::TYPE_DICT => CustomValueDict::className(),
            self::TYPE_INT => CustomValueInt::className(),
            self::TYPE_BOOL => CustomValueBool::className()
        ];
    }

    public function isDict()
    {
        return $this->type == self::TYPE_DICT;
    }

    public function isBool()
    {
        return $this->type == self::TYPE_BOOL;
    }

    public function humanizeValue($value)
    {
        if ($this->isBool()) {
            if ($value) {
                return CustomFieldsModule::t('Yes');
            } else {
                return CustomFieldsModule::t('No');
            }
        } elseif ($this->isDict()){
            $data = $this->list;
            if (isset($data[$value])) {
                return $data[$value];
            }
        } else {
            return $value;
        }
        return null;
    }

    public function afterDelete($owner)
    {
        parent::afterDelete($owner);
        $valueClass = $this->getValueClass();

        // Очищаем поле из товаров
        $objectsClearPk = $valueClass::objects()->filter(['custom_field' => $this])->valuesList(['object_id'], true);

        $objectClass = $this->getModule()->model;
        $batch = $objectClass::objects()->filter(['id__in' => $objectsClearPk])->batch(300);

        foreach($batch as $models) {
            foreach ($models as $model) {
                $model->clearField($owner);
                $model->save();
            }
        }

        // Очищаем связи
        $valueClass::objects()->filter(['custom_field' => $this])->delete();
    }
}