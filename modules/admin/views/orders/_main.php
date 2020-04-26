<?php

/**
 * @var $this \yii\web\View
 * @var $order \app\models\Order
 */

use app\models\OrderDetailsSearch;
use yii\grid\GridView;
use yii\helpers\Html;

$model = new OrderDetailsSearch();

?>
<h3>Номер заказа: <?php echo $order->id ?></h3>
<?php if (\Yii::$app->user->identity->role == 'admin') : ?>
    <div class="form-group">
        <?php echo Html::a('Печать', ['print', 'id' => $order->id], ['class' => 'btn btn-success', 'target' => '_blank']) ?>
        <?php echo Html::a('Пересчет цен', ['recount', 'id' => $order->id], ['class' => 'btn btn-success']) ?>
        <?php echo Html::a('Вернуть к последнему перерасчету', ['recountcancel', 'id' => $order->id], ['class' => 'btn btn-success']) ?>
    </div>
<?php endif; ?>

<?php $this->beginBlock('total'); ?>
<?php
$old_sum = $order->getOldSum();
$sum = $order->getSum();
?>
<?php if (is_float($old_sum) && $old_sum != $sum) : ?>
    <s><strong><?php echo Yii::$app->formatter->asDecimal($old_sum, 2); ?></strong></s><br />
<?php endif; ?>
<strong><?php echo Yii::$app->formatter->asDecimal($sum, 2); ?></strong>
<?php $this->endBlock(); ?>

<?php echo GridView::widget([
    'dataProvider' => $model->search($order->id),
    'tableOptions' => [
        'class' => 'table table-bordered'
    ],
    'layout' => '{items}',
    'showFooter' => true,
    'columns' => [
        [
            'attribute' => 'article',
            'footer' => '<strong>Итого:</strong>'
        ],
        'name',
        [
            'attribute' => 'color',
            'format' => 'raw',
            'value' => function ($model) {
                return $model->color == 'default' ? '' : $model->color;
            }
        ],
        'memo',
        [
            'attribute' => 'amount',
            'label' => 'Кол-во',
            'contentOptions' => ['class' => 'text-center'],
        ],
        [
            'attribute' => 'price',
            'format' => 'raw',
            'value' => function ($model) {
                $str = '';
                if ($model->price_old && $model->price_old != $model->price) {
                    $str .= '<s>' . Yii::$app->formatter->asDecimal($model->price_old, 2) . '</s><br />';
                }
                return $str . Yii::$app->formatter->asDecimal($model->price, 2);
            },
            'contentOptions' => ['class' => 'text-right']
        ],
        [
            'attribute' => 'sum',
            'format' => 'raw',
            'value' => function ($model) {
                $str = '';
                if ($model->sum_old && $model->sum_old != $model->sum) {
                    $str .= '<s>' . Yii::$app->formatter->asDecimal($model->sum_old, 2) . '</s><br />';
                }
                return $str . Yii::$app->formatter->asDecimal($model->sum, 2);
            },
            'footer' => $this->blocks['total'],
            'contentOptions' => ['class' => 'text-right'],
            'footerOptions' => ['class' => 'text-right'],
        ],
    ],
    'rowOptions' => function ($model, $key, $index, $grid) {
        return [
            'class' => !$model->flag ? 'absend' : ''
        ];
    }

]); ?>