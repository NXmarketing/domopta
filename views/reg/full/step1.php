<?php

/**
 * Created by PhpStorm.
 * User: resh
 * Date: 23.01.19
 * Time: 12:56
 */
/* @var $this \yii\web\View */
/* @var $user \app\models\User */
/* @var $profile \app\models\Profile */
/* @var $form \yii\widgets\ActiveForm */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>
<div class="content">
	<div class="container reg-step-1">
		<div class="col-sm-4">Предприниматели<br><a href="/reg/full?step=1&type=1">Зарегистрироваться</a></div>
		<div class="col-sm-4">ООО<br><a href="/reg/full?step=1&type=3">Зарегистрироваться</a></div>
		<div class="col-sm-4">Физики<br><a href="/reg/full?step=1&type=2">Зарегистрироваться</a></div>
	</div>

    <div class="container container_fl-wr">

            <div class="register">
                <div class="register-top">
                    <div class="content__title">РЕГИСТРАЦИЯ (Шаг 2 из 3)</div>
                    <div class="register-alert">
                        <p> <strong>Обязательно</strong> ознакомьтесь с Условиями Работы!</p>
                        <button class="register__btn">читать условия работы</button>
                    </div>
                    <!-- /.register-alert -->


                </div>
                <div class="register-bottom">
                    <div class="reg-step2-wrap">
                        <div class="reg-step2-item">
                            <img src="" alt="" class="reg-step2-item-img">
                            <!-- /.reg-step2-item-img -->
                            <div class="reg-step2-item-tetle">Предприниматели (ИП)</div>
                            <!-- /.reg-step2-item-tetlу -->
                            <div class="reg-step2-item-info">
                                Тип цен: Опт
                                <br>
                                По завершению регистрации видны только оптовые цены;
                                <br>
                                При регистрации необходимо будет заполнить регистрационные данные Предпринимателя (ИП);
                                <br>
                                Заказы формируются от 5.000 рублей; <br>
                                Форма оплаты: наличный и безналичный расчет; Отправка заказов осуществляется Транспортными Компаниями;
                                <br>
                                Бесплатная доставка по городам Крыма; Действует система скидок!
                            </div>
                            <!-- /.reg-step2-item-info -->
                            <div class="reg-step2-item-button register__btn">продолжить регистрацию</div>
                            <!-- /.reg-step2-item-button -->
                        </div>
                        <!-- /.reg-step2-item -->
                        <div class="reg-step2-item">
                            <img src="" alt="" class="reg-step2-item-img">
                            <!-- /.reg-step2-item-img -->
                            <div class="reg-step2-item-tetle">Юридические лица (ООО)</div>
                            <!-- /.reg-step2-item-tetlу -->
                            <div class="reg-step2-item-info">
                                Тип цен: Опт
                                <br>
                                По завершению регистрации видны только оптовые цены;
                                <br>
                                При регистрации необходимо будет заполнить регистрационные данные Юридического лица (ООО);
                                <br>
                                Заказы формируются от 10.000 рублей;
                                <br>
                                Форма оплаты: безналичный расчет;
                                <br>
                                Отправка заказов осуществляется Транспортными Компаниями;
                                <br>
                                Бесплатная доставка по городам Крыма; Действует система скидок!
                            </div>
                            <!-- /.reg-step2-item-info -->
                            <div class="reg-step2-item-button register__btn">продолжить регистрацию</div>
                            <!-- /.reg-step2-item-button -->
                        </div>
                        <!-- /.reg-step2-item -->
                        <div class="reg-step2-item">
                            <img src="" alt="" class="reg-step2-item-img">
                            <!-- /.reg-step2-item-img -->
                            <div class="reg-step2-item-tetle">Физические лица</div>
                            <!-- /.reg-step2-item-tetlу -->
                            <div class="reg-step2-item-info">
                                Тип цен: Мелкий Опт
                                <br>
                                По завершению регистрации видны только мелкооптовые цены; <br>
                                При регистрации необходимо будет заполнить только контактную информацию; <br>
                                Заказы формируются от 2.000 рублей;
                                <br>
                                Форма оплаты: наличный и безналичный расчет;
                                <br>
                                Отправка заказов осуществляется Транспортными Компаниями;
                                <br>
                                Действует система скидок!

                            </div>
                            <!-- /.reg-step2-item-info -->
                            <div class="reg-step2-item-button register__btn">продолжить регистрацию</div>
                            <!-- /.reg-step2-item-button -->
                        </div>
                        <!-- /.reg-step2-item -->
                    </div>
                    <!-- /.reg-step2-wrap -->
                </div>
            </div>
</div>