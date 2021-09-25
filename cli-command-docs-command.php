<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

$cli_command_docs_autoloader = dirname( __FILE__ ) . '/vendor/autoload.php';
if ( file_exists( $cli_command_docs_autoloader ) ) {
	require_once $cli_command_docs_autoloader;
}

// putenv( 'WP_CLI_SUPPRESS_GLOBAL_PARAMS=true' );
WP_CLI::add_command(
	'cli-command-docs',
	'Cli_Command_Docs_Command'
);
