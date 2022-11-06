<?php

namespace QuixNxt\NodeCleaner;

interface INodeCleaner
{
  /**
   * @param  string  $slug
   * @param  array  $data
   *
   * @return array
   * @since 3.0.0
   */
  public function cleanUp(string $slug, array $data): array;

  /**
   * @param  array  $nodes
   *
   * @return array
   *
   * @since 3.0.0
   */
  public function mergeRecursive(array $nodes): array;

  /**
   * @param  string  $slug
   * @param  array  $node
   *
   * @return array
   * @since 3.0.0
   */
  public function merge(string $slug, array $node): array;
}
