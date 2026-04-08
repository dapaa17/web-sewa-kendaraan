<?php

declare(strict_types=1);

$port = getenv('PORT') ?: '8080';
$port = preg_replace('/\D+/', '', $port) ?: '8080';

$command = sprintf('php -S 0.0.0.0:%s -t public', $port);
passthru($command, $exitCode);

exit($exitCode);
