<?php
/**
 * Created by PhpStorm.
 * User: cranky4
 * Date: 04/09/2017
 * Time: 13:25
 */

namespace cranky4\changeLogBehavior\traits;

use cranky4\changeLogBehavior\LogItem;

/**
 * If you want to use change log with ActiveRecord::updateAttributes()
 *
 * Trait UpdateAttributes
 * @package common\modules\eventLogger\components
 */
trait UpdateAttributes
{
    /**
     * @param array $attributes
     * @param string $type
     *
     * @return int
     */
    public function updateAttributes($attributes, $type = null)
    {
        $diff = [];
        $changeLogBehavior = $this->getBehavior('changeLog');
        foreach ($attributes as $attribute => $value) {
            if ($changeLogBehavior && in_array($attribute, $changeLogBehavior->excludedAttributes)) {
                continue;
            }
            $old = $this->getOldAttribute($attribute);
            if ($old != $value) {
                $diff[$attribute] = [$old, $value];
            }
        }

        $diff = $this->applyExclude($diff);

        if ($diff) {
            $logEvent = new LogItem();
            $logEvent->relatedObject = $this;
            $logEvent->data = $diff;
            $logEvent->type = $type;
            $logEvent->save();
        }

        return parent::updateAttributes($attributes);
    }

    /**
     * @param array $diff
     *
     * @return array
     */
    private function applyExclude(array $diff)
    {
        $bahavior = $this->getBehavior('changeLog');

        if (!$bahavior) {
            return $diff;
        }

        foreach ($bahavior->excludedAttributes as $attr) {
            unset($diff[$attr]);
        }

        return $diff;
    }
}
