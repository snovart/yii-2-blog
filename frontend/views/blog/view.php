<?php
/** @var yii\web\View $this */
/** @var \common\models\Post $model */

use yii\helpers\Html;

$this->title = $model->meta_title ?: $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Блог', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;

// Optional: set meta description for SEO
if ($model->meta_description) {
    $this->registerMetaTag(['name' => 'description', 'content' => $model->meta_description]);
}
?>
<div class="blog-view">
    <h1><?= Html::encode($model->title) ?></h1>

    <?php if ($model->image): ?>
        <p><img src="<?= Html::encode($model->image) ?>" alt="" style="max-width:100%;height:auto"></p>
    <?php endif; ?>

    <p class="text-muted">
        <?= $model->published_at ? date('d.m.Y H:i', $model->published_at) : date('d.m.Y H:i', $model->created_at) ?>
        <?php if ($model->author): ?> · Автор: <?= Html::encode($model->author->username) ?><?php endif; ?>
    </p>

    <div class="content">
        <?php // Render raw HTML if you store HTML, or escape if it's plain text ?>
        <?= $model->content ?>
    </div>
</div>
