<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Pdf\Asset;

use Twig\Environment;

final class TemplateAsset extends AbstractAsset
{
    private $templating;

    private $templateName;
    private $templateParameters;

    public function __construct(Environment $templating, string $templateName, array $templateParameters = [])
    {
        $this->templating = $templating;

        $this->templateName = $templateName;
        $this->templateParameters = $templateParameters;
    }

    public function getData(): string
    {
        return $this->templating->render($this->templateName, $this->templateParameters);
    }
}
