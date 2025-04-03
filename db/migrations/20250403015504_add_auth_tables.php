<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddAuthTables extends AbstractMigration
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
        // Create users table
        $users = $this->table('users', ['signed' => false]);
        $users->addColumn('username', 'string', ['limit' => 255])
              ->addColumn('password_hash', 'string', ['limit' => 255])
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at', 'timestamp', [
                  'default' => 'CURRENT_TIMESTAMP',
                  'update' => 'CURRENT_TIMESTAMP'
              ])
              ->addIndex(['username'], ['unique' => true])
              ->create();

        // Add user_id column to comments table
        $comments = $this->table('comments');
        $comments->addColumn('user_id', 'integer', [
                'signed' => false,
                'null' => true,
                'after' => 'recipe_id'
            ])
            ->save();

        // Add foreign key to comments table
        $comments->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'SET NULL',
                'update' => 'CASCADE'
            ])
            ->save();

        // Remove user_name column from comments table
        $comments->removeColumn('user_name')
                ->save();

        // Update comment_likes table to use user_id instead of user_name
        $commentLikes = $this->table('comment_likes');
        
        // Drop the primary key first
        $this->execute("ALTER TABLE comment_likes DROP PRIMARY KEY");
        
        // Add user_id column
        $commentLikes->addColumn('user_id', 'integer', [
                'signed' => false,
                'null' => true,
                'after' => 'comment_id'
            ])
            ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE'
            ])
            ->save();

        // Drop the user_name column
        $commentLikes->removeColumn('user_name')
                ->save();

        // Add new primary key
        $this->execute("ALTER TABLE comment_likes ADD PRIMARY KEY (comment_id, user_id)");
    }

    public function down(): void
    {
        // Add back user_name columns
        $comments = $this->table('comments');
        $comments->addColumn('user_name', 'string', ['limit' => 255, 'null' => true])
                ->save();

        // Restore usernames in comments
        $this->execute("UPDATE comments c 
            INNER JOIN users u ON c.user_id = u.id 
            SET c.user_name = u.username");

        // Remove user_id from comments
        $comments->dropForeignKey('user_id')
                ->removeColumn('user_id')
                ->save();

        // Add back user_name to comment_likes
        $commentLikes = $this->table('comment_likes');
        $commentLikes->addColumn('user_name', 'string', ['limit' => 255, 'null' => true])
                ->save();

        // Restore usernames in comment_likes
        $this->execute("UPDATE comment_likes cl 
            INNER JOIN users u ON cl.user_id = u.id 
            SET cl.user_name = u.username");

        // Update primary key for comment_likes
        $this->execute("ALTER TABLE comment_likes DROP PRIMARY KEY");
        $this->execute("ALTER TABLE comment_likes ADD PRIMARY KEY (comment_id, user_name)");

        // Remove user_id from comment_likes
        $commentLikes->dropForeignKey('user_id')
                    ->removeColumn('user_id')
                    ->save();

        // Drop users table
        $this->table('users')->drop()->save();
    }
}
