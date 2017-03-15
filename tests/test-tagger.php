<?php
/**
 * PHPUnit Test For Tagger Class
 */

/**
 * Tagger Test
 */
class Tagger_test extends WP_UnitTestCase {

	/**
	 * Filepath of CSV to be used for testing
	 *
	 * @var string
	 */
	public $csv;

	/**
	 * Tagger_test constructor.
	 */
	public function __construct() {
		$this->csv = TAGGER_PATH . 'tests/test.csv';
	}

	/**
	 * Test for tagger()->parse_csv() function
	 */
	function test_parse_csv() {
		// non-existent file.
		$this->assertFalse( tagger()->parse_csv( 'non-existent-file.csv' ) );

		// no file provided.
		$this->assertFalse( tagger()->parse_csv() );

		// should return array.
		$this->assertInternalType( 'array', tagger()->parse_csv( $this->csv ) );
	}

	/**
	 * Test for tagger()->get_post_contents() function
	 */
	function test_get_post_contents() {
		// create post.
		$post_id = $this->factory->post->create( array(
			'post_title' => 'this is the post title',
			'post_content' => 'this is the post content',
		));

		// Non-existent post.
		$this->assertFalse( tagger()->get_post_contents( 544 ) );

		// Existing post.
		$this->assertEquals( 'this is the post title this is the post content', tagger()->get_post_contents( $post_id ) );


	}

	/**
	 * Test for tagger()->content_has_terms() function
	 */
	function test_content_has_terms() {
		// set up tags.
		$terms = array( 'dog', 'cat', 'parrot' );

		// set up content.
		$content = 'dog dog cat parrot dog dog apple apple banana tasty banana orange orange';

		// confirm correct tags are returned or not.
		$this->assertTrue( tagger()->content_has_terms( $terms, $content, 5 ) );
		$this->assertFalse( tagger()->content_has_terms( $terms, $content, 7 ) );

	}

	/**
	 * Test for tagger()->get_post_terms() function
	 */
	function test_get_post_terms() {
		// set up tags.
		$tags = array(
			'animal' => array( 'dog', 'cat', 'parrot' ),
			'fruit' => array( 'apple', 'tasty banana', 'orange' ),
		);

		// set up content.
		$post_id = $this->factory->post->create(array(
			'post_title' => 'This is the title',
			'post_content' => 'dog dog cat parrot dog dog apple apple banana tasty banana orange orange',
		));

		// confirm correct tags are returned or not.
		$this->assertEquals( array( 'animal', 'fruit' ), tagger()->get_post_terms( $post_id, $tags, 5 ) );
		$this->assertEquals( array( 'animal' ), tagger()->get_post_terms( $post_id, $tags, 6 ) );
		$this->assertNotEquals( array( 'animal', 'fruit' ), tagger()->get_post_terms( $post_id, $tags, 6 ) );
	}

}