<?php
use yii\bootstrap\Nav;
?>
<div class="row">
<div class="col-md-12">

<?php echo Nav::widget([
    'items' => [
        ['label' => 'Основные', 'url' => ['/admin/settings']],
        ['label' => 'Авторизация', 'url' => ['/admin/settings/auth']],
        ['label' => 'Электронные письма', 'url' => ['/admin/settings/emails']],
        ['label' => 'Уведомления пользователю', 'url' => ['/admin/settings/notify']],
    ],
    'options' => ['class' => 'navbar-nav navbar-default']
]); ?>
</div>
</div>
