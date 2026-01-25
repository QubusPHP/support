<?php

declare(strict_types=1);

namespace Qubus\Support;

use Stringable;

class HtmlString implements Stringable
{
    public function __construct(protected ?string $html = '')
    {
    }

    /**
     * Get the HTML string.
     *
     * @return string|null
     */
    public function toHtml(): ?string
    {
        return $this->html;
    }

    /**
     * Determine if the given HTML string is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return ($this->html ?? '') === '';
    }

    /**
     * Determine if the given HTML string is not empty.
     *
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    /**
     * Get the HTML string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toHtml() ?? '';
    }
}
