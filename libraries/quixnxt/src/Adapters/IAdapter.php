<?php

namespace QuixNxt\Adapters;

interface IAdapter
{
  /**
   * @param  array  $nodes
   *
   * @return array
   *
   * @since 3.0.0
   */
  public function transform(array $nodes): array;
}
