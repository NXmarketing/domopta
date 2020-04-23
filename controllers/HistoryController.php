<?php
/**
 * Created by PhpStorm.
 * User: resh
 * Date: 24.01.19
 * Time: 18:12
 */

namespace app\controllers;


use app\models\Order;
use app\models\OrderSearch;
use yii\filters\AccessControl;
use yii\web\Controller;

class HistoryController extends Controller {

	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}

	public function actionIndex(){
		$orders = Order::find()->where(['user_id' => \Yii::$app->user->id])->orderBy(['created_at' => SORT_DESC])->all();
		return $this->render('index', ['orders' => $orders]);
	}

	public function actionDetail($id){
		$order = Order::findOne(['id' => $id, 'user_id' => \Yii::$app->user->id]);
		return $this->render('detail', ['order' => $order]);
	}

}