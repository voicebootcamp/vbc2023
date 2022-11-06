<?php

namespace QuixNxt\Engine\Concerns;

trait HasHooks
{
    private $beforeHooks = [];
    private $afterHooks = [];

  /**
   * @param  callable  $callable
   *
   * @return $this
   *
   * @since 3.0.0
   */
    public function before(callable $callable): self
    {
        $this->beforeHooks[] = $callable;

        return $this;
    }

  /**
   * @param $data
   *
   * @return mixed
   * @since 3.0.0
   */
    protected function runBeforeHooks($data)
    {
        return $this->mapThrough($this->beforeHooks, $data);
    }

  /**
   * @param  callable  $callable
   *
   * @return $this
   *
   * @since 3.0.0
   */
    public function after(callable $callable): self
    {
        $this->afterHooks[] = $callable;

        return $this;
    }

  /**
   * @param $data
   *
   * @return mixed
   *
   * @since 3.0.0
   */
    protected function runAfterHooks($data)
    {
        return $this->mapThrough($this->afterHooks, $data);
    }

  /**
   * @param  array  $callables
   * @param $data
   *
   * @return mixed
   * @since 3.0.0
   */
    private function mapThrough(array $callables, $data)
    {
        foreach ($callables as $callable) {
            $data = $callable($data);
        }

        return $data;
    }
}
