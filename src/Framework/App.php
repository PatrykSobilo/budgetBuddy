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

  public function run() {
    $sitePath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    $httpMethod = $_SERVER['REQUEST_METHOD'];

    $this->router->dispatchContent($sitePath, $httpMethod);
  }

  public function getRoute(string $sitePath, array $displayingController)
  {
    $this->router->addRoute('GET', $sitePath, $displayingController);
  }
}
