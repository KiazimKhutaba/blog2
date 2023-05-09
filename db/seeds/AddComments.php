<?php


use Phinx\Seed\AbstractSeed;

class AddComments extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++)
        {
            $data[] = [
                'parent_id' => 0,
                'reply_id' => 0,
                'post_id' => random_int(32, 35),
                'user_id' => random_int(1, 3),
                'content' => 'This is comment text. Simple comment'
            ];
        }

        $this->table('comments')->insert($data)->saveData();
    }
}
