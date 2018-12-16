<?php
/**
 * Implements all the steps dealing with the site front-end and UI; e.g., navigating to the post single page or making
 * assertions on the front-end version of a post.
 *
 * @package Test\Gherkin
 */

namespace Test\Traits\Gherkin;

/**
 * Trait Frontend
 *
 * @package Test\Gherkin
 */
trait Frontend {

	/**
	 * @When I see the post on the frontend
	 */
	public function iSeeThePostOnTheFrontend() {
		// Use the `post_id` previously saved in the shared data array.
		$this->amOnPage( '/index.php?p=' . $this->data['postId'] );
	}

	/**
	 * @Then I should see the block shows an estimated reading time of :readingTimeEstimation
	 */
	public function iShouldSeeTheBlockShowsAnEstimatedReadingTimeOfMinutes( $readingTimeEstimation ) {
		$this->see( $readingTimeEstimation );
	}
}
