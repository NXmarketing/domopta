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
<div class="content content_flip main__content">
	<div class="container container_fl-wr">
		<div class="content-left">
			<div class="register">
				<div class="register-top">
					<div class="content__title">Мой профиль</div>
				</div>
				<div class="profile-bottom">
					<div class="profile-left">
						<div class="profile__form">
							<?php $form = ActiveForm::begin([
								'options' => ['enctype' => 'multipart/form-data']
							]); ?>
							<?php echo Html::activeHiddenInput($profile, 'type') ?>

							<div class="profile__row">
								<?php echo Html::activeTextInput($profile, 'lastname', ['readonly' => true , 'placeholder' => 'Фамилия', 'class' => 'profile__input ' . ($profile->hasErrors('lastname')?'has-error':'')]) ?>
                            	<div class="active-input">Фамилия</div>
								<?php if ($err = $profile->getErrors('lastname')): ?>
									<?php echo $err[0] ?>
								<?php endif; ?>
							</div>

							<div class="profile__row">
								<?php echo Html::activeTextInput($profile, 'name', ['readonly' => true , 'placeholder' => 'Имя', 'class' => 'profile__input ' . ($profile->hasErrors('name')?'has-error':'')]) ?>
								<div class="active-input">Имя</div>
								<?php if ($err = $profile->getErrors('name')): ?>
									<?php echo $err[0] ?>
								<?php endif; ?>
							</div>

							<div class="profile__row">
								<?php echo Html::activeTextInput($profile, 'surname', ['readonly' => true , 'placeholder' => 'Отчество', 'class' => 'profile__input ' . ($profile->hasErrors('surname')?'has-error':'')]) ?>
								<div class="active-input">Отчество</div>
								<?php if ($err = $profile->getErrors('surname')): ?>
									<?php echo $err[0] ?>
								<?php endif; ?>
							</div>

							<div class="profile__row">
								<?php echo Html::activeTextInput($profile, 'region', ['readonly' => true , 'placeholder' => 'Регион', 'class' => 'profile__input ' . ($profile->hasErrors('region')?'has-error':'')]) ?>
								<div class="active-input">Регион</div>
								<?php if ($err = $profile->getErrors('region')): ?>
									<?php echo $err[0] ?>
								<?php endif; ?>
							</div>
							<div class="profile__row">
								<?php echo Html::activeTextInput($profile, 'city', ['readonly' => true , 'placeholder' => 'Город', 'class' => 'profile__input ' . ($profile->hasErrors('city')?'has-error':'')]) ?>
								<div class="active-input">Город</div>
								<?php if ($err = $profile->getErrors('city')): ?>
									<?php echo $err[0] ?>
								<?php endif; ?>
							</div>
							<div class="profile__row">
								<?php echo Html::activeTextInput($profile, 'location', ['readonly' => true , 'placeholder' => 'Адрес', 'class' => 'profile__input ' . ($profile->hasErrors('location')?'has-error':'')]) ?>
								<div class="active-input">Адрес</div>
								<?php if ($err = $profile->getErrors('location')): ?>
									<?php echo $err[0] ?>
								<?php endif; ?>
							</div>
							<div class="profile__row">
								<?php echo Html::activeTextInput($profile, 'organization_name', ['readonly' => true , 'placeholder' => 'Название организации', 'class' => 'profile__input ' . ($profile->hasErrors('organization_name')?'has-error':'')]) ?>
								<div class="active-input">Название организации</div>
								<?php if ($err = $profile->getErrors('organization_name')): ?>
									<?php echo $err[0] ?>
								<?php endif; ?>
							</div>

							<div class="profile__row">
								<?php echo Html::activeTextInput($profile, 'inn', ['readonly' => true , 'placeholder' => 'ИНН', 'class' => 'profile__input ' . ($profile->hasErrors('inn')?'has-error':'')]) ?>
								<div class="active-input">ИНН</div>
								<?php if ($err = $profile->getErrors('inn')): ?>
									<?php echo $err[0] ?>
								<?php endif; ?>
							</div>
							<div class="profile__row">
								<?php echo Html::activeTextInput($user, 'username', ['readonly' => true , 'placeholder' => 'Телефон', 'class' => 'profile__input ' . ($user->hasErrors('username')?'has-error':'')]) ?>
								<div class="active-input">Телефон</div>
								<?php if ($err = $profile->getErrors('username')): ?>
									<?php echo $err[0] ?>
								<?php endif; ?>
							</div>
                            <div class="profile__input-name">Сменить E-mail</div>
							<div class="profile__row">
								<?php echo Html::activeTextInput($user, 'email', ['placeholder' => 'Email', 'class' => 'profile__input ' . ($user->hasErrors('email')?'has-error':'')]) ?>
								<div class="active-input">Email</div>
								<?php if ($err = $profile->getErrors('email')): ?>
									<?php echo $err[0] ?>
								<?php endif; ?>
                                <?php if($user->email != '' && $user->unconfirmed_email): ?>
                                <p class="not-red">E-mail подтвержден. Если хотите свой e-mail изменить, то введите новый e-mail.</p>
                                <?php elseif($user->email != ''): ?>
                                    <p class="red">Email не подтвержден. <a href="/cabinet/resend">Выслать письмо повторно?</a></p>
                                <?php else: ?>
                                    <p class="not-red">Если Вы хотите получать на почту свои заказы, то Вам необходимо ввести свой e-mail.</p>
                                <?php endif; ?>
							</div>
                            <div class="profile__input-name">Сменить пароль</div>
							<div class="profile__row">
								<?php echo Html::activePasswordInput($user, 'password', ['placeholder' => 'Пароль', 'minlength' => 6, 'class' => 'profile__input ' . ($user->hasErrors('password')?'has-error':'')]) ?>
								<div class="active-input">Пароль</div>
                                <span id="show_password2" class="show_password">
					<span class="eye-icon">
						<svg class="svg product__svg product__svg_eye">
							<use xlink:href="/img/sprite-sheet.svg#eye"/>
						</svg>
						<span class="line"></span>
					</span>
				</span>
								<?php if ($err = $profile->getErrors('password')): ?>
									<?php echo $err[0] ?>
								<?php endif; ?>
							</div>
							<div class="profile__row">
								<?php echo Html::activePasswordInput($user, 'password_repeat', ['placeholder' => 'Подтвердите пароль', 'class' => 'profile__input ' . ($user->hasErrors('password_repeat')?'has-error':'')]) ?>
								<div class="active-input">Подтвердите пароль</div>
                                <span id="show_password3" class="show_password">
					<span class="eye-icon">
						<svg class="svg product__svg product__svg_eye">
							<use xlink:href="/img/sprite-sheet.svg#eye"/>
						</svg>
						<span class="line"></span>
					</span>
				</span>
								<?php if ($err = $profile->getErrors('password_repeat')): ?>
									<?php echo $err[0] ?>
								<?php endif; ?>
                                
							</div>
							<div class="profile__row profile__row_btn">
								<input class="profile__btn" type="submit" value="Сохранить">
							</div>

							<?php ActiveForm::end() ?>
						</div>
					</div>
					<div class="profile-right">
						<div class="price-category">
							<div class="price-category__heading">Ваша ценовая категория</div>
							<div class="price-category__text1">Для Вас установлена ценовая категория</div>
							<?php if($profile->type == 2): ?>
							<div class="price-category__text2">Мелкий опт</div>
							<?php elseif ($profile->type == 1 || $profile->type == 3): ?>
								<div class="price-category__text2">Опт</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="content-right">
			<div class="user-btns">
				<div class="content__title">Личный кабинет</div>
				<div class="drop-content content-btn__drop-content drop-content_phone">
					<a href="#" class="drop-content__link" id="cab">
						<div class="drop-content__text">
							<span>Личнй кабинет</span>
							<div class="drop-content__icon">
								<svg class="svg drop-content__svg drop-content__svg_arrow2-dn">
									<use xlink:href="/img/sprite-sheet.svg#arrow2-dn"/>
								</svg>
							</div>
						</div>
					</a>
				</div>
                <ul class="user-btns__list">
                    <li class="user-btns__item user-btns__item_active">
                        <a href="/cabinet" class="user-btns__link">Мой профиль</a>
                    </li>
                    <li class="user-btns__item">
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
<?php if(Yii::$app->session->getFlash('reg-success')): ?>
    <div class="log-pop log-pop_flex"><div class="container container_pop-small">
            <div class="reg-pop-inner reg-pop-inner-fav reg-pop-inner-success">
                <div class="reg-pop__step1 reg-pop__step1_block">
                    <p class="popup-text1">Регистрация на сайте завершена.</p><p class="popup-text2">Добро пожаловать на сайт!</p>
                </div>
                <a href="#" id="esc" class="esc">
                    <div class="esc__icon esc__icon_cross1">
                        <svg class="svg esc__svg esc__svg_cross1">
                            <use xlink:href="/img/sprite-sheet.svg#cross1"></use>
                        </svg>
                    </div>
                </a>
            </div>
        </div></div>
<?php endif; ?>
<?php if(Yii::$app->session->getFlash('save_success')): ?>
    <div class="log-pop log-pop_flex"><div class="container container_pop-small">
            <div class="reg-pop-inner reg-pop-inner-fav reg-pop-inner-success">
                <div class="reg-pop__step1 reg-pop__step1_block">
                    <p class="popup-text1">Изменения сохранены.</p>
                </div>
                <a href="#" id="esc" class="esc">
                    <div class="esc__icon esc__icon_cross1">
                        <svg class="svg esc__svg esc__svg_cross1">
                            <use xlink:href="/img/sprite-sheet.svg#cross1"></use>
                        </svg>
                    </div>
                </a>
            </div>
        </div></div>
<?php endif; ?>
