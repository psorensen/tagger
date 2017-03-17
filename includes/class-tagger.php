<?php

/**
 * Tagger csv parsing and tagging functions
 *
 * @since 1.0
 * @package Tagger
 */
class Tagger {

	/**
	 * Placeholder constructor
	 *
	 * @since 0.1.0
	 */
	public function __construct() { }

	/**
	 * Convert contents of CSV to associative array
	 *
	 * @param string $csv csv file of tags and terms.
	 *
	 * @since 0.1.0
	 * @link http://php.net/manual/en/function.str-getcsv.php
	 * @return bool|array $tags false if file not included, array of tags and terms
	 */
	public function parse_csv( $csv = '' ) {
		if ( ! file_exists( $csv ) || ! isset( $csv ) ) {
			return false;
		}

		$csv_contents = array_map( 'str_getcsv', file( $csv ) );
		$tags = array();
		foreach ( $csv_contents as $contents ) {
			$tags[ $contents[0] ] = array_map( 'trim', explode( ',', $contents[1] ) );
		}

		return $tags;
	}

	/**
	 * Concatenate post title and post content
	 *
	 * @param int $post_id ID of post to retrieve content from.
	 *
	 * @since 0.1.0
	 * @return string|void concatenated string of post title and content
	 */
	public function get_post_contents( $post_id ) {
		if ( ! isset( $post_id ) ) {
			return false;
		}

		$post = get_post( $post_id );

		if ( $post ) {
			return $post->post_title . ' ' . $post->post_content;
		}

		return false;

	}

	/**
	 * Scans content for occurences of terms
	 *
	 * @param array  $terms phrases to search for.
	 * @param string $content content to search.
	 * @param int    $threshold required minimum occurrences.
	 *
	 * @since 0.1.0
	 * @return bool true if occurrences meet required threshold
	 */
	public function content_has_terms( $terms = array(), $content = '', $threshold = 0 ) {
		if ( empty( $terms ) || '' === $content || empty( $threshold ) ) {
			return false;
		}

		$count = 0;

		foreach ( $terms as $term ) {
			if ( '' === trim( $term ) ) {
				continue;
			};

			$count += substr_count( strtolower( $content ), strtolower( $term ) );
		}

		return $count >= $threshold;
	}

	/**
	 * Loops through available tag and analyzes content for occurences
	 *
	 * @param int   $post_id ID of post to search.
	 * @param array $tags associative array of tags and terms.
	 * @param int   $threshold the minimum number of term occurences required.
	 * @since 0.1.0
	 * @return array|bool
	 */
	public function get_post_terms( $post_id = 0, $tags = array(), $threshold = 0 ) {
		if ( empty( $post_id ) || empty( $tags ) || empty( $threshold ) ) {
			return false;
		}

		$content = $this->get_post_contents( $post_id );

		if ( ! $content ) {
			return false;
		}

		$applied_tags = array();

		foreach ( $tags as $tag => $terms ) {
			if ( $this->content_has_terms( $terms, $content, $threshold ) ) {
				$applied_tags[] = $tag;
			}
		}

		return $applied_tags;
	}


}
