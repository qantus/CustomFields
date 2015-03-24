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
 * @date 24/03/15 11:03
 */

namespace Modules\CustomFields\Components;

use Mindy\Orm\QuerySet;
use Modules\CustomFields\Models\CustomField;

class CustomQuerySet extends QuerySet
{
    protected $_filterCustom = [];

    /**
     * Фильтрация QS
     * @param $filter
     * @return $this
     */
    public function filterCustom($filter)
    {
        $this->_filterCustom[] = $filter;
        return $this;
    }

    public function filtrateCustom()
    {
        if (!$this->from) {
            $this->from($this->model->tableName() . ' ' . $this->tableAlias);
            if (empty($this->select)) {
                $this->select($this->tableAlias . '.*');
            }
        }

        $joined = [];
        $alias = $this->getTableAlias();

        foreach ($this->_filterCustom as $filter) {
            foreach ($filter as $id => $values) {
                $fieldCondition = explode('__', $id);
                $condition = isset($fieldCondition[1]) ? $fieldCondition[1] : 'default';
                $id = isset($fieldCondition[0]) ? $fieldCondition[0] : null;
                $field = CustomField::objects()->get(['id' => $id]);

                if ($field && $valueClass = $field->getValueClass()) {

                    $tableName = $valueClass::tableName();

                    $schema = $this->getDb()->getSchema();
                    $table = $schema->getRawTableName($tableName);

                    $fieldAlias = $table . '_' . $field->id;

                    if (!in_array($fieldAlias, $joined)) {
                        $joined[] = $fieldAlias;
                        $this->join('INNER JOIN', $tableName . ' ' . $fieldAlias,  "$alias.id = $fieldAlias.object_id");
                    }

                    $this->andWhere(["$fieldAlias.custom_field_id" => $field->id]);

                    $where = [];
                    if ($condition == 'default') {
                        $where["$fieldAlias.value"] = $values;
                    } elseif ($condition == 'between' && count($values) == 2) {
                        $where = ['between', "$fieldAlias.value", $values[0], $values[1]];
                    } elseif ($condition == 'gte' && !is_array($values)) {
                        $where = ['>=', "$fieldAlias.value", $values];
                    } elseif ($condition == 'lte' && !is_array($values)) {
                        $where = ['<=', "$fieldAlias.value", $values];
                    }

                    $this->andWhere($where);
                }

            }
        }
    }

    protected function prepareConditions($aliased = true, $autoGroup = true)
    {
        parent::prepareConditions($aliased, $autoGroup);
        if ($this->_filterCustom) {
            $this->filtrateCustom();
        }
        return $this;
    }
} 