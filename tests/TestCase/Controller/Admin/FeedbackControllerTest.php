<?php

namespace Feedback\Test\TestCase\Controller\Admin;

use Cake\Core\Configure;
use Cake\Http\Session;
use Cake\TestSuite\IntegrationTestCase;
use Feedback\Store\FilesystemStore;

class FeedbackControllerTest extends IntegrationTestCase {

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		Configure::write('Feedback', [
			'configuration' => [
				FilesystemStore::NAME => [
					'location' => TMP,
				],
			],
		]);
	}

	/**
	 * @return void
	 */
	public function testRemove() {
		$this->disableErrorHandlerMiddleware();

		$file = time() . '-' . (new Session())->id() . '.feedback';
		$savepath = Configure::read('Feedback.configuration.Filesystem.location');
		$data = [
			'screenshot' => '123',
		];
		file_put_contents($savepath . $file, serialize($data));
		$this->assertFileExists($savepath . $file);

		$this->post(['prefix' => 'Admin', 'plugin' => 'Feedback', 'controller' => 'Feedback', 'action' => 'remove', $file]);

		$this->assertResponseCode(302);
		$this->assertRedirect(['prefix' => 'Admin', 'plugin' => 'Feedback', 'controller' => 'Feedback', 'action' => 'index']);

		$this->assertFileNotExists($savepath . $file);
	}

	/**
	 * @return void
	 */
	public function tearDown(): void {
		parent::tearDown();

		$savepath = Configure::read('Feedback.configuration.Filesystem.location');
		foreach (glob($savepath . '*-' . session_id() . '.feedback') as $feedbackfile) {
			unlink($feedbackfile);
		}
	}

}
