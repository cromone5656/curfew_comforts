<?php

use Phinx\Migration\AbstractMigration;

final class CreateCommentsTable extends AbstractMigration
{
    public function up(): void
    {
        if ($this->hasTable('comments')) {
            $this->table('comments')->drop()->save();
        }

        $table = $this->table('comments', [
            'id' => false,          // Disable auto-ID
            'primary_key' => ['id'], // Define primary key
            'engine' => 'InnoDB'
        ]);

        $table->addColumn('id', 'integer', [
            'identity' => true,
            'signed' => false,     // Match recipes.id type
            'null' => false
        ])
            ->addColumn('recipe_id', 'integer', [
                'signed' => false,     // Must match recipes.id type
                'null' => false
            ])
            ->addColumn('user_name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('content', 'text', ['null' => false])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'update' => 'CURRENT_TIMESTAMP'
            ])
            ->addIndex(['recipe_id'])
            ->addForeignKey('recipe_id', 'recipes', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE'
            ])
            ->create();
    }

    public function down(): void
    {
        if ($this->hasTable('comments')) {
            $this->table('comments')->drop()->save();
        }
    }
}
