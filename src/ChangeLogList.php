<?php
    /**
     * Created by PhpStorm.
     * User: Cranky4
     * Date: 28.03.2016
     * Time: 12:57
     */

    namespace cranky4\ChangeLogBehavior;

    use yii\base\Exception;
    use yii\base\Model;
    use yii\base\Widget;
    use yii\grid\GridView;
    use yii\helpers\Inflector;

    /**
     * Class ChangesList
     * @package app\widgets
     */
    class ChangeLogList extends Widget
    {
        /**
         * @var Model $model
         */
        public $model;

        public function run()
        {
            $model = $this->model;
            if (!$model) {
                return false;
            }
            if (!$model->hasMethod('getLog')) {
                throw new Exception("Attach ".ChangeLogBehavior::className()." behavior to ".$model::className());
            }

            $logProvider = $model->getLog();

            $view = "<h2>".Inflector::camel2words($model->formName())." change log:</h2>";
            $view .= GridView::widget([
                'dataProvider' => $logProvider,
                'columns'      => [
                    'log_time:datetime',
                    'prefix',
                    [
                        'attribute' => 'message',
                        'content'   => function ($item) {
                            $messages = unserialize($item['message']);
                            if (is_array($messages)) {
                                $message = "";
                                foreach ($messages as $attr => $changes) {
                                    $message .= $attr.": ".$changes."<br>";
                                }

                                return $message;
                            }

                            return null;
                        },
                    ],
                ],
            ]);

            return $view;
        }

    }