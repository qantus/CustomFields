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

use Mindy\Orm\Fields\CharField;

class CustomValueString extends CustomValue
{
    public static function getFields() 
    {
        return array_merge(parent::getFields(), [
            'value' => [
                'class' => CharField::className()
            ]
        ]);
    }
} 