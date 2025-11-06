<?php
namespace common\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;

/**
 * Post model (blog article).
 *
 * @property int         $id
 * @property string      $title
 * @property string      $slug
 * @property string|null $excerpt
 * @property string      $content
 * @property string|null $image
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property int         $status
 * @property int|null    $published_at
 * @property int|null    $created_by
 * @property int|null    $updated_by
 * @property int         $created_at
 * @property int         $updated_at
 */
class Post extends ActiveRecord
{
    public const STATUS_DRAFT     = 0;
    public const STATUS_PUBLISHED = 1;

    /** @inheritdoc */
    public static function tableName(): string
    {
        return '{{%post}}';
    }

    /** @inheritdoc */
    public function behaviors(): array
    {
        return [
            // Auto-fill created_at / updated_at with UNIX timestamps
            TimestampBehavior::class,
            // Auto-fill created_by / updated_by with current user IDs (in web/app context)
            BlameableBehavior::class,
            // Generate slug from title once; keep unique within the table
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',       // source field
                'slugAttribute' => 'slug',    // target field
                'ensureUnique' => true,       // adds numeric suffix if needed
                'immutable' => true,          // don't change slug after first save
            ],
        ];
    }

    /** @inheritdoc */
    public function rules(): array
    {
        return [
            [['title', 'content'], 'required'],

            [['excerpt', 'content'], 'string'],

            [['status', 'published_at', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_PUBLISHED]],

            [['title', 'meta_title'], 'string', 'max' => 255],
            // NOTE: DB column is 191 for index limit; keep the same in validation
            ['slug', 'string', 'max' => 191],
            [['image', 'meta_description'], 'string', 'max' => 500],

            // Unique slug on application level as well
            ['slug', 'unique'],

            // Safe when using mass-assignment in forms
            [['published_at', 'image', 'meta_title', 'meta_description', 'excerpt'], 'safe'],
        ];
    }

    /**
     * Normalize `published_at` and set it automatically when publishing.
     */
    public function beforeSave($insert)
    {
        // Convert string input (e.g. 'YYYY-MM-DDTHH:MM') to a UNIX timestamp
        if (is_string($this->published_at) && $this->published_at !== '' && !ctype_digit((string)$this->published_at)) {
            $ts = strtotime($this->published_at);
            if ($ts !== false) {
                $this->published_at = $ts;
            }
        }

        // If status is "published" and no date provided — use current time
        if ((int)$this->status === self::STATUS_PUBLISHED && empty($this->published_at)) {
            $this->published_at = time();
        }

        return parent::beforeSave($insert);
    }

    /** @inheritdoc */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
            'slug' => 'Слаг (URL)',
            'excerpt' => 'Краткое описание',
            'content' => 'Содержимое',
            'image' => 'Изображение',
            'meta_title' => 'SEO Title',
            'meta_description' => 'SEO Description',
            'status' => 'Статус',
            'published_at' => 'Дата публикации',
            'created_by' => 'Автор',
            'updated_by' => 'Редактор',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    // --- Helpers -------------------------------------------------------------

    /** Returns whether the post is published. */
    public function isPublished(): bool
    {
        return (int)$this->status === self::STATUS_PUBLISHED;
    }

    /** Relations to User model (optional but handy in views) */
    public function getAuthor()
    {
        return $this->hasOne(\common\models\User::class, ['id' => 'created_by']);
    }

    public function getUpdater()
    {
        return $this->hasOne(\common\models\User::class, ['id' => 'updated_by']);
    }
}
