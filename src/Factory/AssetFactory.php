<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Pdf\Factory;

use Endroid\Pdf\Asset\AbstractAsset;
use Endroid\Pdf\Asset\CachedAsset;
use Endroid\Pdf\Asset\ControllerAsset;
use Endroid\Pdf\Asset\DataAsset;
use Endroid\Pdf\Asset\FileAsset;
use Endroid\Pdf\Asset\TemplateAsset;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

final class AssetFactory
{
    private $kernel;
    private $requestStack;
    private $templating;
    private $cache;
    private $optionsResolver;

    public function __construct(
        HttpKernelInterface $kernel = null,
        RequestStack $requestStack = null,
        Environment $templating = null,
        AdapterInterface $cache = null
    ) {
        $this->kernel = $kernel;
        $this->requestStack = $requestStack;
        $this->templating = $templating;
        $this->cache = $cache;

        $this->configureOptions();
    }

    private function configureOptions()
    {
        $this->optionsResolver = new OptionsResolver();
        $this->optionsResolver->setDefaults([
            'controller' => null,
            'data' => null,
            'file' => null,
            'template' => null,
            'parameters' => [],
            'cache' => null,
        ]);
    }

    public function create(array $options = []): AbstractAsset
    {
        $options = $this->optionsResolver->resolve($options);

        if (isset($options['controller'])) {
            $asset = new ControllerAsset($this->kernel, $this->requestStack, $options['controller'], $options['parameters']);
        } elseif (isset($options['file'])) {
            $asset = new FileAsset($options['file']);
        } elseif (isset($options['template'])) {
            $asset = new TemplateAsset($this->templating, $options['template'], $options['parameters']);
        } else {
            $asset = new DataAsset($options['data']);
        }

        if (isset($options['cache'])) {
            $asset = new CachedAsset($asset, $options['cache'], $this->cache);
        }

        return $asset;
    }
}
