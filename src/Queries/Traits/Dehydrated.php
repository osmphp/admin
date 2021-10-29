<?php

namespace Osm\Data\Queries\Traits;

/**
 * @property ?bool $dehydrated
 */
trait Dehydrated
{
    public function dehydrated(bool $dehydrated = true): static {
        $this->dehydrated = $dehydrated;

        return $this;
    }
}