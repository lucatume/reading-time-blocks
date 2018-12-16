Feature: Base estimated remaining reading time block
  In order to show readers the estimated remaining reading time
  As a post editor
  I need to be able to drop the estimated reading time block anywhere in the page

  Scenario: when inserted at the start of the post the block shows the est. remaining reading time for the whole post
	Given a post of "400" words
	And the "tad/reading-time" block is placed at the start of the post
	When I see the post on the frontend
	Then I should see the block shows an estimated reading time of "about 2 minutes"
