<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCommentsTable extends AbstractMigration
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
        $this->table('comments', ['signed' => false, 'collation' => 'utf8mb4_general_ci' ])
            ->addColumn('parent_id', 'integer', ['signed' => false])
            ->addColumn('reply_id', 'integer', ['signed' => false])
            ->addColumn('post_id', 'integer', ['signed' => false])
            ->addColumn('user_id', 'integer', ['signed' => false])
            ->addColumn('content', 'string', ['null' => false])
            ->addTimestamps()
            ->create();
    }
}
