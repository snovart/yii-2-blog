<?php
namespace common\components;

use Yii;
use yii\base\Component;
use yii\web\UploadedFile;

class ImageStorage extends Component
{
    /**
     * Save uploaded file and return a relative path.
     * Example returned value: /uploads/posts/2025/11/unique.png
     */
    public function save(UploadedFile $file, string $subdir = 'posts'): ?string
    {
        // FS base points to .../web/uploads
        $root = Yii::getAlias(Yii::$app->params['uploadsBasePath']);
        $dir  = trim($subdir, '/');
        $ym   = date('Y/m');

        $targetDir = rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $ym;
        if (!is_dir($targetDir) && !@mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
            return null;
        }

        $name = sprintf('%s_%s.%s',
            $dir,
            uniqid('', true),
            strtolower($file->getExtension())
        );

        $fullPath = $targetDir . DIRECTORY_SEPARATOR . $name;
        if (!$file->saveAs($fullPath, false)) {
            return null;
        }

        // Store relative path (always starting with /uploads)
        return sprintf('/uploads/%s/%s/%s', $dir, $ym, $name);
    }

    /**
     * Delete file by relative or absolute URL.
     * Accepts: '/uploads/...' or full 'http(s)://domain/uploads/...'
     */
    public function deleteByUrl(string $url): bool
    {
        // Extract path part if full URL was passed
        $path = parse_url($url, PHP_URL_PATH) ?: $url;

        // Map '/uploads/... ' -> relative part inside uploads dir
        // because uploadsBasePath already points to .../web/uploads
        $relativeInsideUploads = ltrim($path, '/');
        if (stripos($relativeInsideUploads, 'uploads/') === 0) {
            $relativeInsideUploads = substr($relativeInsideUploads, strlen('uploads/')); // remove 'uploads/'
        }

        $root = Yii::getAlias(Yii::$app->params['uploadsBasePath']); // .../web/uploads
        $full = rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $relativeInsideUploads;

        // Normalize slashes for safety
        $full = preg_replace('#[\\/]+#', DIRECTORY_SEPARATOR, $full);

        return is_file($full) ? @unlink($full) : false;
    }

    /**
     * Build an absolute public URL to display in a browser.
     * Input: relative DB value like '/uploads/...'
     * Uses 'uploadsBaseUrl' from params, e.g. 'http://blog.local'
     */
    public function getPublicUrl(?string $relativePath): ?string
    {
        if (!$relativePath) {
            return null;
        }

        // Already absolute? Return as is
        if (preg_match('~^https?://~i', $relativePath)) {
            return $relativePath;
        }

        // Use the correct params key
        $base = rtrim(Yii::$app->params['uploadsBaseUrl'], '/');
        return $base . '/' . ltrim($relativePath, '/');
    }
}
