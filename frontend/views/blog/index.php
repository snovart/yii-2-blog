<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\widgets\ListView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Блог';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="blog-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php // ListView renders each post with a simple card ?>
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => 'Постов пока нет.',
        'itemOptions' => ['class' => 'mb-4 p-3 border rounded'],
        'itemView' => function ($model) {
            /** @var \common\models\Post $model */
            $url = Url::to(['blog/view', 'slug' => $model->slug]);
            return
                '<h3>'.Html::a(Html::encode($model->title), $url).'</h3>'.
                ($model->image ? '<p><img src="'.Html::encode($model->image).'" alt="" style="max-width:100%;height:auto"></p>' : '').
                ($model->excerpt ? '<p>'.nl2br(Html::encode($model->excerpt)).'</p>' : '').
                Html::a('Читать далее →', $url, ['class' => 'btn btn-sm btn-primary']);
        },
        'layout' => "{items}\n{pager}",
    ]) ?>
</div>
