<?php

/**
 * Created by PhpStorm.
 * User: resh
 * Date: 10.05.17
 * Time: 16:04
 */

namespace app\models;

use app\components\Helper;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\web\UploadedFile;

class ImportForm extends Model
{

    public $file;

    public function rules()
    {
        return [
            ['file', 'file']
        ];
    }


    public function backup($id)
    {
        ProductsBackup::deleteAll();

        \Yii::$app->db->createCommand('INSERT ' . ProductsBackup::tableName() . ' SELECT * FROM ' . Products::tableName() . ' WHERE category_id = :id')->bindValue(':id', $id)->execute();
    }

    public static function restore($id)
    {

        Products::deleteAll(['category_id' => $id]);

        \Yii::$app->db->createCommand('INSERT ' . Products::tableName() . ' SELECT * FROM ' . ProductsBackup::tableName() . ' WHERE category_id = :id')->bindValue(':id', $id)->execute();

        static::renewUrls($id);
    }

    public static function renewUrls($id)
    {

        $sql = \Yii::$app->db->createCommand("INSERT INTO seo_urls (`slug`, `name`, `item_id`, `route`, `module`) SELECT `slug`, `name`, `id` as `item_id`, 'product' as `route`, 'catalog' as `module` FROM `products` WHERE `category_id` = :id AND `slug` IS NOT NULL AND `is_deleted` = 0 ON DUPLICATE KEY UPDATE `seo_urls`.`item_id` = `products`.`id`")->bindValue(':id', $id);
        $sql->execute();
    }


    public function import($id)
    {
        set_time_limit(0);
        $this->backup($id);
        $file = UploadedFile::getInstance($this, 'file');
        $content = file($file->tempName);
        unset($content[0]);
        Products::updateAll(['flag' => 0], ['category_id' => $id]);
        $p = new Products();
        $columnNameArray = array_keys($p->attributes);
        $bulkInsertArray = [];
        $ids = [];


        foreach ($content as $string) {
            $string = mb_convert_encoding($string, 'UTF-8', 'CP-1251');
            $data = explode(';', $string);
            $model = Products::findOne(['article_index' => $data[3], 'is_deleted' => 0]);

            $t = false;
            if (!$model) {
                $t = true;
                $model = new Products();
                if (isset($bulkInsertArray[$data[3]])) {
                    $model->attributes = $bulkInsertArray[$data[3]];
                }
            }
            if ($data[1] != '') {
                $model->name = $data[1];
                if (!(trim($data[4]) == '')) {
                    $model->color = '';
                }
            }
            $model->article = $data[2];
            $model->article_index = $data[3];
            if (!(trim($data[4]) == '' && $data[13] == 0)) {
                if (isset($bulkInsertArray[$data[3]])) {
                    $colors = StringHelper::explode($bulkInsertArray[$data[3]]['color'], ',', true, true);
                } else {
                    $colors = [];
                }
                if (!in_array($data[4], $colors)) {
                    $colors[] = $data[4];
                }
                $model->color = implode(',', $colors);
                $flag = $data[13];
            } else {
                $flag = 1;
            }
            $model->size = $data[5];
            $model->consist = $data[6];
            $model->tradekmark = $data[7];
            $model->pack_quantity = $data[8] ? $data[8] : 0;
            $model->price = str_replace(',', '.', $data[9]);
            $model->pack_price = $data[10] ? str_replace(',', '.', $data[10]) : 0;
            $model->price2 = str_replace(',', '.', $data[11]);
            if (!$data[12]) {
                $data[12] = 0;
            }
            $model->pack_price2 = str_replace(',', '.', $data[12]);
            $model->flag = $flag;
            $model->ooo = $data[14];
            $model->category_id = $id;
            $model->slug = $model->generateSlugForInport();
            if ($model->validate()) {
                if (!$t) {
                    $ids[] = $model->article_index;
                } else {
                    $model->is_deleted = 0;
                }
                $model->id = \Yii::$app->db->getLastInsertID();
                $bulkInsertArray[$model->article_index] = $model->attributes;
            } else {
                echo 'Contact with developer team';
                Helper::pr($model->getErrors());
            }
        }

        Products::deleteAll(['article_index' => $ids, 'category_id' => $id]);

        \Yii::$app->db->createCommand()
            ->batchInsert(
                Products::tableName(),
                $columnNameArray,
                $bulkInsertArray
            )
            ->execute();

        static::renewUrls($id);
    }
}
