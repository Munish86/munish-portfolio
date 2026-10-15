<?php
/**
 * Comments on blog posts (only shown when comments are open or exist).
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;
if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments">
	<?php if ( have_comments() ) : ?>
		<h2><?php echo esc_html( sprintf( /* translators: %d: count */ _n( '%d comment', '%d comments', get_comments_number(), 'folio' ), get_comments_number() ) ); ?></h2>
		<ol class="comment-list"><?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true, 'avatar_size' => 40 ) ); ?></ol>
		<?php the_comments_pagination(); ?>
	<?php endif; ?>
	<?php comment_form( array( 'class_submit' => 'btn btn--primary' ) ); ?>
</section>
