<?php
/**
 * @var $this \yii\web\View
 * @var $order \app\models\Order
 */
use yii\helpers\Url;
$types = [
    '1' => 'Опт',
    '2' => 'Мелкий Опт',
    '3' => 'Опт',
];
?>
<table style="margin-bottom: 20px;">
    <tr>
        <td style="font-size:13px">
            <span style="font-size: 24px; font-weight: bold">
                <?php echo $order->user->profile->lastname ?> <?php echo $order->user->profile->name ?> <?php echo $order->user->profile->surname ?>
                <span style="font-size: 18px; font-weight: bold">(<?php echo $types[$order->user->profile->type] ?>)</span>
            </span>
            <br />
            Заказ от <?php echo Yii::$app->formatter->asDate($order->created_at, 'php:d.m.Y') ?>
        </td>
    </tr>
</table>
<table style="width: 100%;">
    <tr>
        <td width="50%">
            <table style="vertical-align: top;" width="100%">
                <tr>
                    <td>
                        ИНН ИП: <?php echo $order->user->profile->inn ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $order->user->username ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $order->user->profile->city ?>, <?php echo $order->user->profile->region ?>
                    </td>
                </tr>
                <tr>
                    <td style="text-decoration: underline;">
                        <?php echo $order->user->email ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 18px;">
                        <br />
                        <?php echo $order->user->profile->order_comment ?>
                    </td>
                </tr>
            </table>
        </td>
        <td width="50%" style="vertical-align: top">
            <table width="100%;" style="border: 1px solid grey; border-collapse: collapse; width: 100%;">
                <tr style="text-align: center; font-weight: bold;">
                    <td style="border: 1px solid grey" width="60%">Категория</td>
                    <td style="border: 1px solid grey" width="20%">Кол-во</td>
                    <td style="border: 1px solid grey" width="20%">Сумма</td>
                </tr>
                <?php $details = $order->getDetiles()->joinWith('product')->orderBy(['category_id' => SORT_ASC, 'article_index' => SORT_ASC])->all();
                    $arr = [];
                    $total = 0;
                    $total_o = 0;
                    $total_t = 0;
                    foreach ($details as $detail){
                        //$cat_name = $detail->product->category->parent?$detail->product->category->parent->name . ' - ':'';
                        if(!$detail->product->category){
                            $cat_name = 'Без категории';
                        } else {
                            $cat_name = $detail->product->category->name;
                        }


                        $cat_name = mb_strtoupper($cat_name);
                        if(!isset($arr[$cat_name])){
                            $arr[$cat_name] = [
                                    'amount' => 0,
                                    'sum' => 0
                            ];
                        }
                        $arr[$cat_name]['amount'] = $arr[$cat_name]['amount'] + $detail->amount;
                        $arr[$cat_name]['sum'] = $arr[$cat_name]['sum'] + $detail->sum;
                        $total += $detail->sum;
                        if($detail->product->ooo){
                            $total_o += $detail->sum;
                        } else {
                            $total_t += $detail->sum;
                        }
                    }
                ?>
                <?php foreach ($arr as $k => $v):?>
                    <tr>
                        <td style="border: 1px solid grey; padding-right: 10px;padding-left: 5px;"><?php echo $k; ?></td>
                        <td style="border: 1px solid grey; text-align: center;"><?php echo $v['amount']; ?></td>
                        <td style="border: 1px solid grey; text-align: center;"><?php echo $v['sum']; ?> руб</td>
                    </tr>
                <?php endforeach; ?>
                    <tr>
                        <td style="border: 1px solid grey; text-align: right; padding-right: 10px;">Общая сумма</td>
                        <td style="border: 1px solid grey"></td>
                        <td style="border: 1px solid grey; text-align: center;"><?php echo $total ?> руб</td>
                    </tr>
            </table>
        </td>
    </tr>
</table>
<table style="font-size: 12px; border: 1px solid #333; border-collapse:collapse; margin-top: 20px ;" border="1" cellpadding="4" cellspacing="0" width="100%">
    <tr align="center" style="font-weight: bold;">
        <td width="1%" valign="middle" align="center">№</td>
        <td width="25%" valign="middle" align="center">Товар</td>
        <td width="1%" valign="middle" align="center"></td>
        <td width="10%" valign="middle" align="center">Артикул</td>
        <td width="7%" valign="middle" align="center">Цвет</td>
        <td valign="middle" align="center">Примечание</td>
        <td width="5%" valign="middle" align="center">Шт. в уп.</td>
        <td width="7%" valign="middle" align="center">Цена за уп.</td>
        <td width="7%" valign="middle" align="center">Цена за шт.</td>
        <td width="7%" valign="middle" align="center">Кол-во</td>
        <td width="10%" valign="middle" align="center">Сумма</td>
    </tr>
    <?php $cat = ''; ?>
    <?php foreach ($details as $i => $detail): ?>
        <tr>

        <?php
        if(!$detail->product->category){
            $cat_name = "Без категории";
        } else {
            $cat_name = $detail->product->category->name;
        }
        if($cat_name != $cat):
            $cat = $cat_name;
            ?>
            <td colspan="11" style="padding: 3px;"><?php
                //if($cat->parent){
                    //echo mb_strtoupper($cat->parent->name) . ' - ';
                //}
                echo mb_strtoupper($cat);
                ?></td>
            </tr>
            <tr>
        <?php endif; ?>
            <td align="center"><?php echo $i+1 ?></td>
            <td style="padding: 3px;"><?php echo $detail->name ?></td>
            <td align="center"><?php echo $detail->product->ooo?'О':'Т' ?></td>
            <td><b style="font-size: 13px;"><?php echo $detail->product->article ?></b></td>
            <td><?php echo $detail->color == 'default' ? '' : $detail->color; ?></td>
            <td><?php echo $detail->memo ?></td>
            <td align="center"><?php echo $detail->product->pack_quantity?$detail->product->pack_quantity:1 ?></td>
            <?php if(Yii::$app->user->identity->profile->type == 2): ?>
                <td align="center"><?php echo ($detail->product->pack_price?(number_format($detail->product->pack_price2, 2, ',', '') . ' руб.'):'-') ?></td>
            <?php else: ?>
                 <td align="center"><?php echo ($detail->product->pack_price?(number_format($detail->product->pack_price, 2, ',', '') . ' руб.'):'-') ?></td>
            <?php endif; ?>
            <td align="center"><?php echo number_format($detail->price, 2, ',' , "") ?> руб.</td>
            <td align="center" style="font-weight: bold;"><?php echo $detail->amount ?></td>
            <td align="right" style="padding: 3px;"><?php echo number_format($detail->sum, 2, ',', '') ?> руб.</td>
        </tr>
    <?php endforeach; ?>
</table>
<br />
<div align="right"><strong>Итого: </strong> <span style="letter-spacing: 1px; font-size: 15px;"> <?php echo number_format($total,2, ',', '') ?> руб.</span> </div>
<div align="left">
    <p>Игого, О: <?php echo $total_o ?> руб.</p>
    <p>Игого, Т: <?php echo $total_t ?> руб.</p>
</div>
