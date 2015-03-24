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
 * @date 24/03/15 09:39
 */
namespace Modules\CustomFields\Models;

use Mindy\Orm\Fields\ForeignField;
use Mindy\Orm\Fields\IntField;
use Mindy\Orm\Model;

abstract class CustomValue extends Model
{
    public static function getFields() 
    {
        return [
            'custom_field' => [
                'class' => ForeignField::className(),
                'modelClass' => CustomField::className()
            ],
            'object_id' => [
                'class' => IntField::className()
            ]
        ];
    }

    public static function clearObject($object_id)
    {
        self::objects()->filter(['object_id' => $object_id])->delete();
    }
} 