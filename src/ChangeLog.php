<?php
    /**
     * Created by PhpStorm.
     * User: Cranky4
     * Date: 29.03.2016
     * Time: 9:58
     */

    namespace cranky4\ChangeLogBehavior;

    use yii\base\Component;
    use yii\data\ArrayDataProvider;
    use yii\db\ActiveRecord;
    use yii\db\Query;

    /**
     * Class ChangeLog
     * @package cranky4\ChangeLogBehavior
     */
    class ChangeLog extends Component
    {
        /**
         * @var string
         */
        public $prefix = 'changelog';
        public $showLimit = 20;

        /**
         * @param \yii\db\ActiveRecord $model
         *
         * @return string
         */
        protected function getCategory(ActiveRecord $model)
        {
            if (!$model->isNewRecord) {
                $id = $model->id;
                $category = $this->prefix.'.'.$model->formName().'_'.$id;
            } else {
                $category = $this->prefix.'.'.$model->formName();
            }

            return $category;
        }

        /**
         * @param \yii\db\ActiveRecord $model
         * @param string $message
         */
        public function addLog(ActiveRecord $model, $message)
        {
            $this->initLogger();
            \Yii::info($message, $this->getCategory($model));
        }

        /**
         * @param $model
         *
         * @return \yii\data\ArrayDataProvider
         */
        public function getLog(ActiveRecord $model)
        {
            $provider = new ArrayDataProvider([
                'allModels'  => (new Query())->select('log_time, prefix, message')
                    ->where(['category' => $this->getCategory($model)])
                    ->from('{{%changelogs}}')
                    ->orderBy(['log_time' => SORT_ASC])
                    ->limit($this->showLimit)
                    ->all(),
                'pagination' => false,
            ]);

            return $provider;
        }

        private function initLogger()
        {
        }

    }