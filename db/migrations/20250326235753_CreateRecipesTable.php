<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRecipesTable extends AbstractMigration
{
    public function up(): void
    {
        // Drop the table if it already exists
        if ($this->hasTable('recipes')) {
            $this->table('recipes')->drop()->save();
        }

        // Create the table again
        $table = $this->table('recipes');
        $table->addColumn('featured', 'boolean', ['default' => false])
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
        // Drop the table in down method
        if ($this->hasTable('recipes')) {
            $this->table('recipes')->drop()->save();
        }
    }
}
