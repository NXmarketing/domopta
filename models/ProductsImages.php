<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "products_images".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $image
 * @property integer $order
 */
class ProductsImages extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'products_images';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'order'], 'integer'],
            [['image'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'image' => 'Image',
            'order' => 'Order',
        ];
    }

    public function getUrl($prefix = 'thumb')
    {
        $fnameArr = explode(".", $this->image);
        $ext = $fnameArr[count($fnameArr) - 1];
        unset($fnameArr[count($fnameArr) - 1]);
        $fnameClean = implode(".", $fnameArr);

        $suffix = '_domopta.ru';



        $path = __DIR__ . '/../web' . '/upload/product/' . $this->product_id . '/' . $prefix . "_" . $fnameClean . $suffix . "." . $ext;

        if (file_exists($path)) {
            return '/upload/product/' . $this->product_id . '/' . $prefix . "_" . $fnameClean . $suffix . "." . $ext;
        } else {
            return '/upload/product/' . $this->product_id . '/' . $prefix . '_' . $this->image;
        }
    }

    public function afterDelete()
    {
        $path = Yii::getAlias('@webroot/upload/product/' . $this->product_id . '/');
        $files = glob($path . '*' . $this->image);
        foreach ($files as $file) {
            unlink($file);
        }
        @unlink($path . $this->image);
    }
}
