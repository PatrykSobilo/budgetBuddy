<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Services\{TransactionService, ReceiptService};

class ReceiptController
{
  public function __construct(
    private TemplateEngine $view,
    private TransactionService $transactionService,
    private ReceiptService $receiptService
  ) {
  }

  public function uploadView(array $params)
  {

  }

  public function upload(array $params)
  {
    
  }

  public function download(array $params)
  {

  }

  public function delete(array $params)
  {
    
  }
}