<?php
use yii\bootstrap\Nav;
?>
<div class="row">
<div class="col-md-12">

<?php echo Nav::widget([
    'items' => [
        ['label' => 'Основные', 'url' => ['/admin/settings']],
        ['label' => 'Контакты', 'url' => ['/admin/settings/auth']],
        ['label' => 'Электронные письма', 'url' => ['/admin/settings/emails']],
        ['label' => 'Настройки почты', 'url' => ['/admin/settings/mail']],
        ['label' => 'Уведомления пользователю', 'url' => ['/admin/settings/notify']],
        ['label' => 'Подсказки для ценовых категорий', 'url' => ['/admin/settings/hint']],
    ],
    'options' => ['class' => 'navbar-nav navbar-default']
]); ?>
</div>
</div>
