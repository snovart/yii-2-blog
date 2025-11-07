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

    public $imageFile = null;

    /** @inheritdoc */
    public static function tableName(): string
    {
        return '{{%post}}';
    }

    /** @inheritdoc */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
                'slugAttribute' => 'slug',
                'ensureUnique' => true,
                'immutable' => true,
            ],
        ];
    }

    /** Robust string → timestamp normalizer (returns int|null). */
    private function normalizeToTimestamp($value): ?int
    {
        if ($value === '' || $value === null) {
            return null;
        }
        if (is_int($value) || ctype_digit((string)$value)) {
            return (int)$value;
        }

        // Try common formats explicitly (more reliable than plain strtotime)
        $candidates = [
            'd.m.Y H:i',
            'Y-m-d\TH:i',
            'Y-m-d H:i',
            'd.m.Y',
            'Y-m-d',
        ];
        foreach ($candidates as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, (string)$value);
            if ($dt instanceof \DateTime) {
                return $dt->getTimestamp();
            }
        }

        // Fallback to strtotime for anything else
        $ts = strtotime((string)$value);
        return $ts !== false ? (int)$ts : null;
    }

    /** @inheritdoc */
    public function rules(): array
    {
        return [
            [['title', 'content'], 'required'],
            [['excerpt', 'content'], 'string'],

            // integers (published_at валидируем отдельно)
            [['status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_PUBLISHED]],

            [['title', 'meta_title'], 'string', 'max' => 255],
            ['slug', 'string', 'max' => 191],
            [['image', 'meta_description'], 'string', 'max' => 500],
            ['slug', 'unique'],

            // Allow mass-assignment
            [['published_at', 'image', 'meta_title', 'meta_description', 'excerpt'], 'safe'],

            // Convert published_at BEFORE validation
            [
                'published_at',
                'filter',
                'filter' => function ($value) {
                    return $this->normalizeToTimestamp($value);
                },
            ],
            // Then validate as integer (client-валидация может ругаться,
            // но на сервере всё уже приведено к int)
            ['published_at', 'integer', 'skipOnEmpty' => true],

            // file upload
            [
                ['imageFile'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => ['png','jpg','jpeg','webp','gif'],
                'maxSize' => 5 * 1024 * 1024,
            ],
        ];
    }

    public function beforeValidate()
    {
        // Extra safety: normalize again in case attribute was set bypassing rules()
        $this->published_at = $this->normalizeToTimestamp($this->published_at);
        return parent::beforeValidate();
    }

    /**
     * Auto-set published_at when publishing if empty.
     */
    public function beforeSave($insert)
    {
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

    public function isPublished(): bool
    {
        return (int)$this->status === self::STATUS_PUBLISHED;
    }

    public function getAuthor()
    {
        return $this->hasOne(\common\models\User::class, ['id' => 'created_by']);
    }

    public function getUpdater()
    {
        return $this->hasOne(\common\models\User::class, ['id' => 'updated_by']);
    }
}
