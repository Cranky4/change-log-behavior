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
                $oldAttrVal = $owner->getAttribute($attrName);
                if ($oldAttrVal != $attrVal) {
                    $diff[$attrName] = $attrVal." => ".$oldAttrVal;
                }
            }
            if ($diff) {
                foreach ($this->excludedAttributes as $attr) {
                    unset($diff[$attr]);
                }
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
    }