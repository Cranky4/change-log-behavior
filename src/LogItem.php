<?php

namespace cranky4\changeLogBehavior;

use cranky4\changeLogBehavior\helpers\CompositeRelationHelper;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "log_event".
 *
 * @property integer $id
 * @property string $relatedObjectType
 * @property integer $relatedObjectId
 * @property string $data
 * @property string $createdAt
 * @property string $type
 * @property string $descr
 * @property integer $userId
 * @property \yii\db\ActiveQuery $user
 * @property string $hostname
 *
 * example of log event creation:
 *          $model =    $this->findModel($id);
 *          $event = new Event;
 *          $event->type  = 'user_view';
 *          $event->relatedObject = $model;
 *          $event->save(false);
 */
class LogItem extends ActiveRecord
{
    /**
     * @var ActiveRecord
     */
    public $relatedObject;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%changelogs}}';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['relatedObjectId', 'userId'], 'integer'],
            //[['data'], 'string'],
            [['createdAt', 'relatedObject', 'data'], 'safe'],
            [['relatedObjectType', 'type', 'hostname'], 'string', 'max' => 255],
            [['descr'], 'string', 'max' => 10000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'relatedObjectType' => 'Related Object Type',
            'relatedObjectId' => 'Related Object ID',
            'data' => 'Data',
            'createdAt' => 'Created At',
            'type' => 'Type',
            'descr' => 'Descr',
            'userId' => 'User ID',
            'hostname' => 'Hostname',
        ];
    }

    /**
     * @param bool $insert
     *
     * @return bool
     * @throws \ReflectionException
     */
    public function beforeSave($insert)
    {
        if (empty($this->userId)) {
            if (!\Yii::$app->user->isGuest) {
                $this->userId = \Yii::$app->user->id;
            } else {
                $this->userId = 0;
            }
        }

        if (empty($this->hostname) && \Yii::$app->request->hasMethod('getUserIP')) {
            $this->hostname = \Yii::$app->request->getUserIP();
        }

        if (!empty($this->data) && is_array($this->data)) {
            $this->data = json_encode($this->data);
        }

        if ($this->relatedObject) {
            $this->relatedObjectType = CompositeRelationHelper::resolveObjectType($this->relatedObject);
            $this->relatedObjectId = $this->relatedObject->primaryKey;
        }

        return parent::beforeSave($insert);
    }
}
