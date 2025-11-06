<?php
namespace frontend\controllers;

use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use common\models\Post;

/**
 * BlogController renders public blog pages (list & single).
 */
class BlogController extends Controller
{
    /** Blog index: list only published posts */
    public function actionIndex()
    {
        $query = Post::find()
            ->where(['status' => Post::STATUS_PUBLISHED])
            ->orderBy(['published_at' => SORT_DESC, 'created_at' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /** Single post by slug */
    public function actionView(string $slug)
    {
        $model = Post::find()
            ->where(['slug' => $slug, 'status' => Post::STATUS_PUBLISHED])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('Пост не найден.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }
}
