<?php

/**
 * Created by PhpStorm.
 * User: resh
 * Date: 10.05.17
 * Time: 13:05
 */

namespace app\modules\admin\controllers;

use app\components\Helper;
use app\models\Category;
use app\models\ImportForm;
use app\models\Products;
use app\models\ProductsBackup;
use app\models\ProductsImages;
use app\models\ProductsSearch;
use Codeception\Module\Yii2;
use yii\helpers\Inflector;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use dektrium\user\filters\AccessRule;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

class CatalogController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            return \Yii::$app->user->identity->role == 'admin';
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'update', 'deleteimage', 'sort2', 'upload', 'deleteproductphotos'],
                        'matchCallback' => function ($rule, $action) {
                            return \Yii::$app->user->identity->role == 'moderator';
                        }
                    ],
                    [
                        'allow' => true,
                        //'actions' => ['index', 'update', 'deleteimage', 'sort', 'upload', 'deleteproductphotos'],
                        'matchCallback' => function ($rule, $action) {
                            return \Yii::$app->user->identity->role == 'contentmanager';
                        }
                    ],
                    [
                        'allow' => false,
                    ]
                ],
            ],
        ];
    }

    public function actionIndex($id = null)
    {
        if ($id) {
            $category = $this->getCategory($id);
        } else {
            $category = new Category();
        }
        $category_list = Category::find()->where(['parent_id' => null])->orderBy(['position' => SORT_ASC])->all();

        $search_model = new ProductsSearch();
        $dataProvider = $search_model->search(\Yii::$app->request->queryParams, $id);
        return $this->render('index', [
            'category' => $category,
            'category_list' => $category_list,
            'searchModel' => $search_model,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionAddcategory($id = null)
    {
        $model = new Category();
        $cat = $this->getCategory($id, false);
        if ($cat && $cat->parent_id == null) {
            $model->parent_id = $id;
        }
        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            return $this->redirect(['index', 'id' => $id]);
        }
        return $this->render('addcategory', ['model' => $model]);
    }

    public function actionUpdatecategory($id = null)
    {
        $model = $this->getCategory($id);
        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $model->save(false);
            return $this->redirect(['index', 'id' => $id]);
        }
        return $this->render('addcategory', ['model' => $model]);
    }

    public function actionDeletecategory($id)
    {
        $category = $this->getCategory($id);
        $category->delete();
        return $this->redirect(['index']);
    }

    public function getCategory($id, $throw = true)
    {
        $category = Category::findOne($id);
        if (!$category && $throw) {
            throw new NotFoundHttpException('Страница не нейдена');
        }
        return $category;
    }

    public function actionImport($id = null)
    {
        if ($id) {
            $category = $this->getCategory($id);
        } else {
            $category = new Category();
        }
        $category_list = Category::find()->where(['parent_id' => null])->orderBy(['position' => SORT_ASC])->all();

        $search_model = new ProductsSearch();
        $model = new ImportForm();
        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $model->import($id);
            \Yii::$app->session->setFlash('success', 'Импорт успешно завершен');
            return $this->redirect(['/admin/catalog/index', 'id' => $id]);
        }
        return $this->render('import', [
            'model' => $model,
            'category' => $category,
            'category_list' => $category_list,
            'searchModel' => $search_model,
        ]);
    }

    public function actionBackup($id)
    {
        if (ProductsBackup::find()->where(['category_id' => $id])->one()) {
            ImportForm::restore($id);
        } else {
            \Yii::$app->session->setFlash('danger', 'Товары по данной категории нету в резерве');
        }
        return $this->redirect(['index', 'id' => $id]);
    }

    public function actionUpdate($id)
    {
        $model = Products::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Товар не найден');
        }
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->refresh();
        }
        $search_model = new ProductsSearch();
        return $this->render('update', ['model' => $model, 'searchModel' => $search_model]);
    }

    //    public function actionUpload($id){
    //        $model = Products::findOne($id);
    //        $arr = [];
    //        if($model){
    //            $files = UploadedFile::getInstances($model, 'images');
    //            foreach ($files as $file){
    //                $fname = uniqid() . '.' . $file->extension;
    //                $path = \Yii::getAlias('@webroot/upload/product/' . $model->id. '/');
    //                @mkdir($path);
    //                $file->saveAs($path . $fname);
    //
    //
    //                $width = 178;
    //                $height = 245;
    //                $model->createThumb($path, $fname, 'thumb_', "_domopta.ru", 240, 330);
    //                $model->createThumb($path, $fname, 'big_', "_domopta.ru", 537, 661);
    //                $model->createThumb($path, $fname, 'small_', "_domopta.ru", 146, 187);
    //
    //                $model1 = new ProductsImages();
    //                $model1->image = $fname;
    //                $model1->product_id = $model->id;
    //                $model1->save();
    //
    //                $arr['initialPreview'][] = '<img src="'. '/upload/product/' . $model->id. '/' . $fname .'" width="146" >';
    //                $arr['initialPreviewConfig'][] = [
    //                	'caption' => '',
    //	                'size' => $file->size,
    //	                'width' => '120px',
    //	                'url' => '/admin/catalog/deleteimage?id=' . $model1->id
    //                ];
    //            }
    //        }
    //        return json_encode($arr);
    //    }


    public function actionUpload($id)
    {
        $model = Products::findOne($id);
        $arr = [];
        if ($model) {
            $model_images = ProductsImages::find()->where(['folder' => Inflector::slug($model->article_index)])->all();
            $max_order = 0;
            foreach ($model_images as $model_image) {
                if ($max_order < $model_image->order) {
                    $max_order = $model_image->order;
                }
            }
            $max_order++;

            $files = UploadedFile::getInstances($model, 'images');
            foreach ($files as $k => $file) {
                $fname = uniqid() . '.' . $file->extension;
                $path = \Yii::getAlias('@webroot/upload/product/' . $model->id . '/');
                @mkdir($path);
                $file->saveAs($path . $fname);


                $width = 178;
                $height = 245;
                $model->createThumb($path, $fname, 'thumb_', "_domopta.ru", 240, 330);
                $model->createThumb($path, $fname, 'big_', "_domopta.ru", 537, 661);
                $model->createThumb($path, $fname, 'small_', "_domopta.ru", 146, 187);

                $model1 = new ProductsImages();
                $model1->image = $fname;
                $model1->folder = Inflector::slug($model->article_index);
                $model1->order = $max_order + $k;
                $model1->save();

                $arr['ids'][$k] = $model1->id;

                /*$arr['initialPreview'][] = '<img src="'. '/upload/product/' . $model->id. '/' . $fname .'" width="146" >';
                $arr['initialPreviewConfig'][] = [
                    'caption' => '',
                    'size' => $file->size,
                    'width' => '120px',
                    'url' => '/admin/catalog/deleteimage?id=' . $model1->id
                ];*/
            }
        }
        return json_encode($arr);
    }


    public function actionDeleteimage($id)
    {
        $model = ProductsImages::findOne($id);
        $model->delete();
        return json_encode(['ok']);
    }

    public function actionSort()
    {
        $params = \Yii::$app->request->post();
        $order = 0;
        foreach ($params as $id) {
            $model = ProductsImages::findOne($id);
            $model->order = $order;
            $model->save();
            $order++;
        }
    }

    public function actionSort2()
    {
        $params = \Yii::$app->request->post();
        $order = 0;
        foreach ($params['ids'] as $id) {
            $model = ProductsImages::findOne($id);
            $model->order = $order;
            $model->save();
            $order++;
        }
    }

    public function actionSlug($string, $id)
    {
        $iteration = 0;
        do {
            $iteration++;
            $slug = Inflector::slug($string, '-') . '-' . $iteration;
        } while (Products::findOne(['slug' => $slug]));
        return $slug;
    }

    public function actionSlugcat($string, $parent_id = null, $id = null)
    {
        $parent = Category::findOne($parent_id);
        $iteration = 0;
        do {
            if ($parent) {
                $slug =  $parent->slug . '/' . Inflector::slug($string, '-');
            } else {
                $slug =  '/' . Inflector::slug($string, '-');
            }
            if($iteration) $slug .= '-' . $iteration;
            $iteration++;
        } while (Category::find()->where(['slug' => $slug])->andWhere(['!=', 'id', $id])->count());
        return $slug;
    }

    public function actionDeletephotos($id)
    {
        $products = Products::findAll(['category_id' => $id]);
        foreach ($products as $product) {
            ProductsImages::deleteAll(['folder' => Inflector::slug($product->article_index)]);
        }
        return $this->redirect(['index', 'id' => $id]);
    }

    public function actionDeletemultiply($id)
    {
        $models = Products::findAll(['id' => \Yii::$app->request->post('selection')]);
        foreach ($models as $model) {
            $model->is_deleted = 1;
            $model->deleted_date = time();
            $model->save();
        }
        return $this->redirect(['index'] + \Yii::$app->request->get());
    }

    public function actionDelete($id = '', $product_id)
    {
        $model = Products::findOne($product_id);
        if ($model) {
            //$model->delete();
            $model->is_deleted = 1;
            $model->deleted_date = time();
            $model->save();
        }
        return $this->redirect(['index', 'id' => $id]);
    }

    public function actionDeleteproductphotos($id)
    {
        $model = Products::findOne($id);
        if ($model) {
            foreach ($model->pictures as $picture) {
                $picture->delete();
            }
        }
        return $this->redirect(['update', 'id' => $id]);
    }

    public function actionTree()
    {
        $cats = \Yii::$app->request->post();
        foreach ($cats as $key => $id) {
            Category::updateAll(['position' => $key], ['id' => $id]);
        }
    }
}
