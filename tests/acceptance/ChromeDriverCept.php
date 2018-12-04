<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'use Chrome for acceptance tests' );

$I->havePostInDatabase( [ 'post_title' => 'Test post', 'post_status' => 'publish' ] );

$I->amOnPage( '/' );

$I->see( 'Test post' );
