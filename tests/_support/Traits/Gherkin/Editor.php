<?php
/**
 * Implements all the steps dealing with the backend editor UI; e.g., adding and removing blocks, entering content and
 * so on.
 *
 * @package Test\Gherkin
 */

namespace Test\Traits\Gherkin;

/**
 * Trait Editor
 *
 * @package Test\Gherkin
 */
trait Editor {

	/**
	 * Whether the scenario already logged in or not.
	 *
	 * @var bool
	 */
	protected $logged = false;

	/**
	 * Adds an editor block of the specified type to the current editor.
	 *
	 * @param      string $type The block name in the format `namespace/name`.
	 * @param array $props An array of properties to initialize the block with.
	 * @param null  $position The position, in the editor, the block should be inserted at.
	 *                        Default to append as last.
	 *
	 * @return string The block `clientId`.
	 */
	protected function addBlock( $type, $props = [], $position = null ) {
		$positionString = null !== $position ? ' at position ' . (int) $position : '';
		$jsonProps      = json_encode( $props, JSON_PRETTY_PRINT );
		codecept_debug( "Editor: adding block of type '{$type}'{$positionString} with props:\n" . $jsonProps );
		$position = null === $position ? 'null' : (int) $position;

		$js       = "return (function(){
			var block = wp.blocks.createBlock('{$type}', $jsonProps);
			wp.data.dispatch('core/editor').insertBlock(block, {$position});
			return block.clientId;
		})();";
		$clientId = $this->executeJS( $js );

		codecept_debug( "Editor: added block of type '{$type}'{$positionString}, block clientId is '{$clientId}'." );

		return $clientId;
	}

	/**
	 * Triggers the save post action on the editor.
	 *
	 * After triggering the save action the method will wait for a set number of seconds before
	 * returning.
	 *
	 * @param int $wait How much to wait for the post to save after triggering the save actions.
	 *                  The saving will happen asynchronously.
	 *
	 * @return int The saved post ID.
	 */
	protected function savePost( $wait = 2 ) {
		codecept_debug( 'Editor: saving the post.' );
		$postId = $this->executeJS( 'return (function(){
			wp.data.dispatch("core/editor").savePost();
			return wp.data.select("core/editor").getCurrentPostId();
		})(); ' );
		codecept_debug( "Editor: saved the post, post ID is {$postId}" );

		$this->wait( $wait );

		return $postId;
	}

	/**
	 * Clicks the tooltip close ("X") icon to disable tooltips for the current user.
	 */
	public function disableEditorTips() {
		codecept_debug( 'Editor: disabling tips.' );
		$this->executeJS( 'document.querySelector("#editor button.nux-dot-tip__disable").click()' );
	}

	/**
	 * Edits the post adding properties to it.
	 *
	 * The post is not saved after it, use the `savePost` method to save the changes.
	 *
	 * @param array $props An associative array of properties to set for the post.
	 *
	 * @return int The edited post ID.
	 */
	protected function editPost( array $props = [] ) {
		$jsonProps = json_encode( $props, JSON_PRETTY_PRINT );
		codecept_debug( 'Editor: editing the post with properties: ' . $jsonProps );
		$postId = $this->executeJS( 'return (function(){
			wp.data.dispatch("core/editor").editPost(' . $jsonProps . ');
			return wp.data.select("core/editor").getCurrentPostId();
		})(); ' );
		codecept_debug( "Editor: edited the post, post ID is {$postId}" );

		return $postId;
	}

	/**
	 * @Given a post of :wordCount words
	 *
	 * @param int $wordCount The number of words in the post content.
	 */
	public function aPostOfWords( $wordCount ) {
		if ( ! $this->logged ) {
			$this->loginAsAdmin();
		}

		$this->amOnAdminPage( 'post-new.php' );
		$this->disableEditorTips();
		$this->fillField( '#post-title-0', 'Test post' );

		// Create the test content: the word "test" repeated n times.
		$content = implode( ' ', array_fill( 0, (int) $wordCount, 'test' ) );
		$this->addBlock( 'core/paragraph', [ 'content' => $content ] );
		// Save the post to commit the changes.
		$this->editPost( [ 'status' => 'publish' ] );
		// Save the post ID in the shared data array.
		$this->data['postId'] = $this->savePost();
	}


	/**
	 * @Given the :type block is placed at the start of the post
	 *
	 * @param string $type The block type, in the format `namespace/type`.
	 */
	public function theBlockIsPlacedAtTheStartOfThePost( $type ) {
		$this->addBlock( $type, [], 0 );
		// Save the post to commit the changes.
		$this->savePost();
	}
}
