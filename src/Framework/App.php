<?php

declare(strict_types=1);

namespace Framework;

class App
{
  private Router $router;

  public function __construct(string $containerDefinitionsPath = null)
  {
    $this->router = new Router();
  }

  public function addRoute(string $path)
  {
    $this->router->addRoute($path);
  }
}
