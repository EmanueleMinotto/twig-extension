<?php

/*
 * This file is part of the puli/twig-puli-extension package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Puli\TwigExtension;

use Puli\AssetPlugin\Api\UrlGenerator\AssetUrlGenerator;
use Puli\Repository\Api\ResourceRepository;
use Puli\TwigExtension\NodeVisitor\LoadedByPuliTagger;
use Puli\TwigExtension\NodeVisitor\TemplatePathResolver;
use Puli\TwigExtension\TokenParser\LoadedByPuliTokenParser;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * @since  1.0
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class PuliExtension extends Twig_Extension
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
     * @var ResourceRepository
     */
    private $repo;

    /**
     * @var AssetUrlGenerator
     */
    private $urlGenerator;

    public function __construct(ResourceRepository $repo, AssetUrlGenerator $urlGenerator = null)
    {
        $this->repo = $repo;
        $this->urlGenerator = $urlGenerator;
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
     * {@inheritdoc}
     */
    public function getNodeVisitors()
    {
        return array(
            new LoadedByPuliTagger(),
            new TemplatePathResolver($this->repo, $this->urlGenerator)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return array(new LoadedByPuliTokenParser());
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        if (!$this->urlGenerator) {
            return array();
        }

        return array(
            new Twig_SimpleFunction('asset_url', array($this->urlGenerator, 'generateUrl')),
        );
    }
}
