<?php
/**
 * WC Simple Waiting List Ajax Tests.
 *
 * @since   1.0.6
 * @package WC_Simple_Waiting_List
 */
class WCSWL_Ajax_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  1.0.6
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'WCSWL_Ajax' ) );
	}

	/**
	 * Test that we can access our class through our helper function.
	 *
	 * @since  1.0.6
	 */
	function test_class_access() {
		$this->assertInstanceOf( 'WCSWL_Ajax', wc_simple_waiting_list()->ajax );
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
