<?php
/** @var yii\web\View $this */
/** @var common\models\Post $model */

use yii\helpers\Html;

$this->title = 'Редактировать пост: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Посты', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="post-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
