<?php
/**
 * @var $this \yii\web\View
 * @var $cart \app\models\Cart[]
 */
use yii\grid\GridView;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use app\components\Breadcrumbs;

$this->registerJsFile('/js/cart.js', ['depends' => \yii\web\JqueryAsset::className()]);
$this->params['breadcrumbs'][] = 'Корзина';
$total = \app\models\Cart::getAmount();
?>

<div class="content content_flip main__content">
    <div class="container container_fl-wr">
        <div class="content-left">
            <div class="cart-main main__cart-main">
                <div class="content__title">Корзина</div>
                <div class="finger">
                    <div class="finger__icon">
                        <svg class="finger__svg">
                            <use xlink:href="/img/sprite-sheet.svg#finger"/>
                        </svg>
                    </div>
                    <div class="finger__text">
                        Если не видно таблицы целиком сделайте слайд по экрану пальцем вправо или в лево
                    </div>
                </div>
                <div class="cart-main-top">
                    <div class="btn-black form-tovar__btn-black">
                        <?php echo Html::a('Оформить заказ', ['/cart/order'], ['class' => 'btn-black__link order_link']); ?>
                    </div>
                </div>
                <div class="cart-main-middle">
                    <ul class="cart-main__list">
                        <div class="table-cart">
                            <table>
                                <thead>
                                <tr>
                                    <th>Товар</th>
                                    <th>Цвет</th>
                                    <th>Цена (шт)</th>
                                    <th>Шт. в уп.</th>
                                    <th>кол-во</th>
                                    <th>Кол-во шт. всего</th>
                                    <th>сумма</th>
                                    <th>операция</th>
                                </tr>
                                </thead>
                                <tbody class="cart-main__item">
                                <?php foreach ($cart as $item): 
                                if(!$item->product) continue;
                                ?>
                                <tr>
                                    <td rowspan="2" class="first-td">
                                        <div class="photos-tovar__item cart-main__pic">
                                            <a href="<?php echo $item->product->slug ?>" target="_blank">
                                            <img src="<?php echo isset($item->product->pictures[0])?$item->product->pictures[0]->getUrl('small'):'/img/d.jpg' ?>" alt="img" class="photos-tovar__img cart-main__img">
                                            </a>
                                        </div>
                                        <span><?php echo $item->product->article?></span>
                                    </td>
                                    <td colspan="7" class="cart-main__item-desc thcart1"><span><?php echo $item->product->name ?></span></td>
                                </tr>
                                <tr>
                                    <td><?php
	                                    $str = '';
	                                    foreach ($item->details as $detail){
		                                    if($detail->amount >0){
		                                        if($item->product->hasColor($detail->color)){
                                                    $str .= '<span>' . $detail->color . '</span><br />';
                                                } else {
                                                    $str .= '<span class="cart_selled">' . $detail->color . '</span><br />';
                                                }
		                                    }
	                                    }
	                                    echo $str;
                                        ?></td>
                                    <td><?php
	                                    $str = '';
	                                    foreach ($item->details as $detail){
		                                    if($detail->amount >0){
		                                        if(Yii::$app->user->identity->profile->type == 1 || Yii::$app->user->identity->profile->type == 3) {
			                                        $str .= number_format( $item->product->price, 2 , ', <span class="kopeyki">', '') . '</span><br />';
		                                        } elseif (Yii::$app->user->identity->profile->type == 2){
			                                        $str .= number_format( $item->product->price2, 2 , ', <span class="kopeyki">', '') . '</span><br />';
                                                }
		                                    }
	                                    }
	                                    echo $str;
	                                    ?></td>
                                    <td>
                                        <?php $str = '';
                                        foreach ($item->details as $detail){
	                                        if($detail->amount >0){
		                                        $str .= ($item->product->pack_quantity?$item->product->pack_quantity:1) . '<br />';
	                                        }
                                        }
                                        echo $str;
                                        ?>
                                    </td>
                                    <td class="input-td"><?php $str = '';
	                                    foreach ($item->details as $detail){
		                                    if($detail->amount >0){
                                                if($item->product->pack_quantity){
                                                    $shtup = 'уп.';
                                                } else {
                                                    $shtup = 'шт.';
                                                }
			                                    $str .= Html::input('number','color[' . $detail->color .']', $detail->amount, ["min"=>"1",'class' => 'cart-main__amount', 'data-id' => $detail->id] ) . $shtup .'<br />';
		                                    }
	                                    }
	                                    echo $str;
	                                    ?></td>
                                    <td><?php $str = '';
	                                    foreach ($item->details as $detail){
		                                    if($detail->amount >0){
			                                    $str .= '<span data-id="'.$detail->id.'" class="detail-amount">' . ($item->product->pack_quantity?$item->product->pack_quantity:1) * $detail->amount . '</span><br />';
		                                    }
	                                    }
	                                    echo $str;
	                                    ?></td>
                                    <td>
                                        <?php
                                        $str = '';
                                        foreach ($item->details as $detail){
	                                        if($detail->amount >0){
		                                        $quantity = $item->product->pack_quantity?$item->product->pack_quantity:1;
		                                        $str .= '<span class="detail-sum" data-id="'.$detail->id.'" >' . number_format($item->product->getUserPrice() * $quantity * $detail->amount, 2, ', ' , '') . '</span></span><br />';
	                                        }
                                        }
                                        echo $str;
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $str = '';
                                        foreach ($item->details as $detail){
	                                        if($detail->amount >0){
	                                            $fill_class = "";
	                                            if(!$item->product->hasColor($detail->color)){
	                                                $fill_class = "fill_red";
                                                }
		                                        $str .= Html::a('<svg class="svg cart-main-btn__svg cart-main-btn__svg_cross1 '.$fill_class.'">
                                                <use xlink:href="/img/sprite-sheet.svg#cross1"/>
                                            </svg>', ['delete', 'id' => $detail->id], [
				                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
				                                        'data-method' => 'post',
				                                        'title' => 'Удалить из корзины',
				                                        'alt' => 'Удалить из корзины',
                                                        'class' => 'cart-main-btn cart-main-btn__icon'
			                                        ]) . '<br />';
	                                        }
                                        }
                                        echo $str;
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8" class="td-bottom">
                                        <?php echo Html::textInput('memo', $item->memo, ['data-id' => $item->id, 'class' => 'cart-main__comment memo', 'placeholder' => 'Примечание:']) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </ul>
                </div>
                <div class="cart-main-bottom">
                    <div class="cart-main-sum">
                        <span>Общая сумма:</span>
                        <span class="cart-main-num"><?php echo $total['sum'] ?></span>
                        руб.
                    </div>
                    <div class="btn-black form-tovar__btn-black">
	                    <?php echo Html::a('Оформить заказ', ['/cart/order'], ['class' => 'btn-black__link order_link']); ?>
                    </div>

                </div>
            </div>
        </div>
        <div class="content-right">  
            <div class="user-btns">
                <div class="content__title">Личный кабинет</div>
                <ul class="user-btns__list">
                    <li class="user-btns__item">
                        <a href="/cabinet" class="user-btns__link">Мой профиль</a>
                    </li>
                    <li class="user-btns__item">
                        <a href="/history" class="user-btns__link">История покупок</a>
                    </li>
                    <li class="user-btns__item">
                        <a href="/favorites" class="user-btns__link">Избранное</a>
                    </li>
                    <li class="user-btns__item">
                        <a href="/site/logout" class="user-btns__link">Выход</a>
                    </li>
                </ul>
            </div> 
        </div>
    </div>
</div>
<div class="cart_popup_overley"></div>
<div class="container container_pop-small cart_popup">
    <div class="reg-pop-inner reg-pop-inner-cart">
        <div class="reg-pop__step1 reg-pop__step1_block cart_popup_selled">
            В КОРЗИНЕ ЕСТЬ ТОВАРЫ, КОТОРЫЕ УЖЕ ПРОДАНЫ И ОТСУТСТВУЮТ НА СКЛАДЕ.<br /><br />
            ДЛЯ ОФОРМЛЕНИЯ ЗАКАЗА УДАЛИТЕ ОТСУТСТВУЮЩИЕ ТОВАРЫ ИЗ КОРЗИНЫ.<br /><br />
            (данные товары выделены красным цветом)
        </div>
        <a href="#" id="esc" class="esc">
            <div class="esc__icon esc__icon_cross1">
                <svg class="svg esc__svg esc__svg_cross1">
                    <use xlink:href="/img/sprite-sheet.svg#cross1"/>
                </svg>
            </div>
        </a>
    </div>
</div>
