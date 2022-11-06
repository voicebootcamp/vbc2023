<?php

namespace QuixNxt\Config\Contracts;

interface TransformerInterface
{
  public function transform($config, string $path = null);

  public function getBuilder(): string;
}
