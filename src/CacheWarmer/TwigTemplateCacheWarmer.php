<?php

/*
 * This file is part of the puli/twig-puli-extension package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Puli\Extension\Twig\CacheWarmer;

use Puli\Repository\Api\ResourceRepository;
use Puli\Repository\Resource\Iterator\RecursiveResourceIteratorIterator;
use Puli\Repository\Resource\Iterator\ResourceCollectionIterator;
use Puli\Repository\Resource\Iterator\ResourceFilterIterator;
use RuntimeException;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Twig_Environment;
use Twig_Error;

/**
 * Generates the Twig cache for all templates in the resource repository.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class TwigTemplateCacheWarmer implements CacheWarmerInterface
{
    /**
     * @var ResourceRepository
     */
    private $repo;

    /**
     * @var string
     */
    private $suffix;

    /**
     * @var Twig_Environment
     */
    private $twig;

    public function __construct(ResourceRepository $repo, Twig_Environment $twig, $suffix = '.twig')
    {
        $this->repo = $repo;
        $this->suffix = $suffix;
        $this->twig = $twig;
    }

    /**
     * Warms up the cache.
     *
     * @param string $cacheDir The cache directory
     *
     * @throws RuntimeException If setEnvironment() wasn't called
     */
    public function warmUp($cacheDir)
    {
        $iterator = new ResourceFilterIterator(
            new RecursiveResourceIteratorIterator(
                new ResourceCollectionIterator(
                    $this->repo->get('/')->listChildren(),
                    ResourceCollectionIterator::CURRENT_AS_PATH
                ),
                RecursiveResourceIteratorIterator::SELF_FIRST
            ),
            $this->suffix,
            ResourceFilterIterator::FILTER_BY_NAME | ResourceFilterIterator::MATCH_SUFFIX
        );

        foreach ($iterator as $path) {
            try {
                $this->twig->loadTemplate($path);
            } catch (Twig_Error $e) {
                // Problem during compilation, stop
            }
        }
    }

    /**
     * Returns whether this warmer is optional or not.
     *
     * @return Boolean always true
     */
    public function isOptional()
    {
        return true;
    }
}
