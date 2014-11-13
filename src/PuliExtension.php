<?php

/*
 * This file is part of the Puli package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Puli\Twig\PuliExtension;

use Puli\Twig\PuliExtension\NodeVisitor\LoadedByPuliTagger;
use Puli\Twig\PuliExtension\NodeVisitor\TemplatePathResolver;
use Puli\Twig\PuliExtension\TokenParser\LoadedByPuliTokenParser;
use Puli\Repository\ResourceRepositoryInterface;

/**
 * @since  1.0
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class PuliExtension extends \Twig_Extension
{
    /**
     * Priority for node visitors that want to work with relative path before
     * they are turned into absolute paths.
     */
    const PRE_RESOLVE_PATHS = 4;

    /**
     * Priority for node visitors that turn relative paths into absolute paths.
     */
    const RESOLVE_PATHS = 5;

    /**
     * Priority for node visitors that want to work with absolute paths.
     */
    const POST_RESOLVE_PATHS = 6;

    /**
     * @var ResourceRepositoryInterface
     */
    private $repo;

    public function __construct(ResourceRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'puli';
    }

    /**
     * Returns the node visitor instances to add to the existing list.
     *
     * @return \Twig_NodeVisitorInterface[] An array of Twig_NodeVisitorInterface instances
     */
    public function getNodeVisitors()
    {
        return array(
            new LoadedByPuliTagger(),
            new TemplatePathResolver($this->repo)
        );
    }

    /**
     * Returns the token parser instances to add to the existing list.
     *
     * @return array An array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances
     */
    public function getTokenParsers()
    {
        return array(new LoadedByPuliTokenParser());
    }

}
