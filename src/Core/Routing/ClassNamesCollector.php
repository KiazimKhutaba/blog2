<?php

namespace MyBlog\Core\Routing;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

class ClassNamesCollector
{
    /**
     * @param string $path
     * @param string $namespace_prefix
     * @return array
     */
    public static function collect(string $path, string $namespace_prefix = ''): array
    {
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        $ite = new RegexIterator($it, '/Controller\.php$/i', RegexIterator::MATCH);

        $files = [];
        foreach ($ite as $file)
        {
            $class = $namespace_prefix . str_replace('.php', '', $ite->getSubPathname());
            $class = str_replace(['\\', '/'], '\\', $class);

            $files[] = $class;
        }

        return $files;
    }
}