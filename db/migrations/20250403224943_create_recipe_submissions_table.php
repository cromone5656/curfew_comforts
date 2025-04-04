<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRecipeSubmissionsTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('recipe_submissions', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => 'Stores recipe submissions from users'
        ]);

        $table->addColumn('id', 'integer', [
            'signed' => false,
            'identity' => true,
            'null' => false
        ])
        ->addColumn('name', 'string', [
            'limit' => 255,
            'null' => false,
            'comment' => 'Submitter\'s name'
        ])
        ->addColumn('email', 'string', [
            'limit' => 255,
            'null' => false,
            'comment' => 'Submitter\'s email'
        ])
        ->addColumn('recipe_name', 'string', [
            'limit' => 255,
            'null' => false,
            'comment' => 'Name of the recipe'
        ])
        ->addColumn('category', 'string', [
            'limit' => 50,
            'null' => false,
            'comment' => 'Recipe category'
        ])
        ->addColumn('ingredients', 'text', [
            'null' => false,
            'comment' => 'Recipe ingredients'
        ])
        ->addColumn('instructions', 'text', [
            'null' => false,
            'comment' => 'Recipe instructions'
        ])
        ->addColumn('notes', 'text', [
            'null' => true,
            'comment' => 'Additional notes about the recipe'
        ])
        ->addColumn('image_url', 'string', [
            'limit' => 255,
            'null' => true,
            'comment' => 'URL to recipe image'
        ])
        ->addColumn('created_at', 'timestamp', [
            'default' => 'CURRENT_TIMESTAMP',
            'null' => false
        ])
        ->addColumn('status', 'enum', [
            'values' => ['pending', 'approved', 'rejected'],
            'default' => 'pending',
            'null' => false,
            'comment' => 'Submission status'
        ])
        ->create();
    }
}
