<?php
/**
 * Created by PhpStorm.
 * User: resh
 * Date: 13.05.17
 * Time: 16:30
 */

namespace app\controllers;

use app\models\Cart;
use app\models\Products;
use app\models\CartDetails;
use app\models\CartSearch;
use app\models\Order;
use yii\filters\AccessControl;
use yii\web\Controller;

class CartController extends Controller
{

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

    public function actionIndex()
    {
        $searchModel = new CartSearch();
        $cart = $searchModel->search();
        return $this->render('index', ['cart' => $cart]);
    }

    public function actionChange(){
        $detail = CartDetails::findOne(\Yii::$app->request->post('detail_id'));
        $detail->amount = \Yii::$app->request->post('amount');
        $detail->save();

        $total = Cart::getAmount();
	    $total['sum'] = $total['sum'];
        $total['row'] = Products::formatPrice($detail->getSum());
        if($detail->cart->product->pack_quantity > 0) {
            $total['row_amount'] = $detail->amount * $detail->cart->product->pack_quantity;
        } else {
            $total['row_amount'] = $detail->amount;
        }
        $total['row_id'] = $detail->id;
        return json_encode($total);
    }

    public function actionMemo(){
        $cart = Cart::findOne([
            'id' => \Yii::$app->request->post('id')
        ]);
        if($cart){
            $cart->memo = \Yii::$app->request->post('memo');
            $cart->save();
        }
    }

    public function actionDelete($id){
        $cart = CartDetails::findOne(['id' => $id]);
        if($cart){
            $cart->delete();
        }
        if(count($cart->cart->details) == 0){
            $cart->cart->delete();
        }
        return $this->redirect(['index']);
    }

    public function actionOrder(){

        if(!Order::create()){
            return $this->redirect(['/cart']);
        }
        return $this->render('order');
    }

}