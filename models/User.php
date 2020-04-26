<?php

namespace app\models;

use app\components\Mailer;
use dektrium\user\helpers\Password;
use yii\db\Exception;
use yii\bootstrap\Html;

/**
 * @property Favorite $favorite
 */
class User extends \dektrium\user\models\User
{

    public $password;
    public $password_repeat;

    public $docs;

    protected function getMailer()
    {
        return \Yii::$container->get(Mailer::className());
    }


    public function getIsAdmin()
    {
        return in_array($this->role, ['admin', 'moderator', 'manager', 'contentmanager']);
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        // add field to scenarios
        $scenarios['create'][]   = 'is_active';
        $scenarios['update'][]   = 'is_active';
        $scenarios['register'][] = 'is_active';
        $scenarios['cabinet'] = ['password', 'password_repeat', 'password_hash', 'email'];
        return $scenarios;
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules['activate'] = ['is_active', 'integer'];
        $rules['phone_code'] = ['phone_code', 'safe'];
        $rules[] = ['role', 'default', 'value' => 'user'];
        $rules[] = ['not_delete', 'integer'];
        $rules[] = ['password', 'string', 'min' => 6];
        $rules[] = ['flags', 'safe'];
        $rules[] = ['email', 'email', 'skipOnEmpty' => true];
        $rules[] = ['password_repeat', 'compare', 'compareAttribute' => 'password'];
        $rules[] = ['docs', 'file', 'maxFiles' => 5, 'extensions' => ['jpg', 'pdf', 'doc', 'docx']];
        unset($rules['usernameMatch']);
        unset($rules['emailRequired']);
        return $rules;
    }


    public function attributeLabels()
    {
        $attribute_lables = parent::attributeLabels();
        $attribute_lables['created_at'] = 'Дата';
        $attribute_lables['not_delete'] = 'Защита от удаления';
        $attribute_lables['role'] = 'Роль';
        $attribute_lables['organization'] = 'Название ООО';
        $attribute_lables['phone'] = 'Телефон';
        $attribute_lables['inn'] = 'ИНН';
        $attribute_lables['is_active'] = 'Активированный пользователь';
        return $attribute_lables;
    }


    public function activate()
    {
        $this->is_active = 1;
        $this->save(false);
        $this->mailer->sendActivate($this);
    }

    public function block()
    {
        parent::block();
        try {
            $this->mailer->sendBlock($this);
        } catch (\Exception $e) {
        }
    }

    public function unblock()
    {
        parent::unblock();
        $this->mailer->sendUnblock($this);
    }

    public function delete()
    {
        parent::delete();
        //$this->mailer->sendDelete($this);
    }

    public function ignore()
    {
        $this->is_ignored = 1;
        $this->save();
    }

    public function unignore()
    {
        $this->is_ignored = 0;
        $this->save();
    }

    public function getIsIgnored()
    {
        return $this->is_ignored;
    }

    public function getStatus()
    {
        if (!$this->confirmed_at) {
            return 'Не активный';
        }
        if ($this->getIsBlocked()) {
            return 'Блок';
        }
        if ($this->getIsIgnored()) {
            return 'Игнор';
        }
        if ($this->is_active) {
            return 'актив';
        }
    }

    public function getIsActive()
    {
        return $this->is_active == 1 && $this->getIsIgnored() != 1 && $this->getIsBlocked() != 1;
    }

    public function sendEmail($letter, $params = [])
    {
        if ($letter == 'delete') {
            try {
                $this->mailer->sendDelete($this);
            } catch (\Swift_TransportException $e) {
            }
        }
        if ($letter == 'confirm') {
            try {
                $this->mailer->sendConfirmationMessage($this, 'https://domopta.ru/confirm?key=' . $this->auth_key);
            } catch (\Swift_TransportException $e) {
            }
        }
    }

    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['user_id' => 'id']);
    }

    public function afterDelete()
    {
        parent::afterDelete();
        foreach ($this->orders as $order) {
            $order->delete();
        }
    }

    public static function countActivated()
    {
        return self::find()->where(['is_active' => 1, 'is_ignored' => null, 'blocked_at' => null])->count();
    }

    public static function countBlocked()
    {
        return self::find()->where('blocked_at IS NOT NULL')->count();
    }

    public static function countIgnored()
    {
        return self::find()->where(['is_ignored' => 1, 'blocked_at' => null])->count();
    }

    public function createMob()
    {
        if ($this->getIsNewRecord() == false) {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }

        $transaction = $this->getDb()->beginTransaction();

        try {
            //$this->password = ($this->password == null && $this->module->enableGeneratingPassword) ? Password::generate(8) : $this->password;

            $this->trigger(self::BEFORE_CREATE);

            if (!$this->save()) {
                $transaction->rollBack();
                return false;
            }

            //			$this->confirm();

            $this->trigger(self::AFTER_CREATE);

            $transaction->commit();

            $number = str_replace('+', '', $this->username);

            \Yii::$app->sms->send_sms($number, "Ваш код для подтверждения регистрации на сайте:\n"  . $this->phone_code . "\ndomopta.ru");


            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::warning($e->getMessage());
            throw $e;
        }
    }

    public function resetPass()
    {
        $this->trigger(self::BEFORE_CONFIRM);
        $this->password = Password::generate(8);
        $this->confirmed_at = time();
        $result = (bool) $this->save();
        if ($result) {
            $number = str_replace('+', '', $this->username);
            \Yii::$app->sms->send_sms($number, "Пароль для входа на сайт успешно изменен.\nНовый пароль для входа:\n" . $this->password . "\ndomopta.ru");
            //			echo $this->password;
        }
        //$result = (bool) $this->updateAttributes(['confirmed_at' => time()]);
        $this->trigger(self::AFTER_CONFIRM);
        return $result;
    }

    public function confirm()
    {
        $this->trigger(self::BEFORE_CONFIRM);
        $this->password = Password::generate(8);
        $this->confirmed_at = time();
        $result = (bool) $this->save();
        if ($result) {
            $number = str_replace('+', '', $this->username);
            \Yii::$app->sms->send_sms($number, "Спасибо за регистрацию!\nВаш пароль для входа на сайте:\n" . $this->password . "\ndomopta.ru");
        }
        //$result = (bool) $this->updateAttributes(['confirmed_at' => time()]);
        $this->trigger(self::AFTER_CONFIRM);
        return $result;
    }

    public function getFiles()
    {
        return $this->hasMany(UserFile::class, ['user_id' => 'id']);
    }

    public function getFavorite()
    {
        return $this->hasMany(Favorite::className(), ['user_id' => 'id']);
    }

    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        if (!$insert) {
            $this->username = $this->getOldAttribute('username');
        }
        return true;
    }

    public function getCartSum()
    {
        $carts = Cart::findAll(['user_id' => $this->id]);
        $sum = 0;
        foreach ($carts as $cart) {
            $sum += $cart->getSum();
        }
        return $sum;
    }

    public function renderBigCart()
    {
        $carts = Cart::find()
            ->innerJoinWith('details')
            ->where(['user_id' => $this->id])
            ->andFilterWhere(['>', '{{%cart_details}}.amount', 0])
            ->orderBy(['article' => SORT_ASC])->all();

        $return = "<table class=\"table table-striped table-bordered table-bigcart\">";
        $return .= "<thead>";
        $return .= "<tr>";
        $return .= "<th></th>";
        $return .= "<th>Ариткул</th>";
        $return .= "<th>Название</th>";
        $return .= "<th>Цвет</th>";
        $return .= "<th class=\"text-center\">Кол-во</th>";
        $return .= "<th class=\"text-right\">Цена за ед.</th>";
        $return .= "<th class=\"text-right\">Сумма</th>";
        $return .= "<th class=\"text-right\">Cтатус</th>";
        $return .= "<th class=\"text-center\">Дата</th>";
        $return .= "</tr>";
        $return .= "</thead>";
        $return .= "<tbody>";
        $sum=0;
        $amm = 0;
        foreach ($carts as $cart) {
            foreach ($cart->details as $detail) {
                $return .= "<tr>";
                $return .= "<td>";
                if(isset($cart->product->pictures[0])) $return .= Html::img($cart->product->pictures[0]->getUrl('thumb'));
                $return .= "</td>";
                $return .= "<td>";
                $return .= $cart->product->article_index;
                $return .= "</td>";
                $return .= "<td>";
                $return .= $cart->product->name;
                $return .= "</td>";
                $return .= "<td>";
                $return .= $detail->color == 'default' ? '' : $detail->color;
                $return .= "</td>";
                $return .= "<td class=\"text-center\">";
                $return .= $detail->amount;
                $return .= "</td>";
                $return .= "<td class=\"text-right text-nowrap\">";
                $return .= number_format($detail->getPrice(), 2, ', <span class="kopeyki">', '') . '</span>';
                $return .= "</td>";
                $return .= "<td class=\"text-right text-nowrap\">";
                $return .= number_format($detail->getSum(), 2, ', <span class="kopeyki">', '') . '</span>';
                $return .= "</td>";
                $return .= "<td class=\"text-center\">";
                $return .= $cart->product->flag ? 'В&nbsp;наличии' : '<span style="color:red;">Продан</span>';
                $return .= "</td>";
                $return .= "<td class=\"text-center\">";
                $return .= str_replace(" ", "&nbsp;", date("H:i d.m.y", $cart->created_at));
                $return .= "</td>";
                $return .= "</tr>";
                $sum += $detail->getSum();
                $amm += $detail->amount;
            }
        }
        // Добавить новую колонку, статус товара (в наличии, продан (продан=красным цветом))

        $return .= "</tbody>";
        $return .= "<tfoot>";
        $return .= "<td colspan=\"4\"><strong>Итого:</strong></td>";
        $return .= "<td class=\"text-right text-center\"><strong>".$amm."</strong></td>";
        $return .= "<td class=\"text-right text-nowrap\"><strong></strong></td>";
        $return .= "<td class=\"text-right text-nowrap\"><strong>".number_format($sum, 2, ', <span class="kopeyki">', '') . '</span>'."</strong></td>";
        $return .= "<td class=\"text-right text-nowrap\"><strong></strong></td>";
        $return .= "<td class=\"text-right text-nowrap\"><strong></strong></td>";
        $return .= "</tfoot>";
        $return .= "</table>";
        return $return;
    }
}
