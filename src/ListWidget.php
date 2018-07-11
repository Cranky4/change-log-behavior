<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace cranky4\changeLogBehavior;

use cranky4\changeLogBehavior\helpers\CompositeRelationHelper;
use yii\base\Widget;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Inflector;

/**
 * Class ObjectLog
 * @package common\modules\eventLogger\widgets\objectLog
 */
class ListWidget extends Widget
{
    /**
     * @var ActiveRecord
     */
    public $model;
    /**
     * @var ActiveRecord[]
     */
    public $additionalModels = [];

    /**
     * @param $objectType
     * @param $objectId
     * @return ActiveDataProvider
     * @throws \ReflectionException
     */
    public function getEventProvider($objectType, $objectId)
    {
        $query = LogItem::find()->andWhere([
            'relatedObjectType' => $objectType,
            'relatedObjectId' => $objectId,
        ]);

        if (!empty($this->additionalModels)) {
            foreach ($this->additionalModels as $additionalModel) {
                if ($additionalModel) {
                    if (is_array($additionalModel)) {
                        foreach ($additionalModel as $addModel) {
                            $query->orWhere([
                                'relatedObjectType' => CompositeRelationHelper::resolveObjectType($addModel),
                                'relatedObjectId' => $addModel->primaryKey,
                            ]);
                        }
                    } else {
                        $query->orWhere([
                            'relatedObjectType' => CompositeRelationHelper::resolveObjectType($additionalModel),
                            'relatedObjectId' => $additionalModel->primaryKey,
                        ]);
                    }
                }
            }
        }

        // add conditions that should always apply here
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_ASC]],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
    }

    /**
     * @return string|void
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function run()
    {
        $objectType = CompositeRelationHelper::resolveObjectType($this->model);
        $dataProvider = $this->getEventProvider($objectType, $this->model->primaryKey);

        $this->renderProvider($dataProvider);
    }

    /**
     * @param $dataProvider
     * @throws \Exception
     */
    protected function renderProvider($dataProvider)
    {
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => [
                'class' => 'table-responsive',
            ],
            'columns' => [
                [
                    'attribute' => 'createdAt',
                    'format' => 'datetime',
                    'headerOptions' => ['style' => 'width:100px']
                ],
                'type',
                [
                    'format' => 'html',
                    'value' => function (LogItem $model) {
                        if ($data = json_decode($model->data, true)) {
                            $out = '';
                            if ($this->model && $this->model->tableName() != $model->relatedObjectType) {
                                $out = Inflector::humanize($model->relatedObjectType) . ' #' . $model->relatedObjectId
                                    . ": ";
                            }
                            foreach ($data as $fieldName => $val) {
                                if (is_string($val)) {
                                    $out .= $val . '<br>';
                                } else {
                                    if (substr($fieldName, -2) === "At") {
                                        $val[0] = \Yii::$app->formatter->asDatetime($val[0]);
                                        $val[1] = \Yii::$app->formatter->asDatetime($val[1]);

                                        $out .= ($fieldName . ': <span style="color:#ccc">'
                                                . print_r($val[0], true) .
                                                ' => </span>' . print_r($val[1], true)) . '<br>';
                                    } else {
                                        $out .= ($fieldName . ': <span style="color:#ccc">'
                                                . Html::encode(print_r($val[0], true)) .
                                                ' => </span>' . Html::encode(print_r($val[1], true))) . '<br>';
                                    }

                                }
                            }

                            return $out;
                        } else {
                            return Html::encode($model->data);
                        }
                    },
                    'headerOptions' => ['style' => 'width:55%']
                ],
                'userId',
                'hostname',
            ],
        ]);
    }
}
