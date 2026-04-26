<?php

declare(strict_types=1);

/**
 * Run a shell command and stop startup on failure.
 */
function runOrFail(string $command, string $stepName): void
{
	passthru($command, $exitCode);

	if ($exitCode !== 0) {
		fwrite(STDERR, sprintf("[startup] %s failed with exit code %d\n", $stepName, $exitCode));
		exit($exitCode);
	}
}

$autoMigrateRaw = getenv('AUTO_RUN_MIGRATIONS');
$autoMigrate = $autoMigrateRaw === false
	? true
	: filter_var($autoMigrateRaw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;

if ($autoMigrate) {
	echo "[startup] Running database migrations...\n";
	runOrFail('php artisan migrate --force --no-interaction', 'database migration');
}

$port = getenv('PORT') ?: '8080';
$port = preg_replace('/\D+/', '', $port) ?: '8080';

$command = sprintf('php -S 0.0.0.0:%s -t public', $port);
passthru($command, $exitCode);

exit($exitCode);
