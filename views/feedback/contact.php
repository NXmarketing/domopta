								
<?php
/* @var $this \yii\web\View */
/* @var $model \app\models\ContactForm */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="content content_flip main__content">
	<div class="container container_fl-wr">
		<div class="content-left">
			<div class="feedback">
				<div class="feedback-top">
					<div class="content__title"><?php echo $title ?></div> 
				</div>
				<div class="feedback-bottom">
					<div class="feedback-left">
						<div class="feedback__form">
							<?php ActiveForm::begin(['method' => 'post', 'options' => ['enctype' => 'multipart/form-data'], 'id' => 'feedback-form']); ?>
							<div class="feedback__row">
								<?php echo Html::activeTextInput($model, 'name', ['class' => 'feedback__input', 'placeholder' => 'ИМЯ', 'required' => true])?>
								<div class="active-input">ИМЯ</div>
							</div>
							<div class="feedback__row">
								<?php echo Html::activeTextInput($model, 'email', ['class' => 'feedback__input', 'placeholder' => 'E-mail', 'required' => true])?>
								<div class="active-input">E-mail</div>
							</div>
							<div class="feedback__row">
								<?php echo Html::activeTextarea($model, 'body', ['class' => 'feedback__input feedback__input_text', 'placeholder' => 'СОOБЩЕНИЕ', 'required' => true])?>
								<div class="active-input">СООБЩЕНИЕ</div>
							</div>
							<div class="feedback__row">
								<label for="file-input-feed" class="file-input">
									<span class='file-input__icon'>
										<svg class="file-input__svg">
											<use xlink:href="/img/sprite-sheet.svg#attach"/>
										</svg>
									</span>
									<span class="file-input__text1">Загрузить копии документов</span>
								</label>
								<?php echo Html::activeFileInput($model, 'file', ['multiple' => true,'class' => 'feedback__input-file','id' => 'file-input-feed'])?>
							</div>
							<div class="feedback__row">
								<label for="feedback__input-check" class="reg-checkbox <?php echo $model->hasErrors('agree')?'has-error':'' ?>">
									<span class="checkmark">
										<svg class="checkmark__svg checkmark__svg_check">
											<use xlink:href="/img/sprite-sheet.svg#check"/>
										</svg>
									</span>
								</label>
								<label>
                                    <?php echo Html::activeCheckbox($model, 'agree', ['id' => 'feedback__input-check', 'class' => 'checkbox', 'required' => true]) ?>
									<span>Я согласен с обработкой персональных данных и <br><a href="/politika-konfidencialnosti" class="text-link" target="_blank">политикой конфиденциальности</a></span>
								</label>
							</div>
							<div class="feedback__row feedback__row_btn">
								<input class="feedback__btn" type="submit" value="Отправить">
							</div>
							<?php ActiveForm::end(); ?>
						</div>
					</div>
					<div class="feedback-right">
					</div>
				</div>
			</div>
		</div>
		<div class="content-right">

		</div>
	</div>
</div>
<?php if(\Yii::$app->session->hasFlash('contactFormSubmitted') && \Yii::$app->session->getFlash('contactFormSubmitted')): ?>

    <div class="log-pop log-pop_flex"><div class="container container_pop-small">
            <div class="reg-pop-inner reg-pop-inner-cart">
                <div class="reg-pop__step1 reg-pop__step1_block">
                    Сообщение отправлено
                </div>
                <a href="#" id="esc" class="esc">
                    <div class="esc__icon esc__icon_cross1">
                        <svg class="svg esc__svg esc__svg_cross1">
                            <use xlink:href="/img/sprite-sheet.svg#cross1"></use>
                        </svg>
                    </div>
                </a>
            </div>
        </div>
    </div>

<?php endif; ?>
