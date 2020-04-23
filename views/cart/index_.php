<?php
/**
 * @var $this \yii\web\View
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
use yii\grid\GridView;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use app\components\Breadcrumbs;

$this->registerJsFile('/js/cart.js', ['depends' => \yii\web\JqueryAsset::className()]);
$this->params['breadcrumbs'][] = 'Корзина';
$total = \app\models\Cart::getAmount();
?>
    <main id="cat">
    <div class="wrap inner_product pr">

 
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            'homeLink' => [
                'label' => 'Каталог',
                'url' => '/'
            ],
            'tag' => 'div',
            'options' => ['class' => 'bread'],
            'itemTemplate' => '{link}',
            'activeItemTemplate' => '{link}',
            'glue' => ' > '

        ]) ?>
          <div class="cart_full">
        <div class="cat_desc">
            <?= Yii::$app->params['page']->text; ?>
        </div>

        <table class="tar">
            <tr class="first_tr">
                <td>Артикул</td>
                <td>Фото</td>
                <td>Цвет</td>
                <td>Цена за 1 шт.</td>
                <td>Штук в уп.</td>
                <td>кол-во</td>
                <td>сумма</td>
                <td>удалить</td>
            </tr>
        </table>

<?php echo GridView::widget([
    'tableOptions' => ['class' => 'cart_table', 'id' => 'cart_table'],
    'dataProvider' => $dataProvider,
    'layout' => '{items}',
    'showHeader' => false,
    'rowOptions' => ['class' => 'cart_item'],
    'afterRow' => function($model){
        return '<tr class="prim">
                <td colspan="8">Примечание: '.Html::textInput('memo', $model->memo, ['data-id' => $model->id, 'class' => 'memo']).'</td>
            </tr>';
    },
    'columns' => [
        'article',
        [
            'value' => function($model){
                /** @var $model \app\models\Cart */
                $url = isset($model->product->pictures[0])?$model->product->pictures[0]->getUrl('small'):'/images/gj_146x187.jpg';
                if($url){
                    return Html::img($url);
                }
            },
            'format' => 'raw'
        ],
        [
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
            'value' => function($model){
                $str = '';
                foreach ($model->details as $detail){
                    if($detail->amount >0){
                        $str .= Yii::$app->formatter->asDecimal($model->product->price, 2) . '<br />';
                    }
                }
                return $str;
            },
            'format' => 'raw'
        ],
        [
            'value' => function($model){
                $str = '';
                foreach ($model->details as $detail){
                    if($detail->amount >0){
                        $str .= ($model->product->pack_quantity?$model->product->pack_quantity:1) . '<br />';
                    }
                }
                return $str;
            },
            'format' => 'raw'
        ],
        [
            'value' => function($model){
                $str = '';
                foreach ($model->details as $detail){
                    if($detail->amount >0){
                        $str .= Html::textInput('color[' . $detail->color .']', $detail->amount, ['class' => 'amount', 'data-id' => $detail->id] ) . '<br />';
                    }
                }
                return $str;
            },
            'format' => 'raw'
        ],

        [
            'value' => function($model){
                $str = '';
                foreach ($model->details as $detail){
                    if($detail->amount >0){
                        $quantity = $model->product->pack_quantity?$model->product->pack_quantity:1;
                        $str .= '<span class="detail-sum" data-id="'.$detail->id.'" >' . Yii::$app->formatter->asDecimal($model->product->price * $quantity * $detail->amount, 2) . '</span><br />';
                    }
                }
                return $str;
            },
            'format' => 'raw'
        ],
        [
            'class' => ActionColumn::className(),
            'template' => '{delete}',
            'buttons' => [
                'delete' => function($url, $model, $key){
                    $str = '';
                    foreach ($model->details as $detail){
                        if($detail->amount >0){
                            $str .= Html::a('', ['delete', 'id' => $detail->id], [
                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                'data-method' => 'post',
                                 'title' => 'Удалить из корзины',
                                 'alt' => 'Удалить из корзины',
                            ]) . '<br />';
                        }
                    }
                    return $str;
                }
            ]
        ]
    ]
]);
?>

 </div>

        <hr class="lin">

        <div class="ob_count fr">Общее количество штук: <span id="cart_amount2"><?php echo $total['amount'] ?></span></div>
        <div class="clear"></div>
        <div class="ob_count fr">Общая сумма:<span id="cart_sum2"><?php echo $total['sum'] ?></span></div>
        <div class="clear"></div>
        <?php echo Html::a('Оформить', ['/cart/order'], ['class' => 'ofzak']); ?>
        <div class="clear"></div>

    </div>
</main>
