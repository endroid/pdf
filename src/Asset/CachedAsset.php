<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Pdf\Asset;

use Symfony\Component\Cache\Adapter\AdapterInterface;

final class CachedAsset extends AbstractAsset
{
    private $asset;
    private $key;
    private $cache;

    public function __construct(AbstractAsset $asset, string $key, AdapterInterface $cache)
    {
        $this->asset = $asset;
        $this->key = $key;
        $this->cache = $cache;
    }

    public function getData(): string
    {
        $cacheItem = $this->cache->getItem($this->key);
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $data = $this->asset->getData();

        $cacheItem->set($data);
        $this->cache->save($cacheItem);

        return $data;
    }
}
