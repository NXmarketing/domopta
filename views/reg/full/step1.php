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
</div>