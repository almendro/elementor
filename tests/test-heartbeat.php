<?php

class Elementor_Test_Heartbeat extends WP_UnitTestCase {

	protected $user_own_post;
	protected $user_editor;

	function setUp() {
		parent::setUp();

		// Create new instance again
		new \Elementor\Heartbeat;
	}

	public function test_postLock() {
		$this->user_own_post = $this->factory->user->create( [ 'role' => 'administrator' ] );
		$this->user_editor = $this->factory->user->create( [ 'role' => 'administrator' ] );

		wp_set_current_user( $this->user_own_post );

		$post = $this->factory->post->create_and_get();

		$data = [
			'elementor_post_lock' => [
				'post_ID' => $post->ID,
			],
		];

		$response = apply_filters( 'heartbeat_received', [], $data, '' );

		// Switch to other user
		wp_set_current_user( $this->user_editor );

		$this->assertEquals( $this->user_own_post, wp_check_post_lock( $post->ID ) );

		$response = apply_filters( 'heartbeat_received', [], $data, '' );

		$this->assertArrayHasKey( 'locked_user', $response );
	}
}
