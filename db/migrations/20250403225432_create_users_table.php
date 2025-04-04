<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUsersTable extends AbstractMigration
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
        if (!$this->hasTable('users')) {
            $this->table('users')
                ->addColumn('username', 'string', [
                    'limit' => 255,
                    'null' => false
                ])
                ->addColumn('password', 'string', [
                    'limit' => 255,
                    'null' => false
                ])
                ->addColumn('created_at', 'timestamp', [
                    'default' => 'CURRENT_TIMESTAMP',
                    'null' => false
                ])
                ->addIndex(['username'], ['unique' => true])
                ->create();
        }

        // Add is_admin column if it doesn't exist
        if (!$this->table('users')->hasColumn('is_admin')) {
            $this->table('users')
                ->addColumn('is_admin', 'boolean', [
                    'default' => false,
                    'null' => false,
                    'after' => 'password_hash'
                ])
                ->update();
        }
    }
}
