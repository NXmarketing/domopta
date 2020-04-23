<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\Settings;
 * @var $form ActiveForm;
 */

use yii\bootstrap\ActiveForm;
use dosamigos\tinymce\TinyMce;
use yii\bootstrap\Html;

echo $this->render('menu');
?>
<?php $form = ActiveForm::begin(); ?>
<?php echo $form->field($model, 'adminEmail') ?>
<?php echo $form->field($model, 'sellEmail') ?>
<?php echo $form->field($model, 'phone_call') ?>
<?php echo $form->field($model, 'phone_order') ?>
<?php echo $form->field($model, 'phone_admin') ?>
<?php echo $form->field($model, 'phone') ?>
<?php echo $form->field($model, 'social_instagram') ?>
<?php echo $form->field($model, 'social_vk') ?>
<?php echo $form->field($model, 'social_viber') ?>
<?php echo $form->field($model, 'social_whatsapp') ?>
<?php echo $form->field($model, 'addresses')->widget(TinyMce::className(),[
	'options' => ['rows' => 6],
	'language' => 'ru',
	'clientOptions' => Yii::$app->params['clientOptions']
]); ?>
<?php echo $form->field($model, 'time')->widget(TinyMce::className(),[
	'options' => ['rows' => 6],
	'language' => 'ru',
	'clientOptions' => Yii::$app->params['clientOptions']
]); ?>
<?php echo $form->field($model, 'schema')->widget(TinyMce::className(),[
	'options' => ['rows' => 6],
	'language' => 'ru',
	'clientOptions' => Yii::$app->params['clientOptions']
]); ?>
<?php echo $form->field($model, 'rekvizit')->widget(TinyMce::className(),[
	'options' => ['rows' => 6],
	'language' => 'ru',
	'clientOptions' => Yii::$app->params['clientOptions']
]); ?>
<?php echo $form->field($model, 'footer')->widget(TinyMce::className(),[
    'options' => ['rows' => 6],
    'language' => 'ru',
    'clientOptions' => Yii::$app->params['clientOptions']
]); ?>
<div class="form-group">
    <?php echo Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
</div>
<?php ActiveForm::end(); ?>
