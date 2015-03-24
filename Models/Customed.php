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
 * @date 24/03/15 09:21
 */
namespace Modules\CustomFields\Models;

use Mindy\Orm\Fields\CharField;
use Mindy\Orm\Fields\JsonField;
use Mindy\Orm\Model;
use Modules\CustomFields\Components\CustomFields;

class Customed extends Model
{
    use CustomFields;

    public static function getFields() 
    {
        return [
            'name' => [
                'class' => CharField::className(),
            ],
            'custom_data' => [
                'class' => JsonField::className(),
                'null' => true
            ]
        ];
    }

    public function __toString()
    {
        return (string) $this->name;
    }
} 