<?php
/** @var yii\web\View $this */
/** @var common\models\Post $model */
/** @var yii\widgets\ActiveForm $form */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

// Note: keep form simple; image is a URL string for now (upload can be added later)
?>
<div class="post-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php // Slug is generated automatically; still allow manual edit if needed ?>
    <?= $form->field($model, 'slug')->textInput(['maxlength' => true])->hint('Оставьте пустым — сгенерируется из заголовка') ?>

    <?= $form->field($model, 'excerpt')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 10]) ?>

    <?= $form->field($model, 'image')->textInput(['maxlength' => true])->hint('URL изображения (обложка/превью)') ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'meta_title')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'meta_description')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <?= $form->field($model, 'status')->dropDownList([
        \common\models\Post::STATUS_DRAFT => 'Черновик',
        \common\models\Post::STATUS_PUBLISHED => 'Опубликован',
    ]) ?>

    <?php
    // For now we accept UNIX timestamp; later we can replace with DatePicker
    ?>
    <?= $form->field($model, 'published_at')->textInput()->hint('UNIX timestamp (можно оставить пустым)') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Назад', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
