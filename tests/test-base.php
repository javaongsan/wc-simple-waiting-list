<?php
/**
 * WC_Simple_Waiting_List.
 *
 * @since   1.0.6
 * @package WC_Simple_Waiting_List
 */
class WC_Simple_Waiting_List_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  1.0.6
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'WC_Simple_Waiting_List') );
	}

	/**
	 * Test that our main helper function is an instance of our class.
	 *
	 * @since  1.0.6
	 */
	function test_get_instance() {
		$this->assertInstanceOf(  'WC_Simple_Waiting_List', wc_simple_waiting_list() );
	}

	/**
	 * Replace this with some actual testing code.
	 *
	 * @since  1.0.6
	 */
	function test_sample() {
		$this->assertTrue( true );
	}
}
