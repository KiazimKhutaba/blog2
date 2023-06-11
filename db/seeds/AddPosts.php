<?php


use Phinx\Seed\AbstractSeed;

class AddPosts extends AbstractSeed
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
        $posts = [
            [
                'title' => 'This is first post',
                'content' => 'This is simple content that create for testing purposes',
                'user_id' => 1
            ],
            [
                'title' => 'This is second post',
                'content' => 'This is simple content that create for testing purposes',
                'user_id' => 1
            ],
            [
                'title' => 'This is third post',
                'content' => 'This is simple content that create for testing purposes',
                'user_id' => 1
            ],
            [
                'title' => 'This is first post of simple user',
                'content' => 'This is simple content that create for testing purposes',
                'user_id' => 2
            ],
        ];

        $this->table('posts')->insert($posts)->saveData();
    }
}
