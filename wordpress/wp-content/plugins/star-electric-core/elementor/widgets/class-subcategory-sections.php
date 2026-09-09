<?php
/**
 * Subcategory Sections.
 *
 * One band per department, listing that department's subcategories.
 *
 * Every department, every subcategory, every product count and every picture
 * comes from the catalogue, so this panel only sets the wording of the link at
 * the top right of each band. A new department appears here by itself.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Subcategory sections widget.
 */
class Star_Electric_Widget_Subcategory_Sections extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-subcategory-sections';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Subcategory Sections', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-menu-card';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_section',
			array( 'label' => __( 'Sections', 'star-electric' ) )
		);

		$this->note(
			__( 'One band per department, drawn from the catalogue. A department with no subcategories is left out, and the bands alternate shade by themselves.', 'star-electric' )
		);

		$this->text(
			'link_label',
			__( 'Link at the top of each band', 'star-electric' ),
			__( 'Browse department', 'star-electric' )
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		Star_Electric_Sections::subcategory_sections(
			array( 'link_label' => (string) ( $s['link_label'] ?? '' ) )
		);
	}
}
