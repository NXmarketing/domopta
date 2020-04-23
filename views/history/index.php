<?php
/* @var $this \yii\web\View */
/* @var $orders \app\models\Order[] */
?>
<div class="content content_flip main__content">
	<div class="container container_fl-wr">
		<div class="content-left">
			<div class="history-main">
				<div class="content__title">ИСТОРИЯ ЗАКАЗОВ</div>
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
				<ul class="history-main__list">
					<div class="table-history">
						<table>
							<thead>
							<tr>

								<th>Дата</th>
								<th>Всего товаров</th>
								<th>Сумма</th>
								<th>подробности заказа</th>
							</tr>
							</thead>
							<?php foreach ($orders as $order): ?>
							<tbody class="history-main__item">
							<tr>

								<td><?php echo Yii::$app->formatter->asDate($order->created_at, 'php:d.m.Y') ?></td>
								<td class="td-left"><?php echo $order->getAmount() ?> <span class="sht">шт</span></td>
								<td class="td-left"><?php echo number_format($order->getSum(), 2, ', <span class="kopeyki">', '') ?></span> <span class="rub">руб.</span></td>
								<td>
									<div class="btn-black history-main__btn-black">
										<a href="/history/detail?id=<?php echo $order->id ?>" class="btn-black__link">Подробности</a>
									</div>
								</td>
							</tr>
							</tbody>
							<?php endforeach; ?>
						</table>
					</div>
				</ul>
			</div>
		</div>
		<div class="content-right">
			<div class="user-btns">
				<div class="content__title">Личный кабинет</div>
				<div class="drop-content content-btn__drop-content drop-content_phone">
					<a href="#" class="drop-content__link" id="cab">
						<div class="drop-content__text">
							<span>Личный кабинет</span>
							<div class="drop-content__icon">
								<svg class="svg drop-content__svg drop-content__svg_arrow2-dn">
									<use xlink:href="/img/sprite-sheet.svg#arrow2-dn"/>
								</svg>
							</div>
						</div>
					</a>
				</div>
				<ul class="user-btns__list">
					<li class="user-btns__item">
						<a href="/cabinet" class="user-btns__link">Мой профиль</a>
					</li>
					<li class="user-btns__item user-btns__item_active">
						<a href="/history" class="user-btns__link">История заказов</a>
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
