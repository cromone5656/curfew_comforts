<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRecipesTable extends AbstractMigration
{
    public function up(): void
    {
        if ($this->hasTable('recipes')) {
            $this->table('recipes')->drop()->save();
        }

        // Disable Phinx's auto-id and define primary key explicitly
        $table = $this->table('recipes', [
            'id' => false,          // Disable auto-added 'id'
            'primary_key' => ['id'] // Manually define 'id' as primary key
        ]);

        $table->addColumn('id', 'integer', [
            'identity' => true,     // Auto-increment
            'signed' => false,      // UNSIGNED
            'null' => false
        ])
            ->addColumn('featured', 'boolean', [
                'default' => false,
                'null' => false // Add this line
            ])
            ->addColumn('description', 'text')
            ->addColumn('name', 'string', ['limit' => 255])
            ->addColumn('ingredients', 'text')
            ->addColumn('instructions', 'text')
            ->addColumn('category', 'string', ['limit' => 255])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();
    }

    public function down(): void
    {
        if ($this->hasTable('recipes')) {
            $this->table('recipes')->drop()->save();
        }
    }
}
