<?php
/** @var yii\web\View $this */
/** @var common\models\Post $model */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="post-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php // Title ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php // Slug (URL) ?>
    <?= $form->field($model, 'slug')->textInput(['maxlength' => 191])->hint('Leave empty to auto-generate from title') ?>

    <?php // Short summary (excerpt) ?>
    <?= $form->field($model, 'excerpt')->textarea(['rows' => 3]) ?>

    <?php // Full content (HTML or Markdown) ?>
    <?= $form->field($model, 'content')->textarea(['rows' => 10]) ?>

    <?php // Preview/cover image URL ?>
    <?= $form->field($model, 'image')->textInput(['maxlength' => 500])->hint('Absolute or relative image URL') ?>

    <hr>

    <?php // SEO fields ?>
    <?= $form->field($model, 'meta_title')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'meta_description')->textInput(['maxlength' => 500]) ?>

    <hr>

    <?php
    // Status dropdown
    // 0 = draft, 1 = published (see Post::STATUS_* constants)
    echo $form->field($model, 'status')->dropDownList([
        \common\models\Post::STATUS_DRAFT => 'Черновик',
        \common\models\Post::STATUS_PUBLISHED => 'Опубликован',
    ], ['prompt' => 'Выберите статус']);
    ?>

    <?php
    // Published at (UNIX timestamp in DB) — use HTML5 datetime-local in form
    // Format existing value to "YYYY-MM-DDTHH:MM" for the input control
    $publishedValue = $model->published_at
        ? date('Y-m-d\TH:i', (int)$model->published_at)
        : '';
    echo $form->field($model, 'published_at')->input('datetime-local', [
        'value' => $publishedValue,
    ])->hint('Optional. If empty, publication date is not set');
    ?>

    <div class="form-group mt-3">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
