<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddStatCounterTable extends AbstractMigration
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
        $this
            ->table('stat_counter', ['signed' => false, 'collation' => 'utf8mb4_general_ci' ])
            ->addColumn('post_id', 'integer', ['signed' => false])
            ->addColumn('ip', 'integer', ['signed' => false])
            ->addColumn('views_count', 'integer', ['signed' => false, 'default' => 1])
            ->addColumn('user_agent', 'string')
            ->addColumn('referer', 'string')
            ->addTimestamps()
            ->addIndex(['post_id', 'ip'], ['unique' => true])
            ->create();
    }
}
