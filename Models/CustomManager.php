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
 * @date 24/03/15 11:02
 */
namespace Modules\CustomFields\Models;

use Mindy\Orm\Manager;
use Modules\CustomFields\Components\CustomQuerySet;

class CustomManager extends Manager
{
    /**
     * @return \Modules\CustomFields\Components\CustomQuerySet
     */
    public function getQuerySet()
    {
        if ($this->_qs === null) {
            $this->_qs = new CustomQuerySet([
                'model' => $this->getModel(),
                'modelClass' => $this->getModel()->className()
            ]);
        }
        return $this->_qs;
    }

    public function filterCustom($filter)
    {
        return $this->getQuerySet()->filterCustom($filter);
    }
}