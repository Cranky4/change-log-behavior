<?php
    /**
     * Created by PhpStorm.
     * User: Cranky4
     * Date: 29.03.2016
     * Time: 9:58
     */

    namespace cranky4\ChangeLogBehavior;

    use yii\base\Component;
    use yii\base\Model;
    use yii\data\ArrayDataProvider;
    use yii\db\ActiveRecord;
    use yii\db\Query;
    use yii\helpers\Json;

    /**
     * Class ChangeLog
     * @package cranky4\ChangeLogBehavior
     */
    class ChangeLog extends Component
    {
        /**
         * @var string
         */
        public $showLimit = 20;

        /**
         * @var string
         */
        public $serializer = 'serialize';
        /**
         * @var string
         */
        public $logCategory = 'changelog';

        /**
         * @param \yii\base\Model $model
         *
         * @return string
         */
        protected function getCategory(Model $model)
        {
            if (!$model->isNewRecord) {
                $id = $model->id;
                $category = $this->logCategory.":".$model->formName().'-'.$id;
            } else {
                $category = $this->logCategory.":".$model->formName();
            }

            return $category;
        }

        /**
         * @param ActiveRecord $model
         * @param string $message
         */
        public function addLog(ActiveRecord $model, $message)
        {
            if(is_array($message)) {
                $message = $this->serialize($message);
            }
            \Yii::info($message, $this->getCategory($model));
        }

        /**
         * @param $model
         *
         * @return ArrayDataProvider
         */
        public function getLog(ActiveRecord $model)
        {
            return new ArrayDataProvider([
                'allModels'  => (new Query())->select('log_time, prefix, message')
                    ->where(['category' => $this->getCategory($model)])
                    ->from('{{%changelogs}}')
                    ->orderBy(['log_time' => SORT_ASC])
                    ->limit($this->showLimit)
                    ->all(),
                'pagination' => false,
            ]);
        }

        /**
         * @return string
         */
        public function serialize($data)
        {
            if ($this->serializer == 'json') {
                return Json::encode($data);
            }

            return serialize($data);
        }

        /**
         * @return string
         */
        public function unserialize($data)
        {
            try {
                if ($this->serializer == 'json') {
                    return Json::decode($data);
                }

                return unserialize($data);
            } catch (\Exception $e) {
                return $data;
            }
        }
    }


