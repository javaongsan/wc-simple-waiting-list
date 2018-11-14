<?php
/**
 * WC Simple Waiting List Admin Tests.
 *
 * @since   1.0.6
 * @package WC_Simple_Waiting_List
 */
class WCSWL_Admin_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  1.0.6
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'WCSWL_Admin' ) );
	}

	/**
	 * Test that we can access our class through our helper function.
	 *
	 * @since  1.0.6
	 */
	function test_class_access() {
		$this->assertInstanceOf( 'WCSWL_Admin', wc_simple_waiting_list()->admin );
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
