#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/src/AppBundle/Command/VimeoDownloaderCommand.php';

use Symfony\Component\Console\Application;
use AppBundle\Command\VimeoDownloaderCommand;

$application = new Application();

$application->add(new VimeoDownloaderCommand());

$application->run();