# Changelog Behavior (v.0.0.5)

Simple behavior for your yii2-models 

## Installation

1- Install package via composer:
```
composer require cranky4/change-log-behavior "*"
```
2- Add ChangeLog component to your project config file:
```php
'c4ChangeLog' => [
    'class' => 'cranky4\ChangeLogBehavior\ChangeLog',
],
```
3- Run migrations:
```
yii migrate --migrationPath=@vendor/cranky4/change-log-behavior/src/migrations
```

## Usage

1- Add *ChangeLogBehavior* to any model or active record:
```php
public function behaviors()
{
    return [
        ...
        [
            'class' => ChangeLogBehavior::className(),
            'excludedAttributes' => ['updated_at'],
        ],
        ...
    ];
}
```
__Attention:__ Behavior watches to "safe" attributes only.
Add attributes into *excludedAttributes* if you don't want to log 
its changes.

2- Configure *log* component:
```php
    'components' => [
        ...
        'log'         => [
            'targets'    => [
                // add new target
                [
                    'class'      => 'yii\log\DbTarget',
                    'categories' => ['cranky4\ChangeLogBehavior\ChangeLog:*'],
                    'logTable'   => '{{%changelogs}}',
                    //remove application category from logging
                    'logVars'    => [],
                    'levels'     => ['info'],
                ],
            ],
        ],
        ...
    ],
```
You can add other targets are to catch logs changes, but *categories*, *logTable* and *level* must be same as in the code above.

3- Add *ChangeLogList* to view:
```php
 echo ChangeLogList::widget([
     'model' => $model,
 ])
```

4- Add custom log:
```php
// Use serialize() if you want to add array. Use pairs [attrName => message, attrName2 => message]:
\Yii::$app->c4ChangeLog->addLog($model, serialize($array));
// If you want to add text to log use: 
\Yii::$app->c4ChangeLog->addLog($model, $someText);
```

5- OPTIONAL: You may use `json_encode` instead `serialize`:
```php
    'c4ChangeLog' => [
        'class' => 'cranky4\ChangeLogBehavior\ChangeLog',
        'serializer' => 'json'
    ],
```

### Example
App config *config/main.php*
```php
return [
    ...
    
    'components' => [
        ...
        'log'         => [
            [
                'class'      => 'yii\log\DbTarget',
                'categories' => ['cranky4\ChangeLogBehavior\ChangeLog:*'],
                'logTable'   => '{{%changelogs}}',
                //remove application category from logging
                'logVars'    => [],
                'levels'     => ['info'],
            ],
        ],
        'c4ChangeLog' => [
            'class' => 'cranky4\ChangeLogBehavior\ChangeLog',
        ],
        ...
    ],
    ...
];
```

Model *Post*
```php
/**
 * @propertu int id
 * @property int created_at
 * @property int updated_at
 * @property string title
 * @property int rating
 */
class Post extends yii\db\ActiveRecord {
    
    /**
     *  @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => ChangeLogBehavior::className(),
                'excludedAttributes' => ['created_at','updated_at'],
            ]
        ];
    }
    
}
```
View *post/view.php*
```php
use cranky4\ChangeLogBahavior\ChangeLogList;
use app\models\Post;

/**
 *  @var Post $model
 */
echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'title',
        'rating',
        'created_at:datetime',
        'updated_at:datetime',
    ],
]);

echo ChangeLogList::widget([
    'model' => $model,
]);

```
