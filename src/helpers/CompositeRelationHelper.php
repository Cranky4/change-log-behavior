<?php

namespace cranky4\changeLogBehavior\helpers;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

/**
 * Class for working with relations consisting of two keys - object type and id
 * (e.g. when you store "outgoing" or "incoming" in type and id of outgoing/incoming AR in separate columns in
 * target object)
 * @package common\helpers
 */
class CompositeRelationHelper
{
    /**
     * @param ActiveRecord $model
     *
     * @return string
     * @throws \ReflectionException
     */
    public static function resolveObjectType(ActiveRecord $model)
    {
        $reflection = new \ReflectionClass($model);
        return $reflection->getShortName();
    }

    /**
     * @param        $objectType
     * @param        $objectId
     * @param string $namespace
     *
     * @return ActiveRecord
     */
    public static function relatedObject($objectType, $objectId, $namespace = 'common\\models')
    {
        if (!$objectType) {
            return null;
        }

        return self::relatedObjectQuery($objectType, $objectId, $namespace)->one();
    }

    /**
     * @param        $objectType
     * @param        $objectId
     * @param string $namespace
     *
     * @return ActiveQuery
     */
    public static function relatedObjectQuery($objectType, $objectId, $namespace = 'common\\models')
    {
        if (!$objectType) {
            return null;
        }

        $class = Inflector::classify($objectType);

        /** @var ActiveRecord $className */
        $className = rtrim($namespace, '\\') . "\\" . $class;
        $query = $className::find()->andWhere(['id' => $objectId]);

        return $query;
    }

    /**
     * @param $objectType
     * @param $objectId
     *
     * @return array
     */
    public static function relatedObjectLink($objectType, $objectId)
    {
        $path = Inflector::camel2id(Inflector::camelize($objectType));
        $path .= '/view';

        return ['/' . $path, 'id' => $objectId];
    }

    /**
     * @param $objectType
     * @param $objectId
     *
     * @return string
     */
    public static function relatedObjectName($objectType, $objectId)
    {
        $class = Inflector::classify($objectType);
        $objectName = $class . ' #' . $objectId;

        return $objectName;
    }
}
