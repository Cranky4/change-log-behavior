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
            if ($diff) {
                $diff = $this->_setLabels($this->_applyExclude($diff));

                \Yii::$app->c4ChangeLog->addLog($owner, serialize($diff));
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