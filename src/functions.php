<?php

namespace MyBlog\Helpers;


use MyBlog\Repositories\BaseRepository;

function env(string $varname, $default = '')
{
    return !empty($_ENV[$varname]) ? $_ENV[$varname] : $default;
}

function isDev(): bool
{
    return env('APP_ENV') === 'dev';
}

function isProd(): bool
{
    return env('APP_ENV') === 'prod';
}

function map_keys(array $array, callable $callback): array
{
    $new_array = [];
    foreach ($array as $key => $value)
        $new_array[$callback($key)] = $value;

    return $new_array;
}


function buildTree(array &$list, string $parent_key = 'parent_id', string $child_key = 'comment_id')
{
    $tree = [];

    // делаем группировку по идентификатору родителя
    foreach ($list as $node) {
        $tree[$node[$parent_key]][] = $node;
    }

    // рекурсивная функция, создающая вложенную (древовидную) структуру
    $recursiveBuilder = function ($children) use (&$recursiveBuilder, $tree, $child_key) {

        foreach ($children as $key => $child)
        {
            $child_id = $child[$child_key];

            if (isset($tree[$child_id])) {
                $child['answers'] = $recursiveBuilder($tree[$child_id]);
            }

            $children[$key] = $child;
        }

        return $children;
    };

    return $recursiveBuilder($tree[0]);
}

/**
 * @throws \Exception
 */
function debug(mixed $value)
{
    throw new \Exception(json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

function pagination(BaseRepository $repository)
{
    try {

        // Find out how many items are in the table
        $total = $repository->query('
        SELECT
            COUNT(*)
        FROM
            table
    ')->fetchColumn();

        // How many items to list per page
        $limit = 20;

        // How many pages will there be
        $pages = ceil($total / $limit);

        // What page are we currently on?
        $page = min($pages, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array(
            'options' => array(
                'default'   => 1,
                'min_range' => 1,
            ),
        )));

        // Calculate the offset for the query
        $offset = ($page - 1)  * $limit;

        // Some information to display to the user
        $start = $offset + 1;
        $end = min(($offset + $limit), $total);

        // The "back" link
        $prevlink = ($page > 1) ? '<a href="?page=1" title="First page">&laquo;</a> <a href="?page=' . ($page - 1) . '" title="Previous page">&lsaquo;</a>' : '<span class="disabled">&laquo;</span> <span class="disabled">&lsaquo;</span>';

        // The "forward" link
        $nextlink = ($page < $pages) ? '<a href="?page=' . ($page + 1) . '" title="Next page">&rsaquo;</a> <a href="?page=' . $pages . '" title="Last page">&raquo;</a>' : '<span class="disabled">&rsaquo;</span> <span class="disabled">&raquo;</span>';

        // Display the paging information
        echo '<div id="paging"><p>', $prevlink, ' Page ', $page, ' of ', $pages, ' pages, displaying ', $start, '-', $end, ' of ', $total, ' results ', $nextlink, ' </p></div>';

        // Prepare the paged query
        $stmt = $dbh->prepare('
        SELECT
            *
        FROM
            table
        ORDER BY
            name
        LIMIT
            :limit
        OFFSET
            :offset
    ');

        // Bind the query params
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        // Do we have any results?
        if ($stmt->rowCount() > 0) {
            // Define how we want to fetch the results
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $iterator = new IteratorIterator($stmt);

            // Display the results
            foreach ($iterator as $row) {
                echo '<p>', $row['name'], '</p>';
            }

        } else {
            echo '<p>No results could be displayed.</p>';
        }

    } catch (\Exception $e) {
        echo '<p>', $e->getMessage(), '</p>';
    }
}