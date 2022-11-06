<?php

namespace QuixNxt\Engine\Contracts;

interface AssetManager
{
  public static function getInstance(): self;

  public function add(string $str): self;

  public function addUrl(string $url): self;

  public function compile(): string;
}
