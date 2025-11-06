<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Посты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать пост', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // Simple admin grid with key columns ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => yii\grid\SerialColumn::class],
            'id',
            'title',
            'slug',
            [
                'attribute' => 'status',
                'value' => static function($model) {
                    return $model->isPublished() ? 'Опубликован' : 'Черновик';
                }
            ],
            [
                'attribute' => 'published_at',
                'value' => static function($model) {
                    return $model->published_at ? date('Y-m-d H:i', $model->published_at) : '—';
                }
            ],
            ['class' => yii\grid\ActionColumn::class],
        ],
    ]) ?>
</div>
