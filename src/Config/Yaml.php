<?php

namespace Eater\Order\Config;

use Symfony\Component\Yaml\Yaml as SymfonyYaml;

class Yaml extends Memory {

    public function __construct($logger, $file)
    {
        $this->logger = $logger;
        $logger->addDebug(sprintf('Loaded YAML config "%s"', $file));
        $this->config = SymfonyYaml::parse(file_get_contents($file));
    }

}
