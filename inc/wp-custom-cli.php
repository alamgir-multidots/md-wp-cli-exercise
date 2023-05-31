<?php
/**
 * Wp Custom Cli.
 *
 * @package md-wp-cli-exercise
 * @since x.x.x
 */

namespace MdWpCliExercise\Inc;

use MdWpCliExercise\Inc\Traits\Get_Instance;
use MdWpCliExercise\Inc\Exercise;

/**
 * Wp Custom Cli Class
 *
 * @since x.x.x
 */
class Wp_Custom_Cli extends Exercise {

	use Get_Instance;

	/**
	 * Invoke
	 *
	 * @since x.x.x
	 *
	 * @param Array $args       Callback args.
	 * @param Array $assoc_args Argument args.
	 *
	 * @return void
	 */
    public function __invoke( $args, $assoc_args ) {
		if ( ! isset( $assoc_args['file'] ) ) {
			\WP_CLI::warning( '"File" argument is missing, add a argument --file=FILE_URL' );
			return;
		}

		$overwrite = false;

		if ( isset( $assoc_args['overwrite'] ) && 1 === intval( $assoc_args['overwrite'] ) ) {
			$overwrite = true;
		}
			
		if ( @fopen( $assoc_args['file'], 'r' ) ) {
			$file_to_read = fopen( $assoc_args['file'], 'r' );
			$progress     = \WP_CLI\Utils\make_progress_bar( 'Importing data', 100 );
			$count        = 0;
			
			while ( ( $data = fgetcsv( $file_to_read, 100, ',' ) ) !== FALSE ) {
				$post_id   = intval( $data[0] );
				$category  = sanitize_text_field( $data[1] );
				$tag       = sanitize_text_field( $data[2] );
				$image_url = sanitize_url( $data[3] );

				// Check post exists or not
				if ( get_post_status( $post_id ) ) {
					// Set post category
					$this->set_category( $post_id, $category, $overwrite );

					// Set post tag
					$this->set_tag( $post_id, $tag, $overwrite );

					// Set post thumbnail
					$this->set_thumbnail( $post_id, $image_url );

					$count++;
					$progress->tick();
				}
			}
			
			$progress->finish();
			fclose( $file_to_read );
			\WP_CLI::success( 'Category & tag set on ' . $count . ' posts' );
			return;
		} else {
			\WP_CLI::error( 'CSV File Not Found' );
			return;
		}
    }

	/**
	 * Set category
	 *
	 * @since x.x.x
	 * 
	 * @param Int    $post_id   Post Id.
	 * @param String $category  Category name.
	 * @param Bool   $overwrite Overwrite true or false.
	 *
	 * @return void
	 */
    public function set_category( $post_id, $category, $overwrite ) {
		if ( empty( $category ) ) {
			return;
		}

		// Set category to Post
		$assign_category = term_exists( $category, 'category' );

		if ( ! $overwrite && is_array( $assign_category ) && in_category( $assign_category['term_id'], $post_id ) ) {
			return;
		}
					
		// Insert new category if not exits
		if ( ! is_array( $assign_category ) ) {
			$assign_category = wp_insert_term( $category, 'category' );
		}

		$set_categories = [ $assign_category['term_id'] ];

		// Check set overwrite or not, if overwrite true then only new category will be assign
		if ( ! $overwrite ) {
			$set_categories   = wp_get_post_terms( $post_id, 'category' , [ 'fields' => 'ids' ] );
			$set_categories[] = $assign_category['term_id'];
		}

		// Set post categories
		wp_set_post_categories( $post_id, $set_categories );
	}

	/**
	 * Set tag
	 *
	 * @since x.x.x
	 * 
	 * @param Int    $post_id   Post Id.
	 * @param String $tag       Tag name.
	 * @param Bool   $overwrite Overwrite true or false.
	 *
	 * @return void
	 */
    public function set_tag( $post_id, $tag, $overwrite ) {
		if ( empty( $tag ) ) {
			return;
		}

		if ( ! $overwrite && has_term( $tag, 'post_tag', $post_id ) ) {
			return;
		}

		$set_tags = $tag;

		// Check set overwrite or not, if overwrite true then only new tag will be assign
		if ( ! $overwrite ) {
			$set_tags   = wp_get_post_terms( $post_id, 'post_tag' , [ 'fields' => 'names' ] );
			$set_tags[] = $tag;
		}

		// Set tag to Post
		wp_set_post_terms( $post_id, $set_tags );
	}

	/**
	 * Set thumbnail
	 *
	 * @since x.x.x
	 * 
	 * @param Int    $post_id  Post Id.
	 * @param String $image_url Image url.
	 * 
	 * @return void
	 */
    public function set_thumbnail( $post_id, $image_url ) {
		if ( has_post_thumbnail( $post_id ) || empty( $image_url ) ) {
			return;
		}

		// Build up array like PHP file upload
		$file             = [];
		$file['name']     = basename( $image_url );
		$file['tmp_name'] = download_url( $image_url );

		if ( is_wp_error( $file['tmp_name'] ) ) {
			@unlink( $file['tmp_name'] );
			return;
		}

		$attach_id = media_handle_sideload( $file, $post_id );

		// Create the thumbnails
		$attach_data = wp_generate_attachment_metadata( $attach_id,  get_attached_file( $attach_id ) );

		wp_update_attachment_metadata( $attach_id,  $attach_data );

		// And finally assign featured image to post
		set_post_thumbnail( $post_id, $attach_id );
	}
}
