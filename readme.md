# Changelog Behavior

Simple behavior for your yii2-models

## Installation

1. Install package via composer:
```
composer require cranky4/ChangeLogBehavior "*"
```
2. Add ChangeLog component to your project config file:
```php
'changeLog' => [
    'class' => 'cranky4\ChangeLogBehavior\ChangeLog',
],
```
3. Run migrations:
```
yii migrate --migrationPath=@vendor/cranky4/migrations
```

## Usage

1. Add *ChangeLogBehavior* to any model or active record:
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
Add attributes to *excludedAttributes* if you don't want to log 
its changes.

2. Configure *log* component:
```php
    'components' => [
        ...
        'log'         => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'      => 'yii\log\DbTarget',
                    'categories' => ['changelog.*'],
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

3. Add *ChangeLogList* to view:
```php
 echo ChangeLogList::widget([
     'model' => $model,
 ])
```

### Example
App config *config/main.php*
```php
return [
    ...
    
    'components' => [
        ...
        'log'         => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'      => 'yii\log\DbTarget',
                    'categories' => ['changelog.*'],
                    'logTable'   => '{{%changelogs}}',
                    //remove application category from logging
                    'logVars'    => [],
                    'levels'     => ['info'],
                ],
            ],
        ],
        'logChanges' => [
            'class' => \common\components\logChanges\LogChanges::className(),
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