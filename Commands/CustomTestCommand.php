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
 * @date 24/03/15 10:05
 */
namespace Modules\CustomFields\Commands;

use Mindy\Console\ConsoleCommand;
use Modules\CustomFields\Models\Customed;
use Modules\CustomFields\Models\CustomField;

class CustomTestCommand extends ConsoleCommand
{
    public $time;

    public function createField()
    {
        $field = new CustomField();
        $field->name = 'Питание';
        $field->type = CustomField::TYPE_DICT;
        $field->list = [
            '220' => '220 вольт',
            '380' => '380 вольт'
        ];
        $field->external_id = 'PIT';
        $field->save();
    }

    public function deleteField()
    {
        $field = CustomField::objects()->get(['id' => 3]);
        $field->delete();
    }

    public function actionIndex()
    {
        $this->time = time();

        $this->deleteField();
//        $this->createField();

//        $field = CustomField::objects()->get(['id' => 3]);
//
//        $count = 10000;
//        while ($count > 0) {
//            $model = new Customed();
//            $model->name = $count;
//            if ($model->save()) {
//                $model->setCustomValue($field, "220");
//                $model->save();
//            };
//            $count--;
//        }
//
//        var_dump($model->getCustomValue($field));
//
//        $qs = Customed::objects()->getQuerySet();
//
//        $qs->filter(['id' => 1]);
//        $filtrated = $qs->filterCustom([1 => ['220', '380'], '1__between' => [0, 400]]);
//
//        $data = $filtrated->all();
//        $model->delete();

        echo (time() - $this->time) . ' S' . PHP_EOL;
        echo number_format(memory_get_peak_usage(), 0, '.', ' ');
    }
}