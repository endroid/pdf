<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Pdf\Asset;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class ControllerAsset extends AbstractAsset
{
    private $kernel;
    private $requestStack;

    private $controller;
    private $controllerParameters;

    public function __construct(
        HttpKernelInterface $kernel,
        RequestStack $requestStack,
        string $controller,
        array $controllerParameters = []
    ) {
        $this->kernel = $kernel;
        $this->requestStack = $requestStack;
        $this->controller = $controller;
        $this->controllerParameters = $controllerParameters;
    }

    public function getData(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        $this->controllerParameters['_forwarded'] = $request->attributes;
        $this->controllerParameters['_controller'] = $this->controller;
        $subRequest = $request->duplicate([], null, $this->controllerParameters);

        $response = $this->kernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);

        return $response->getContent();
    }
}
