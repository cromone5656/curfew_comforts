<?php

use Phinx\Migration\AbstractMigration;

final class CreateCommentLikesTable extends AbstractMigration
{
    public function up(): void
    {
        if ($this->hasTable('comment_likes')) {
            $this->table('comment_likes')->drop()->save();
        }

        $table = $this->table('comment_likes', [
            'id' => false,
            'primary_key' => ['comment_id', 'user_name'], // Composite primary key
            'engine' => 'InnoDB'
        ]);

        $table->addColumn('comment_id', 'integer', [
            'signed' => false,     // Must match comments.id type
            'null' => false
        ])
            ->addColumn('user_name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['comment_id'])
            ->addForeignKey('comment_id', 'comments', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE'
            ])
            ->create();
    }

    public function down(): void
    {
        if ($this->hasTable('comment_likes')) {
            $this->table('comment_likes')->drop()->save();
        }
    }
}
