<?php

use Phinx\Migration\AbstractMigration;

class CreateRecipesTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('recipes');
        $table->addColumn('name', 'string', ['limit' => 255])
            ->addColumn('featured', 'boolean', [
                'default' => false,
                'null' => false
            ])  // Closing bracket for featured options
            ->addColumn('description', 'text')  // Added missing description column
            ->addColumn('ingredients', 'text')
            ->addColumn('instructions', 'text')
            ->addColumn('category', 'string', ['limit' => 100])
            ->addColumn('image_url', 'string', [
                'limit' => 255,
                'null' => true,
                'default' => null,
                'after' => 'description'
            ])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->create();
    }
}
