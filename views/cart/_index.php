<?php
/**
 * @var $this \yii\web\View
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
use kartik\grid\GridView;
use yii\helpers\Html;
use kartik\grid\ActionColumn;

$this->registerJsFile('/js/cart.js', ['depends' => \yii\web\JqueryAsset::className()]);

?>
<h2>Корзина</h2>
<?php echo GridView::widget([
    'dataProvider' => $dataProvider,
    'showPageSummary' => true,
    'columns' => [
            [
                'attribute' => 'article',
                'group' => true,
                'groupFooter'=>function ($model, $key, $index, $widget) {
                    return [
                        'mergeColumns'=>[[0,8]],
                        'content' => [
                                0 => 'Примечание: ' . Html::textInput('memo', $model->memo, ['data_id' => $model->id, 'class' => 'memo']) ,
                        ],
                    ];
                }
            ],
        [
            'label' => 'фото',
            'value' => function($model){
                /** @var $model \app\models\Cart */
                $url = isset($model->product->pictures[0])?$model->product->pictures[0]->getUrl('small'):'';
                if($url){
                    return Html::img($url);
                }
            },
            'format' => 'raw'
        ],
        'product.name',
        [
            'label' => 'Цвет',
            'value' => function($model){
                /** @var $model \app\models\Cart */
                $str = '';
                foreach ($model->details as $detail){
                    if($detail->amount >0){
                        $str .= $detail->color . '<br />';
                    }
                }
                return $str;
            },
            'format' => 'raw'
        ],
        [
                'attribute' =>'product.price',
                'value' => function($model){
                    return Yii::$app->formatter->asCurrency($model->product->price, 'RUR');
                }
        ],
        [
                'attribute' => 'product.pack_quantity',
                'value' => function($model){
                    return $model->product->pack_quantity?$model->product->pack_quantity:1;
                }
        ],
        [
            'label' => 'Кол-во',
            'value' => function($model){
                /** @var $model \app\models\Cart */
                $str = '';
                foreach ($model->details as $detail){
                    if($detail->amount >0){
                        $str .= Html::textInput('color[' . $detail->color .']', $detail->amount, ['class' => 'amount', 'data-id' => $detail->id] ). '<br />';
                    }
                }
                return $str;
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Сумма',
            'value' => function($model){
                $sum = 0;
                foreach ($model->details as $detail){
                    if($detail->amount >0){
                        $sum += $model->product->price * $model->product->pack_quantity * $detail->amount;
                    }
                }
                return Yii::$app->formatter->asCurrency($sum, 'RUR');
            },
            'contentOptions' => ['class' => 'row_sum']
        ],
        [
                'class' => ActionColumn::className(),
                'template' => '{delete}',
            'buttons' => [
                    'delete' => function($url, $model, $key){
                        return Html::a('Удалить', $url, [
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method' => 'post',
                        ]);
                    }
            ]
        ]
    ]
]);
?>
<?php echo Html::a('Оформить', ['/cart/order'], ['class' => 'but']); ?>