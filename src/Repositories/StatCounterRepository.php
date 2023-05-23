<?php

namespace MyBlog\Repositories;

use MyBlog\Core\Db\DatabaseInterface;
use Symfony\Component\HttpFoundation\Request;

class StatCounterRepository
{
    public function __construct
    (
        private readonly DatabaseInterface $db
    )
    {
    }


    public function countViews(Request $request, int $obj_id)
    {
        $sql = 'INSERT INTO stat_counter (post_id, ip, user_agent, referer) VALUES (:post_id, :ip, :user_agent, :referer)  
                ON CONFLICT(post_id, ip) DO UPDATE SET views_count = stat_counter.views_count + 1';

        return $this->db->query($sql, [
            ':post_id' => $obj_id,
            ':ip' => ip2long($request->getClientIp()),
            //':views_count' => 1,
            ':user_agent' => $request->headers->get('user-agent', ''),
            ':referer' => $request->headers->get('referer', '')
        ]);
    }
}