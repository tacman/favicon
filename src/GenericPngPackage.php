<?php

declare(strict_types=1);

namespace Genkgo\Favicon;

final class GenericPngPackage implements PackageAppendInterface
{
    /**
     * @param array<int, int> $sizes
     */
    public function __construct(
        private readonly Input $input,
        private readonly string $name,
        private readonly string $shortName,
        private readonly string $themeColor,
        private readonly string $rootPrefix = '/',
        private readonly ?string $tileColor = null,
        private readonly string $backgroundColor = 'transparent',
        private readonly array $sizes = [32, 16, 48, 57, 76, 96, 128, 192, 228, 512],
        private readonly WebApplicationManifestDisplay $display = WebApplicationManifestDisplay::Standalone,
    ) {
    }

    public function package(): \Generator
    {
        $rootPrefix = $this->rootPrefix;
        if (\substr($rootPrefix, -1, 1) === '/') {
            $rootPrefix = \substr($rootPrefix, 0, -1);
        }

        $first = true;
        $manifestFormats = [];
        foreach ($this->sizes as $size) {
            $generator = new PngGenerator($this->input, $size, $this->backgroundColor);
            $blob = $generator->generate();
            if ($first) {
                yield 'favicon.png' => $blob;
            }

            $first = false;
            yield 'favicon-' . $size . 'x' . $size . '.png' => $blob;
            $manifestFormats[$size . 'x' . $size] = $rootPrefix . '/favicon-' . $size . 'x' . $size . '.png';
        }

        $manifest = new WebApplicationJsonGenerator(
            $this->display,
            $this->name,
            $this->shortName,
            $this->themeColor,
            $this->tileColor ?? '#FFFFFF',
            $manifestFormats
        );

        yield 'web-app-manifest.json' => $manifest->generate();
    }
}
