<?php

class MemcachedUnitTestsGet extends MemcachedUnitTests {
	public function test_get_value() {
		$key = microtime();
		$value = 'brodeur';

		// Add string to memcached
		$this->assertTrue( $this->object_cache->add( $key, $value ) );

		// Verify correct value is returned
		$this->assertSame( $value, $this->object_cache->get( $key ) );
	}

	public function test_get_value_twice() {
		$key = microtime();
		$value = 'brodeur';

		// Add string to memcached
		$this->assertTrue( $this->object_cache->add( $key, $value ) );

		// Verify correct value is returned
		$this->assertSame( $value, $this->object_cache->get( $key ) );

		// Verify correct value is returned when pulled from the internal cache
		$this->assertSame( $value, $this->object_cache->get( $key ) );
	}

	public function test_get_value_with_group() {
		$key = microtime();
		$value = 'brodeur';

		$group = 'devils';

		// Add string to memcached
		$this->assertTrue( $this->object_cache->add( $key, $value, $group ) );

		// Verify correct value is returned
		$this->assertSame( $value, $this->object_cache->get( $key, $group ) );
	}

	public function test_get_value_with_no_mc_group() {
		$key = microtime();
		$value = 'brodeur';

		$group = 'comment';

		// Add string to memcached
		$this->assertTrue( $this->object_cache->add( $key, $value, $group ) );

		// Verify correct value is returned
		$this->assertSame( $value, $this->object_cache->get( $key, $group ) );
	}

	public function test_get_value_with_global_group() {
		$key = microtime();
		$value = 'brodeur';

		$group = 'usermeta';

		// Add string to memcached
		$this->assertTrue( $this->object_cache->add( $key, $value, $group ) );

		// Verify correct value is returned
		$this->assertSame( $value, $this->object_cache->get( $key, $group ) );
	}

	public function test_get_value_with_found_indicator() {
		$key = microtime();
		$value = 'karlson';
		$group = 'senators';
		$found = false;

		// Add string to memcached
		$this->assertTrue( $this->object_cache->add( $key, $value, $group ) );

		// Verify correct value is returned
		$this->assertSame( $value, $this->object_cache->get( $key, $group, false, $found ) );

		// Verify that found variable is set to true because the item was found
		$this->assertTrue( $found );
	}

	public function test_get_value_with_found_indicator_when_value_is_not_found() {
		$key = microtime();
		$value = 'neil';
		$group = 'senators';
		$found = false;

		// Add string to memcached
		$this->assertTrue( $this->object_cache->add( $key, $value, $group ) );

		// Verify that the value is deleted
		$this->assertTrue( $this->object_cache->delete( $key, $group ) );

		// Verify that false is returned
		$this->assertFalse( $this->object_cache->get( $key, $group, false, $found ) );

		// Verify that found variable is set to true because the item was found
		$this->assertFalse( $found );
	}

	public function test_get_value_with_found_indicator_when_retrieved_from_memcached() {
		$key = microtime();
		$value = 'holtby';
		$group = 'capitals';
		$found = false;

		// Add string to memcached
		$this->assertTrue( $this->object_cache->add( $key, $value, $group ) );

		// Remove from internal cache and verify
		unset( $this->object_cache->cache[ $this->object_cache->buildKey( $key, $group ) ] );
		$this->assertFalse( $this->object_cache->get_from_runtime_cache( $key, $group ) );

		// Verify correct value is returned
		$this->assertSame( $value, $this->object_cache->get( $key, $group, false, $found ) );

		// Verify that found variable is set to true because the item was found
		$this->assertTrue( $found );
	}

	public function test_get_value_with_found_indicator_when_retrieved_from_memcached_and_value_is_not_found() {
		$key = microtime();
		$value = 'backstrom';
		$group = 'capitals';
		$found = false;

		// Add string to memcached
		$this->assertTrue( $this->object_cache->add( $key, $value, $group ) );

		// Remove from internal cache and verify
		unset( $this->object_cache->cache[ $this->object_cache->buildKey( $key, $group ) ] );
		$this->assertFalse( $this->object_cache->get_from_runtime_cache( $key, $group ) );

		// Verify that the value is deleted
		$this->assertTrue( $this->object_cache->delete( $key, $group ) );

		// Verify that false is returned
		$this->assertFalse( $this->object_cache->get( $key, $group, false, $found ) );

		// Verify that found variable is set to true because the item was found
		$this->assertFalse( $found );
	}

	public function test_get_value_with_callback_with_true_response() {
		$key = microtime();
		$group = 'nj-devils';

		$value = 'brodeur';

		// Verify that callback sets value correctly
		$this->assertSame( $value, $this->object_cache->get( $key, $group, false, $found, '', true, 'memcached_get_callback_true' ) );

		// Doublecheck it
		$this->assertSame( $value, $this->object_cache->get( $key, $group ) );
	}

	public function test_get_value_with_callback_with_false_response() {
		$key = microtime();
		$group = 'nhl-nj-devils';

		$value = 'brodeur';

		// Verify that callback sets value correctly
		$this->assertFalse( $this->object_cache->get( $key, $group, false, $found, '', false, 'memcached_get_callback_false' ) );

		// Doublecheck it
		$this->assertFalse( $this->object_cache->get( $key, $group ) );
	}

	public function test_get_value_with_callback_with_true_response_and_using_class_method() {
		$key = microtime();
		$group = 'nhl-nj-devils-team';

		$value = 'brodeur';

		// Verify that callback sets value correctly
		$this->assertSame( $value, $this->object_cache->get( $key, $group, false, $found, '', false, array( &$this, 'memcached_get_callback_true_class_method' ) ) );

		// Doublecheck it
		$this->assertSame( $value, $this->object_cache->get( $key, $group ) );
	}

	public function memcached_get_callback_true_class_method( $m, $key, &$value ) {
		$value = 'brodeur';
		return true;
	}

	public function test_get_value_with_callback_with_false_response_and_using_class_method() {
		$key = microtime();
		$group = 'nhl-nj-devils-team-runner-up';

		// Verify that callback sets value correctly
		$this->assertFalse( $this->object_cache->get( $key, $group, false, $found, '', false, array( &$this, 'memcached_get_callback_false_class_method' ) ) );

		// Doublecheck it
		$this->assertFalse( $this->object_cache->get( $key, $group ) );
	}

	public function memcached_get_callback_false_class_method( $m, $key, &$value ) {
		$value = 'brodeur';
		return false;
	}

	public function test_get_value_with_callback_ignores_callback_for_no_mc_group() {
		$key = microtime();
		$group = 'comment';

		$value = 'brodeur';

		// Verify that if completely bypassed
		$this->assertFalse( $this->object_cache->get( $key, $group, false, $found, '', false, array( &$this, 'memcached_get_callback_true_no_mc_group' ) ) );

		// Doublecheck that no value has been set
		$this->assertFalse( $this->object_cache->get( $key, $group ) );

		// Verify that a normal set and get works when a callback is sent
		$this->assertTrue( $this->object_cache->set( $key, $value, $group ) );
		$this->assertSame( $value, $this->object_cache->get( $key, $group, false, $found, '', false, array( &$this, 'memcached_get_callback_true_no_mc_group' ) ) );
	}

	public function memcached_get_callback_true_no_mc_group( $m, $key, &$value ) {
		$value = 'parise';
		return true;
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function test_get_expect_exception_when_cache_cb_is_not_callable() {
		$key = microtime();

		$value = 'brodeur';
		$group = 'devils';

		// Set value via the callback when key is not set
		$this->assertSame( $value, $this->object_cache->get( $key, $group, false, $found, '', false, array( &$this, 'fake_function' ) ) );
	}
}

function memcached_get_callback_true( $m, $key, &$value ) {
	$value = 'brodeur';
	return true;
}

function memcached_get_callback_false( $m, $key, &$value ) {
	$value = 'brodeur';
	return false;
}
