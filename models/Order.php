<?php

namespace app\models;

use app\components\Mailer;
use app\modules\admin\Module;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $created_at
 * @property OrderDetails[] $detiles
 * @property User $user
 * @property Mailer $mailer
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Номер заказа',
            'user_id' => 'User ID',
            'created_at' => 'Дата добавления',
            'sum' => 'Сумма',
            'ooo' => 'ООО',
        ];
    }

    protected function getMailer()
    {
        return \Yii::$container->get(Mailer::className());
    }

    public function getUser(){
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getDetiles(){
        return $this->hasMany(OrderDetails::className(), ['order_id' => 'id'])->orderBy(['article' => SORT_ASC]);
    }

    public function getSum(){
        return $this->getDetiles()->sum('sum');
    }

    public function getOldSum(){
        return $this->getDetiles()->sum('sum_old');
    }

    public function getAmount(){
    	$amount = 0;
    	foreach ($this->detiles as $item){
    		$amount += $item->amount;
	    }
	    return $amount;
    }

    public static function create(){
        //print_r(Yii::$app->user->identity->profile->type); die();
        $cart = Cart::findAll(['user_id' => Yii::$app->user->id]);
        if($cart) {
            $order = new Order();
            $order->user_id = Yii::$app->user->id;
            $order->created_at = time();
            $order->save();
            foreach ($cart as $item) {
//            	print_r($item->product); die();
                foreach ($item->details as $detail) {
                    if ($detail->amount > 0) {
                        $order_details = new OrderDetails();
                        $order_details->order_id = $order->id;
                        $order_details->article = $item->article;
                        $order_details->name = $item->product->name;
                        $order_details->color = $detail->color;
                        $order_details->memo = $item->memo;
                        $order_details->amount = $detail->amount;
                        if(Yii::$app->user->identity->profile->type == 1) {
                            $order_details->price = $item->product->price;
                        } elseif (Yii::$app->user->identity->profile->type == 2){
                            $order_details->price = $item->product->price2;
                        } else {
                            $order_details->price = $item->product->price;
                        }
                        $quantity = $item->product->pack_quantity?$item->product->pack_quantity:1;
                        $order_details->sum = $order_details->price * $quantity * $detail->amount;
                        $order_details->flag = 1;
                        $order_details->save();
                    }
                    $detail->delete();
                }
                $item->delete();
            }

            $controller = new Controller('new', Module::className());
            $body = $controller->renderPartial('@app/modules/admin/views/orders/_email', ['order' => $order]);
            $model = new Self;
            $model->mailer->sendEmail(Yii::$app->settings->get('Settings.adminEmail'), 'Новый заказ', $body);
            $model->mailer->sendEmail(Yii::$app->settings->get('Settings.sellEmail'), 'Новый заказ', $body);

            if($order->user->unconfirmed_email == 1) {
                $body = $controller->renderPartial('@app/modules/admin/views/orders/_email2', ['order' => $order]); // @todo сделать письмо
                $model->mailer->sendEmail($order->user->email, 'Ваш Заказ успешно оформлен и отправлен в Отдел Заказов', $body);
            }
            return true;
        }
        return false;
    }

    public function afterDelete()
    {
        $models = $this->detiles;
        foreach ($models as $model){
            $model->delete();
        }
    }

    public function recount(){
        foreach ($this->detiles as $detail){
            $product = Products::findOne(['article_index' => $detail->article]);
            $detail->flag_old = $detail->flag;
            $detail->flag = $product->flag;
            $detail->price_old = $detail->price;
            $detail->price = $product->price;
            $detail->sum_old = $detail->sum;
            $quantity = $product->pack_quantity?$product->pack_quantity:1;
            $detail->sum = $product->price * $quantity * $detail->amount;
            $detail->save();
        }
    }

    public function recountcancel(){
        foreach ($this->detiles as $detail){
            if($detail->price_old !== null){
                $detail->flag = $detail->flag_old;
                $detail->flag_old = null;
                $detail->price = $detail->price_old;
                $detail->price_old = null;
                $detail->sum = $detail->sum_old;
                $detail->sum_old = null;
                $detail->save();
            }
        }
    }

}
