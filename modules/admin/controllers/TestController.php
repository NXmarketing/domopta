<?php
/**
 * Created by PhpStorm.
 * User: resh
 * Date: 25.01.19
 * Time: 17:07
 */

namespace app\modules\admin\controllers;


use app\models\Products;
use yii\helpers\Inflector;
use app\models\ProductsImages;
use yii\httpclient\Client;
use yii\web\Controller;
use yii\web\UploadedFile;

class TestController extends Controller {

	public function actionIndex(){
		$curl = new Client();
		$response = $curl->createRequest()
			->setHeaders([
				'Content-Type: application/json',
				'Accept: application/json',
				'Authorization: Token d1f1cc1d2f8b283837831c90c7f5d8e1b33776da'
			])
			->setData(['query' => '2315995226111'])
			->setUrl('https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party')
			->send();
//		$data = $response->data;
//		if(!isset($data['suggestions'])){
//
//		}
		print_r($response->data);
	}

	public function actionPhoto(){
	    $model = Products::findOne(4038);
	    return $this->render('index', ['model' => $model]);
    }

    public function actionUpload($id){
        $model = Products::findOne($id);
        $arr = [];
        if($model){
            $model_images = ProductsImages::find()->where(['folder' => Inflector::slug($model->article_index)])->all();
            $max_order = 0;
            foreach ($model_images as $model_image){
                if($max_order < $model_image->order){
                    $max_order = $model_image->order;
                }
            }
            $max_order ++;

            $files = UploadedFile::getInstances($model, 'images');
            foreach ($files as $k => $file){
                $fname = uniqid() . '.' . $file->extension;
                $path = \Yii::getAlias('@webroot/upload/product/' . Inflector::slug($model->article_index). '/');
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

}