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
 * @date 24/03/15 09:19
 */
namespace Modules\CustomFields;

use Mindy\Base\Mindy;
use Mindy\Base\Module;

class CustomFieldsModule extends Module
{
    public $model = null;

    public static function preConfigure()
    {
        Mindy::app()->signal->handler('\Mindy\Orm\Model', 'afterDelete', [self::className(), 'afterDeleteModel']);
    }

    public function getMenu()
    {
        return [
            'name' => $this->getName(),
            'items' => []
        ];
    }

    public static function afterDeleteModel($owner)
    {
        $module = Mindy::app()->getModule('CustomFields');
        if ($module && $module->model && $owner->className() == $module->model) {
            $owner->clearCustom();
        }
    }
}