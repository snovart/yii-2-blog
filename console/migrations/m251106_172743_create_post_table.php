<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%post}}`.
 */
class m251106_172743_create_post_table extends Migration
{
    public function safeUp()
    {
        // Create blog posts table
        $this->createTable('{{%post}}', [
            'id' => $this->primaryKey(),

            // Content
            'title' => $this->string(255)->notNull()->comment('Post title'),
            'slug' => $this->string(191)->notNull()->unique()->comment('URL slug'),
            'excerpt' => $this->text()->null()->comment('Short summary'),
            'content' => $this->text()->notNull()->comment('Full content (HTML or Markdown)'),

            // Media
            'image' => $this->string(500)->null()->comment('Preview/cover image URL'),

            // SEO
            'meta_title' => $this->string(255)->null()->comment('SEO title'),
            'meta_description' => $this->string(500)->null()->comment('SEO description'),

            // Status & times
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('0 = draft, 1 = published'),
            'published_at' => $this->integer()->null()->comment('Publication time (UNIX timestamp)'),

            // Auditing
            'created_by' => $this->integer()->null()->comment('Author ID'),
            'updated_by' => $this->integer()->null()->comment('Editor ID'),
            'created_at' => $this->integer()->notNull()->comment('Created at (UNIX timestamp)'),
            'updated_at' => $this->integer()->notNull()->comment('Updated at (UNIX timestamp)'),
        ]);

        // Indexes (keep as is, helpful for routing and listings)
        $this->createIndex('idx-post-slug', '{{%post}}', 'slug', true);
        $this->createIndex('idx-post-status', '{{%post}}', 'status');
        $this->createIndex('idx-post-published_at', '{{%post}}', 'published_at');
    }

    public function safeDown()
    {
        $this->dropTable('{{%post}}');
    }
}
