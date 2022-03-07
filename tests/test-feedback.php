<?php
/**
 * WC Simple Waiting List Feedback Tests.
 *
 * @since   1.0.11
 * @package WC_Simple_Waiting_List
 */
class WCSWL_Feedback_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  1.0.11
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'WCSWL_Feedback' ) );
	}

	/**
	 * Test that we can access our class through our helper function.
	 *
	 * @since  1.0.11
	 */
	function test_class_access() {
		$this->assertInstanceOf( 'WCSWL_Feedback', wc_simple_waiting_list()->feedback );
	}

	/**
	 * Replace this with some actual testing code.
	 *
	 * @since  1.0.11
	 */
	function test_sample() {
		$this->assertTrue( true );
	}
}
