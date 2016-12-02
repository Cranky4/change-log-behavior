<?php
    /**
     * Created by PhpStorm.
     * User: Cranky4
     * Date: 28.03.2016
     * Time: 12:37
     */

    namespace cranky4\ChangeLogBehavior;

    use yii\base\Behavior;
    use yii\base\Event;
    use yii\data\ArrayDataProvider;
    use yii\db\ActiveRecord;

    class ChangeLogBehavior extends Behavior
    {
        public $excludedAttributes = [];

        public function events()
        {
            return [
                ActiveRecord::EVENT_AFTER_UPDATE => 'addLog',
                ActiveRecord::EVENT_AFTER_INSERT => 'addLog',
            ];
        }

        /**
         * @param \yii\base\Event $event
         */
        public function addLog(Event $event)
        {
            /**
             * @var ActiveRecord $owner
             */
            $owner = $this->owner;
            $changedAttributes = $event->changedAttributes;
            /**
             * @var ChangeLog $component
             */
            $component = \Yii::$app->c4ChangeLog;

            $diff = [];
            foreach ($changedAttributes as $attrName => $attrVal) {
                $newAttrVal = $owner->getAttribute($attrName);
                if ($newAttrVal != $attrVal) {
                    if ($attrVal == '') {
                        $attrVal = 'null';
                    }
                    if ($newAttrVal == '') {
                        $newAttrVal = 'null';
                    }
                    $diff[$attrName] = $attrVal." &raquo; ".$newAttrVal;
                }
            }
            $diff = $this->_applyExclude($diff);

            $function = $component->getSerializeFunction();

            if ($diff) {
                $diff = $this->_setLabels($diff);

                $component->addLog($owner, $function($diff));
            }
        }

        /**
         * @return ArrayDataProvider
         */
        public function getLog()
        {
            /**
             * @var ActiveRecord $owner
             */
            $owner = $this->owner;

            return \Yii::$app->c4ChangeLog->getLog($owner);
        }

        /**
         * @param array $diff
         *
         * @return array
         */
        private function _applyExclude(array $diff)
        {
            foreach ($this->excludedAttributes as $attr) {
                unset($diff[$attr]);
            }

            return $diff;
        }

        /**
         * @param array $diff
         *
         * @return array
         */
        private function _setLabels(array $diff)
        {
            /**
             * @var ActiveRecord $owner
             */
            $owner = $this->owner;

            foreach ($diff as $attr => $msg) {
                unset($diff[$attr]);
                $diff[$owner->getAttributeLabel($attr)] = $msg;
            }

            return $diff;
        }
    }