<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
WP_CLI::add_command( 'tagger', 'tagger_cli' );

/**
 * CLI Commands for Tagger
 */
class Tagger_cli extends WP_CLI_Command {

	/**
	 * Exports a report of suggested tags for each post
	 *
	 * @var array $args positional arguments.
	 * @var array $assoc_args associated arguments.
	 *
	 */
	public function tag_posts( $args, $assoc_args ) {

		if ( empty( $args[0] ) ) {
			WP_CLI::error( 'Please include a CSV file' );
		}

		$tags = tagger()->parse_csv( $args[0] );

		if ( ! $tags ) {
			WP_CLI::error( sprintf( "'%s' doesn't exist.",  $args[0] ) );
		}

		$threshold = 2;

		if ( ! empty( $assoc_args['threshold'] ) ) {
			$threshold = $assoc_args['threshold'];
		}

		$post_type = array( 'post' );

		/**
		 * If post type argument presented, split into array, trime, and check for valid post types
		 */
		if ( ! empty( $assoc_args['post_type'] ) ) {
			// explode comma seperated value into array.
			$post_type = explode( ',', $assoc_args['post_type'] );
			// trim white space.
			$post_type = array_map( 'trim', $post_type );
			// Check for valid post type.
			foreach ( $post_type as $type ) {
				if ( ! post_type_exists( $type ) ) {
					WP_CLI::error( $type . ' is not a valid post type' );
				}
			}
		}

		$total_posts = 0;

		foreach ( $post_type as $type ) {
			$total_posts += wp_count_posts( $type )->publish;
		}

		// start progress bar.
		$progress     = WP_CLI\Utils\make_progress_bar( 'Tagging Posts: ', $total_posts );
		$posts_tagged = 0;
		$more         = true;
		$page         = 0;
		$number       = 5;

		$tagged_posts = array();

		// this.
		while ( $more ) {
			$offset  = $page * $number;
			$args    = array(
				'number'                 => $number,
				'offset'                 => $offset,
				'post_type'              => $post_type,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			);
			$query   = new WP_Query( $args );
			$results = $query->get_posts();

			if ( ! empty( count( $results ) > 1 ) ) {
				foreach ( $results as $post ) {
					// do this thing here.
					$tagged_posts[ $post->ID ] = tagger()->get_post_terms( $post->ID, $tags, $threshold );

					$progress->tick();
					$posts_tagged ++;
				}
				$page ++;
			} else {
				$more = false;
			}
		}

		$file_date = date( 'Ymd_hia' );
		$file_post_types = implode( '-', $post_type );
		$file_threshold = 'threshold=' . $threshold;

		$filename = sprintf( 'tagging_results_%s_%s_%s.csv', $file_date, $file_post_types, $file_threshold );

		$this->put_csv( $filename, $tagged_posts );
		wp_CLI::success( sprintf( "'%s' created with %d posts anaylzed.", $filename, $total_posts ) );

	}

	/**
	 * Writes an associative array to a CSV file
	 *
	 * @param string $filename file name.
	 * @param array  $contents array of values.
	 *
	 * @return string
	 */
	protected function put_csv( $filename = '', $contents = array() ) {

		if ( '' === $filename ) {
			return 'Please provide a filename to write';
		}

		if ( empty( $contents ) ) {
			return 'Nothing to write';
		}

		$file = fopen( TAGGER_RESULTS_PATH . $filename, 'w' );

		// put the header row.
		fputcsv( $file, array( 'post_id', 'tags' ) );

		// put the rows.
		foreach ( $contents as $key => $value ) {

			$value = is_array( $value ) ? implode( ', ', $value ) : $value;
			$row = array( $key, $value );
			fputcsv( $file, $row );
		}

		fclose( $file );

	}


}
