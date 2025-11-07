<?php
/** @var yii\web\View $this */
/** @var common\models\Post $model */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="post-form">

    <?php
    // Enable file uploads (needed for cover image upload)
    $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>

    <?php // Title ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php // Slug (URL). Leave empty to auto-generate from title on first save ?>
    <?= $form->field($model, 'slug')->textInput(['maxlength' => 191])->hint('Leave empty to auto-generate from title') ?>

    <?php // Short summary (excerpt) ?>
    <?= $form->field($model, 'excerpt')->textarea(['rows' => 3]) ?>

    <?php // Full content (HTML or Markdown). Replace with WYSIWYG later if needed ?>
    <?= $form->field($model, 'content')->textarea(['rows' => 10]) ?>

    <hr>

    <?php // Cover image: preview + file input (imageFile) + note about existing image ?>

    <?php if (!empty($model->image)): ?>
        <div class="mb-3">
            <label class="form-label">Current cover</label>
            <div>
                <img src="<?= Yii::$app->imageStorage->getPublicUrl($model->image) ?>"
                     alt="cover"
                     style="max-width: 320px; height: auto; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="form-text">This is the currently stored cover image.</div>
        </div>
    <?php endif; ?>

    <?php
    // File input for new upload (model must have public $imageFile and validation rule)
    echo $form->field($model, 'imageFile')->fileInput()->hint(
        'Select a new image to replace the current cover. Leave empty to keep existing.'
    );
    ?>

    <?php
    // Optional: allow manual URL (kept for flexibility; controller may prioritize upload over URL)
    echo $form->field($model, 'image')->textInput(['maxlength' => 500])->hint('Absolute or relative image URL');
    ?>

    <hr>

    <?php // SEO fields ?>
    <?= $form->field($model, 'meta_title')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'meta_description')->textInput(['maxlength' => 500]) ?>

    <hr>

    <?php
    // Status dropdown (0 = draft, 1 = published)
    echo $form->field($model, 'status')->dropDownList([
        \common\models\Post::STATUS_DRAFT => 'Черновик',
        \common\models\Post::STATUS_PUBLISHED => 'Опубликован',
    ], ['prompt' => 'Выберите статус']);
    ?>

    <?php
    // Published at (UNIX timestamp in DB) — use HTML5 datetime-local in form
    $publishedValue = $model->published_at ? date('Y-m-d\TH:i', (int)$model->published_at) : '';

    echo $form->field($model, 'published_at', [
        // turn off client-side validation for this field
        'enableClientValidation' => false,
    ])->input('datetime-local', [
        'value' => $publishedValue,
        'placeholder' => 'Select publish date & time',
        'step' => 60, // опционально: шаг 1 минута
    ])->hint('If empty and status is "Published", the current time will be used.');
    ?>

    <div class="form-group mt-3">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Назад', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
