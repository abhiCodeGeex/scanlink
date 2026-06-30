<?php

require_once(__DIR__.'/../config.php');
require_once(__DIR__ . '/function.php');
$Killbot = new Killbot($CONFIG_KILLBOT);
$Killbot->run();