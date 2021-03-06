<?php

// test the output of post template tags etc

/**
 * @group post
 * @group formatting
 */
class WP_Test_Post_Output extends WP_UnitTestCase {

	function setUp() {
		parent::setUp();
		add_shortcode( 'dumptag', array( $this, '_shortcode_dumptag' ) );
		add_shortcode( 'paragraph', array( $this, '_shortcode_paragraph' ) );
	}

	function tearDown() {
		global $shortcode_tags;
		unset( $shortcode_tags['dumptag'], $shortcode_tags['paragraph'] );
		parent::tearDown();
	}

	function _shortcode_dumptag( $atts ) {
		$out = '';
		foreach ($atts as $k=>$v)
			$out .= "$k = $v\n";
		return $out;
	}

	function _shortcode_paragraph( $atts, $content ) {
		extract(shortcode_atts(array(
			'class' => 'graf',
		), $atts));
		return "<p class='$class'>$content</p>\n";
	}

	function test_the_content() {
		$post_content = <<<EOF
<i>This is the excerpt.</i>
<!--more-->
This is the <b>body</b>.
EOF;

		$post_id = $this->factory->post->create( compact( 'post_content' ) );

		$expected = <<<EOF
<p><i>This is the excerpt.</i><br />
<span id="more-{$post_id}"></span><br />
This is the <b>body</b>.</p>
EOF;

		$this->go_to( get_permalink( $post_id ) );
		$this->assertTrue( is_single() );
		$this->assertTrue( have_posts() );
		$this->assertNull( the_post() );

		$this->assertEquals( strip_ws( $expected ), strip_ws( get_echo( 'the_content' ) ) );
	}

	function test_the_content_shortcode() {
		$post_content = <<<EOF
[dumptag foo="bar" baz="123"]

[dumptag foo=123 baz=bar]

[dumptag http://example.com]

EOF;

		$expected =<<<EOF
foo = bar
baz = 123
foo = 123
baz = bar
0 = http://example.com

EOF;

		$post_id = $this->factory->post->create( compact( 'post_content' ) );
		$this->go_to( get_permalink( $post_id ) );
		$this->assertTrue( is_single() );
		$this->assertTrue( have_posts() );
		$this->assertNull( the_post() );

		$this->assertEquals( strip_ws( $expected ), strip_ws( get_echo( 'the_content' ) ) );
	}

	function test_the_content_shortcode_paragraph() {
		$post_content = <<<EOF
Graf by itself:

[paragraph]my graf[/paragraph]

  [paragraph foo="bar"]another graf with whitespace[/paragraph]

An [paragraph]inline graf[/paragraph], this doesn't make much sense.

A graf with a single EOL first:
[paragraph]blah[/paragraph]

EOF;

		$expected = <<<EOF
<p>Graf by itself:</p>
<p class='graf'>my graf</p>

  <p class='graf'>another graf with whitespace</p>

<p>An <p class='graf'>inline graf</p>
, this doesn&#8217;t make much sense.</p>
<p>A graf with a single EOL first:<br />
<p class='graf'>blah</p>
</p>

EOF;

		$post_id = $this->factory->post->create( compact( 'post_content' ) );
		$this->go_to( get_permalink( $post_id ) );
		$this->assertTrue( is_single() );
		$this->assertTrue( have_posts() );
		$this->assertNull( the_post() );

		$this->assertEquals( strip_ws( $expected ), strip_ws( get_echo( 'the_content' ) ) );
	}
}

/**
 * @group media
 * @group gallery
 * @ticket UT30
 */
class WPTestGalleryPost extends WP_UnitTestCase { // _WPDataset1
	function setUp() {
		parent::setUp();
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure('/%year%/%monthnum%/%day%/%postname%/');
		$wp_rewrite->flush_rules();
	}

	function test_the_content() {
		// permalink page
		$link = '/2008/04/01/simple-gallery-test/';
		$this->go_to('/2008/04/01/simple-gallery-test/');
		the_post();
		// filtered output
		$out = get_echo('the_content');
		$this->assertNotEmpty($out, "Could not get the_content for $link.");

		$expected = <<<EOF
<p>There are ten images attached to this post.  Here&#8217;s a gallery:</p>

		<style type='text/css'>
			.gallery {
				margin: auto;
			}
			.gallery-item {
				float: left;
				margin-top: 10px;
				text-align: center;
				width: 33%;			}
			.gallery img {
				border: 2px solid #cfcfcf;
			}
			.gallery-caption {
				margin-left: 0;
			}
		</style>
		<!-- see gallery_shortcode() in wp-includes/media.php -->
		<div class='gallery'><dl class='gallery-item'>
			<dt class='gallery-icon'>
				<a href='http://example.com/2008/04/01/simple-gallery-test/dsc20040724_152504_53/' title='dsc20040724_152504_53'><img src="http://example.com/wp-content/uploads/2008/04/dsc20040724_152504_537.jpg" class="attachment-thumbnail" alt="" /></a>
			</dt></dl><dl class='gallery-item'>
			<dt class='gallery-icon'>
				<a href='http://example.com/2008/04/01/simple-gallery-test/canola/' title='canola'><img src="http://example.com/wp-content/uploads/2008/04/canola3.jpg" class="attachment-thumbnail" alt="" /></a>
			</dt></dl><dl class='gallery-item'>
			<dt class='gallery-icon'>
				<a href='http://example.com/2008/04/01/simple-gallery-test/dsc20050315_145007_13/' title='dsc20050315_145007_13'><img src="http://example.com/wp-content/uploads/2008/04/dsc20050315_145007_134.jpg" class="attachment-thumbnail" alt="" /></a>
			</dt></dl><br style="clear: both" /><dl class='gallery-item'>
			<dt class='gallery-icon'>
				<a href='http://example.com/2008/04/01/simple-gallery-test/dsc20050604_133440_34/' title='dsc20050604_133440_34'><img src="http://example.com/wp-content/uploads/2008/04/dsc20050604_133440_343.jpg" class="attachment-thumbnail" alt="" /></a>
			</dt></dl><dl class='gallery-item'>
			<dt class='gallery-icon'>
				<a href='http://example.com/2008/04/01/simple-gallery-test/dsc20050831_165238_33/' title='dsc20050831_165238_33'><img src="http://example.com/wp-content/uploads/2008/04/dsc20050831_165238_333.jpg" class="attachment-thumbnail" alt="" /></a>
			</dt></dl><dl class='gallery-item'>
			<dt class='gallery-icon'>
				<a href='http://example.com/2008/04/01/simple-gallery-test/dsc20050901_105100_21/' title='dsc20050901_105100_21'><img src="http://example.com/wp-content/uploads/2008/04/dsc20050901_105100_213.jpg" class="attachment-thumbnail" alt="" /></a>
			</dt></dl><br style="clear: both" /><dl class='gallery-item'>
			<dt class='gallery-icon'>
				<a href='http://example.com/2008/04/01/simple-gallery-test/dsc20050813_115856_5/' title='dsc20050813_115856_5'><img src="http://example.com/wp-content/uploads/2008/04/dsc20050813_115856_54.jpg" class="attachment-thumbnail" alt="" /></a>
			</dt></dl><dl class='gallery-item'>
			<dt class='gallery-icon'>
				<a href='http://example.com/2008/04/01/simple-gallery-test/dsc20050720_123726_27/' title='dsc20050720_123726_27'><img src="http://example.com/wp-content/uploads/2008/04/dsc20050720_123726_274.jpg" class="attachment-thumbnail" alt="" /></a>
			</dt></dl><dl class='gallery-item'>
			<dt class='gallery-icon'>
				<a href='http://example.com/2008/04/01/simple-gallery-test/dsc20050727_091048_22/' title='Title: Seedlings'><img src="http://example.com/wp-content/uploads/2008/04/dsc20050727_091048_224.jpg" class="attachment-thumbnail" alt="" /></a>
			</dt></dl><br style="clear: both" /><dl class='gallery-item'>
			<dt class='gallery-icon'>
				<a href='http://example.com/2008/04/01/simple-gallery-test/dsc20050726_083116_18/' title='dsc20050726_083116_18'><img src="http://example.com/wp-content/uploads/2008/04/dsc20050726_083116_184.jpg" class="attachment-thumbnail" alt="" /></a>
			</dt></dl>
			<br style='clear: both;' />
		</div>

<p>It&#8217;s the simplest form of the gallery tag.  All images are from the public domain site burningwell.org.</p>
<p>The images have various combinations of titles, captions and descriptions.</p>
EOF;
		$this->assertEquals(strip_ws($expected), strip_ws($out));
	}

	function test_gallery_attributes() {
		// make sure the gallery shortcode attributes are parsed correctly

		$id = 575;
		$post = get_post($id);
		$this->assertNotNull($post, "get_post($id) could not find the post.");
		$post->post_content = '[gallery columns="1" size="medium"]';
		wp_update_post($post);

		// permalink page
		$this->go_to('/2008/04/01/simple-gallery-test/');
		the_post();
		// filtered output
		$out = get_echo('the_content');

		$expected = <<<EOF
		<style type='text/css'>
			.gallery {
				margin: auto;
			}
			.gallery-item {
				float: left;
				margin-top: 10px;
				text-align: center;
				width: 100%;			}
			.gallery img {
				border: 2px solid #cfcfcf;
			}
			.gallery-caption {
				margin-left: 0;
			}
		</style>
		<!-- see gallery_shortcode() in wp-includes/media.php -->
		<div class='gallery'><dl class='gallery-item'>
			<dt class='gallery-icon'>
				<a href='http://example.com/?attachment_id=565' title='dsc20040724_152504_53'><img src="http://example.com/wp-content/uploads/2008/04/dsc20040724_152504_537.jpg" class="attachment-medium" alt="" /></a>
			</dt></dl><br style="clear: both" /><dl class='gallery-item'>
			<dt class='gallery-icon'>
				<a href='http://example.com/?attachment_id=566' title='canola'><img src="http://example.com/wp-content/uploads/2008/04/canola3.jpg" class="attachment-medium" alt="" /></a>
			</dt></dl><br style="clear: both" /><dl class='gallery-item'>
			<dt class='gallery-icon'>
				<a href='http://example.com/?attachment_id=567' title='dsc20050315_145007_13'><img src="http://example.com/wp-content/uploads/2008/04/dsc20050315_145007_134.jpg" class="attachment-medium" alt="" /></a>
			</dt></dl><br style="clear: both" /><dl class='gallery-item'>
			<dt class='gallery-icon'>
				<a href='http://example.com/?attachment_id=568' title='dsc20050604_133440_34'><img src="http://example.com/wp-content/uploads/2008/04/dsc20050604_133440_343.jpg" class="attachment-medium" alt="" /></a>
			</dt></dl><br style="clear: both" /><dl class='gallery-item'>
			<dt class='gallery-icon'>
				<a href='http://example.com/?attachment_id=569' title='dsc20050831_165238_33'><img src="http://example.com/wp-content/uploads/2008/04/dsc20050831_165238_333.jpg" class="attachment-medium" alt="" /></a>
			</dt></dl><br style="clear: both" /><dl class='gallery-item'>
			<dt class='gallery-icon'>
				<a href='http://example.com/?attachment_id=570' title='dsc20050901_105100_21'><img src="http://example.com/wp-content/uploads/2008/04/dsc20050901_105100_213.jpg" class="attachment-medium" alt="" /></a>
			</dt></dl><br style="clear: both" /><dl class='gallery-item'>
			<dt class='gallery-icon'>
				<a href='http://example.com/?attachment_id=571' title='dsc20050813_115856_5'><img src="http://example.com/wp-content/uploads/2008/04/dsc20050813_115856_54.jpg" class="attachment-medium" alt="" /></a>
			</dt></dl><br style="clear: both" /><dl class='gallery-item'>
			<dt class='gallery-icon'>
				<a href='http://example.com/?attachment_id=572' title='dsc20050720_123726_27'><img src="http://example.com/wp-content/uploads/2008/04/dsc20050720_123726_274.jpg" class="attachment-medium" alt="" /></a>
			</dt></dl><br style="clear: both" /><dl class='gallery-item'>
			<dt class='gallery-icon'>
				<a href='http://example.com/?attachment_id=573' title='Title: Seedlings'><img src="http://example.com/wp-content/uploads/2008/04/dsc20050727_091048_224.jpg" class="attachment-medium" alt="" /></a>
			</dt></dl><br style="clear: both" /><dl class='gallery-item'>
			<dt class='gallery-icon'>
				<a href='http://example.com/?attachment_id=574' title='dsc20050726_083116_18'><img src="http://example.com/wp-content/uploads/2008/04/dsc20050726_083116_184.jpg" class="attachment-medium" alt="" /></a>
			</dt></dl><br style="clear: both" />
			<br style='clear: both;' />
		</div>

EOF;
		$this->assertEquals(strip_ws($expected), strip_ws($out));
	}

}

/**
 * @group post
 * @group formatting
 */
class WPTestAttributeFiltering extends WP_UnitTestCase {
	function setUp() {
		parent::setUp();
		kses_init_filters();
	}

	function tearDown() {
		kses_remove_filters();
		parent::tearDown();
	}

	function test_the_content_attribute_filtering() {
		// http://bpr3.org/?p=87
		// the title attribute should make it through unfiltered
		$post_content = <<<EOF
<span class="Z3988" title="ctx_ver=Z39.88-2004&rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Ajournal&rft.aulast=Mariat&rft.aufirst=Denis&rft. au=Denis+Mariat&rft.au=Sead+Taourit&rft.au=G%C3%A9rard+Gu%C3%A9rin& rft.title=Genetics+Selection+Evolution&rft.atitle=&rft.date=2003&rft. volume=35&rft.issue=1&rft.spage=119&rft.epage=133&rft.genre=article& rft.id=info:DOI/10.1051%2Fgse%3A2002039"></span>Mariat, D., Taourit, S., GuÃ©rin, G. (2003). . <span style="font-style: italic;">Genetics Selection Evolution, 35</span>(1), 119-133. DOI: <a rev="review" href= "http://dx.doi.org/10.1051/gse:2002039">10.1051/gse:2002039</a>
EOF;

		$expected = <<<EOF
<p><span class="Z3988" title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Ajournal&amp;rft.aulast=Mariat&amp;rft.aufirst=Denis&amp;rft. au=Denis+Mariat&amp;rft.au=Sead+Taourit&amp;rft.au=G%C3%A9rard+Gu%C3%A9rin&amp; rft.title=Genetics+Selection+Evolution&amp;rft.atitle=&amp;rft.date=2003&amp;rft. volume=35&amp;rft.issue=1&amp;rft.spage=119&amp;rft.epage=133&amp;rft.genre=article&amp; rft.id=info:DOI/10.1051%2Fgse%3A2002039"></span>Mariat, D., Taourit, S., GuÃ©rin, G. (2003). . <span style="font-style: italic">Genetics Selection Evolution, 35</span>(1), 119-133. DOI: <a rev="review" href="http://dx.doi.org/10.1051/gse:2002039">10.1051/gse:2002039</a></p>
EOF;

		$post_id = $this->factory->post->create( compact( 'post_content' ) );
		$this->go_to( get_permalink( $post_id ) );
		$this->assertTrue( is_single() );
		$this->assertTrue( have_posts() );
		$this->assertNull( the_post() );

		$this->assertEquals( strip_ws( $expected ), strip_ws( get_echo( 'the_content' ) ) );
	}

	function test_the_content_attribute_value_with_colon() {
		// http://bpr3.org/?p=87
		// the title attribute should make it through unfiltered
		$post_content = <<<EOF
<span title="My friends: Alice, Bob and Carol">foo</span>
EOF;

		$expected = <<<EOF
<p><span title="My friends: Alice, Bob and Carol">foo</span></p>
EOF;

		$post_id = $this->factory->post->create( compact( 'post_content' ) );
		$this->go_to( get_permalink( $post_id ) );
		$this->assertTrue( is_single() );
		$this->assertTrue( have_posts() );
		$this->assertNull( the_post() );

		$this->assertEquals( strip_ws( $expected ), strip_ws( get_echo( 'the_content' ) ) );
	}
}
