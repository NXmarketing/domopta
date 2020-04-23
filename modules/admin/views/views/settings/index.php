<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\Settings;
 * @var $form ActiveForm;
 */

use yii\bootstrap\ActiveForm;
use dosamigos\tinymce\TinyMce;
use yii\bootstrap\Html;
echo $this->render('../_alert', ['module' => Yii::$app->getModule('user')]);
echo $this->render('menu');
?>
<?php $form = ActiveForm::begin(); ?>
<?php echo $form->field($model, 'addresses')->widget(TinyMce::className(),[
    'options' => ['rows' => 6],
    'language' => 'ru',
    'clientOptions' => [
        'plugins' => [
            "advlist autolink lists link charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste "
        ],
        'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code"
    ]
]); ?><?php echo $form->field($model, 'contacts')->widget(TinyMce::className(),[
    'options' => ['rows' => 6],
    'language' => 'ru',
    'clientOptions' => [
        'plugins' => [
            "advlist autolink lists link charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste "
        ],
        'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code"
    ]
]); ?>
<div class="form-group">
    <?php echo Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
</div>
<?php ActiveForm::end(); ?>
