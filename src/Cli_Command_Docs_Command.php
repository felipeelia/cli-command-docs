<?php

class Cli_Command_Docs_Command extends WP_CLI_Command {
	/**
	 * Generate a markdown content with the description of all WP-CLI subcommands
	 * of a given command.
	 * 
	 * ## OPTIONS
	 *
	 * <command>
	 * : Get help on a specific command.
	 * 
	 * [--custom-intro=<string>]
	 * : Pass if you want to have an intro different from the main command short desc.
	 *
	 * [--custom-order=<array>]
	 * : Array with custom order of subcommands.
	 *
	 * [--remove=<array>]
	 * : Array with subcommands that should not be documented.
	 *
	 * @subcommand generate-md
	 * @param array $args Positional CLI args.
	 * @param array $assoc_args Associative CLI args.
	 */
	public function __invoke( $args, $assoc_args ) {
		$command_list = [ $args[0] ];
		$command = WP_CLI::get_runner()->find_command_to_run( $command_list );

		$command = $command[0];

		echo ( isset( $assoc_args['custom-intro'] ) ) ? $assoc_args['custom-intro'] : $command->get_shortdesc();
		echo "\n\n";

		echo ( $command->longdesc ) ? $command->longdesc . "\n\n" : '';

		$subcommands = $command->get_subcommands();
		if ( isset( $assoc_args['remove'] ) ) {
			$assoc_args['remove'] = explode( ',', $assoc_args['remove'] );
			foreach ( $assoc_args['remove'] as $subcommand ) {
				unset( $subcommands[ $subcommand ] );
			}
		}
		
		if ( isset( $assoc_args['custom-order'] ) ) {
			$assoc_args['custom-order'] = explode( ',', $assoc_args['custom-order'] );
			$ordered = array();
			foreach ( $assoc_args['custom-order'] as $key ) {
				if ( array_key_exists( $key, $subcommands ) ) {
					$ordered[ $key ] = $subcommands[$key];
					unset( $subcommands[$key] );
				}
			}
			$subcommands = $ordered + $subcommands;
		}

		foreach ( $subcommands as $subcommand ) {
			$usage = trim( $subcommand->get_usage( '' ) );
			echo "* `{$usage}` \n\n";
			echo "\t{$subcommand->get_shortdesc()}";
			
			$longdesc = $subcommand->longdesc;
			if ( false !== strpos( $longdesc, '## OPTIONS' ) ) {
				$longdesc = substr( $longdesc, 0, strpos( $longdesc, '## OPTIONS' ) );
			}
			$longdesc = trim( $longdesc );
			if ( $longdesc ) {
				echo "\n\n\t{$longdesc}";
			}

			preg_match_all( '/(.+?)[\r\n]+:([^\r\n]*)/', $subcommand->longdesc, $matches );
			if ( ! empty( $matches[1] ) ) {
				echo "\n";
				foreach ( $matches[1] as $index => $parameter ) {
					$parameter_desc = ( isset( $matches[2][ $index ] ) ) ? ":{$matches[2][ $index ]}" : '';
					echo "\n\t* `{$parameter}`{$parameter_desc}";
				}
			}

			echo "\n\n";
		}
	}
}
