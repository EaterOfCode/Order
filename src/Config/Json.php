<?php

namespace Eater\Order\Config;

class Json extends Memory {
    public function __construct($logger, $file)
    {
      $this->logger = $logger;
      $logger->addDebug(sprintf('Loaded JSON config "%s"', $file));
      $this->config = json_decode(file_get_contents($file));
    }
}
