<?php

declare(strict_types=1);

namespace App\Serializer\Cache;

use Liip\Serializer\Compiler;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class SerializerWarmer implements CacheWarmerInterface
{
    /**
     * @var Compiler
     */
    private $compiler;

    public function __construct(Compiler $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * The serializer cache must be successfully warmed.
     */
    public function isOptional(): bool
    {
        return false;
    }

    /**
     * Generate serializer and deserializer code.
     *
     * @param string $cacheDir We ignore this and configure the directory on the generators instead
     */
    public function warmUp($cacheDir): void
    {
        $this->compiler->compile();
    }
}
