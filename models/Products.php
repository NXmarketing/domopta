<?php

namespace app\models;

use Imagine\Image\ManipulatorInterface;
use Yii;
use yii\helpers\Inflector;
use yii\imagine\Image;
use yii\rbac\ManagerInterface;
use yii\web\UploadedFile;

/**
 * This is the model class for table "products".
 *
 * @property integer $id
 * @property string $name
 * @property string $article
 * @property string $article_index
 * @property string $color
 * @property string $size
 * @property string $consist
 * @property string $tradekmark
 * @property string $slug
 * @property string $description
 * @property integer $pack_quantity
 * @property string $price
 * @property string $price2
 * @property string $pack_price
 * @property string $pack_price2
 * @property integer $flag
 * @property integer $ooo
 * @property integer $category_id
 * @property integer $is_deleted
 * @property ProductsImages[] $pictures
 */
class Products extends \yii\db\ActiveRecord
{

    public $images;

    public $hide = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pack_quantity', 'flag', 'ooo', 'category_id', 'is_deleted', 'deleted_date'], 'integer'],
            [['price', 'pack_price', 'price2', 'pack_price2'], 'number'],
            [['name', 'article', 'article_index', 'size', 'consist', 'tradekmark'], 'string', 'max' => 255],
            ['images', 'each', 'rule' => ['image']],
            ['description', 'safe'],
            ['slug', 'string'],
            ['color', 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'article' => 'Артикул',
            'article_index' => 'Артикул / индекс',
            'color' => 'Цвета',
            'size' => 'Размеры',
            'consist' => 'Состав',
            'tradekmark' => 'Товарный знак',
            'pack_quantity' => 'Кол-во штук в упаковке',
            'price' => 'Цена Опт',
            'price2' => 'Мелкий опт',
            'pack_price' => 'Цена за упаковку',
            'pack_price2' => 'Цена за упаковку (мелкий опт)',
            'flag' => 'Остаток',
            'ooo' => 'Товар по ООО',
            'category_id' => 'Category ID',
            'description' => 'Описание',
            'slug' => 'Ссылка',
            'images' => 'Изображения'
        ];
    }

    public function getPictures()
    {
        return $this->hasMany(ProductsImages::className(), ['product_id' => 'id'])->orderBy('order');
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->is_deleted == 1) {
            $carts = Cart::findAll(['article' => $this->article_index]);
            foreach ($carts as $cart) {
                $cart->delete();
            }

            foreach ($this->pictures as $image) {
                $image->delete();
            }
        }
        //        $files = UploadedFile::getInstances($this, 'images');
        //        foreach ($files as $file){
        //            $fname = uniqid() . '.' . $file->extension;
        //            $path = Yii::getAlias('@webroot/upload/product/' . $this->id. '/');
        //            @mkdir($path);
        //            $file->saveAs($path . $fname);
        //
        //
        //            $width = 178;
        //            $height = 245;
        //            $this->createThumb($path, $fname, 'thumb_', "_domopta.ru", 240, 330);
        //            $this->createThumb($path, $fname, 'big_', "_domopta.ru", 537, 661);
        //            $this->createThumb($path, $fname, 'small_', "_domopta.ru", 146, 187);
        //
        //            $model = new ProductsImages();
        //            $model->image = $fname;
        //            $model->product_id = $this->id;
        //            $model->save();
        //        }
        //        print_r($path); die();
    }

    public function createThumb($path, $fname, $prefix, $suffix, $width, $height)
    {
        $fnameArr = explode(".", $fname);
        $ext = $fnameArr[count($fnameArr) - 1];
        unset($fnameArr[count($fnameArr) - 1]);
        $fnameClean = implode(".", $fnameArr);

        $image = Image::getImagine()->open($path . $fname);
        $ratio = $image->getSize()->getWidth() / $image->getSize()->getHeight();
        if ($width) {
            $height = ceil($width / $ratio);
        } else {
            $width = ceil($height * $ratio);
        }

        ini_set('memory_limit', '512M');
        Image::thumbnail($path . $fname, $width, $height, ManipulatorInterface::THUMBNAIL_INSET)
            ->save($path . $prefix . $fnameClean . $suffix . "." . $ext, ['quality' => 100]);
    }

    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        $this->validateSlug($insert);
        $this->color = str_replace(', ', ',', $this->color);
        return true;
    }

    public function validateSlug($insert)
    {
        $slug = $this->slug;
        if (!$slug) {
            $iteration = 0;
            do {
                $slug = $iteration ? $iteration . '-' : "";
                $slug .= Inflector::slug($this->name) . '-' . Inflector::slug($this->article);
                $model = Products::find()->where(['slug' => $slug])->andWhere(['<>', 'id', $this->id])->one();
                $iteration++;
            } while ($model);
        }
        $this->slug = $slug;
    }

    public function generateSlugForInport()
    {
        //$slug = $this->slug;
        $slug = false;
        if (!$slug) {
            $iteration = 0;
            do {
                $slug = $iteration ? $iteration . '-' : "";
                $slug .= Inflector::slug($this->name) . '-' . Inflector::slug($this->article);
                $model = Products::find()->where(['slug' => $slug])->andWhere(['<>', 'id', $this->id])->one();
                $iteration++;
            } while ($model);
        }
        return $this->category->slug . "/" . $slug;
    }

    public function afterDelete()
    {
        $carts = Cart::findAll(['id' => $this->id]);
        $cart_i = 0;
        foreach ($carts as $cart) {
            $cart->delete();
            $cart_i++;
        }
        Yii::$app->session->addFlash('success1', 'Из корзин: ' . $cart_i);

        $models = ProductsImages::findAll(['product_id' => $this->id]);
        foreach ($models as $model) {
            $model->delete();
        }
        $path = Yii::getAlias('@webroot/upload/product/' . $this->id . '/');
        @rmdir($path);

        $fav = Favorite::findAll(['product_id' => $this->id]);
        $fav_i = 0;
        foreach ($fav as $item) {
            $fav_i++;
            $item->delete();
        }
        Yii::$app->session->addFlash('success2', 'Из закладок: ' . $fav_i);

        $product_views = ProductViews::findAll(['product_id' => $this->id]);
        foreach ($product_views as $product_view) {
            $product_view->delete();
        }
    }

    public function getUserPrice()
    {
        if (Yii::$app->user->identity->profile->type == 1 || Yii::$app->user->identity->profile->type == 3) {
            return $this->price;
        } else {
            return $this->price2;
        }
    }

    public static function find()
    {
        $query = parent::find();
        if (!Yii::$app->user->isGuest && !Yii::$app->user->identity->getIsAdmin()) {
            if (Yii::$app->user->identity->flags == 1) {
                $query->andWhere(['ooo' => '1']);
            }
        }
        return $query;
    }

    public function afterFind()
    {
        // $this->price = number_format($this->price, 2, '.', '');
        // $this->price2 = number_format($this->price2, 2, '.', '');
        $this->color = str_replace(',', ', ', $this->color);
    }


    public function getNextproduct()
    {
        $product = Products::find()
            ->where(['>', 'article', $this->article])
            ->andWhere(['is_deleted' => 0])
            ->andWhere(['category_id' => $this->category_id])
            ->orderBy(['article' => SORT_ASC])
            ->one();
        if (!$product) {
            return $this;
        }
        return $product;
    }
    public function getPrevproduct()
    {
        $product = Products::find()->where(['<', 'article', $this->article])
            ->andWhere(['is_deleted' => 0])
            ->andWhere(['category_id' => $this->category_id])
            ->orderBy(['article' => SORT_DESC])
            ->one();
        if (!$product) {
            return $this;
        }
        return $product;
    }


    public function hasColor($color)
    {
        $colors = explode(',', $this->color);
        return in_array(trim($color), $colors);
    }

    public static function formatPrice($price)
    {
        if (ceil($price * 100) == intVal(ceil($price) . '00')) {
            return number_format($price, 0, '', '');
        } else {
            return number_format($price, 2, ', <span class="kopeyki">', '') . '</span>';
        }
    }

    public static function formatEmailPrice($price, $cur = false)
    {
        if ($price) {
            if (ceil($price * 100) == intVal(ceil($price) . '00')) {
                $return = number_format($price, 0, ',', "");
                if($cur) $return .= 'руб.';
            } else {
                $return = number_format($price, 2, ',', "");
                if($cur) $return .= 'руб.';
            }
        } else
            return '-';
            return $return;
    }
}
