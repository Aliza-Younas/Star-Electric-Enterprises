<?php
/**
 * Brand Directory.
 *
 * The searchable, A-Z filterable list of every brand in the catalogue.
 *
 * The brands, their order, their product counts and their monograms all come
 * from the catalogue. The tiles are monograms because no dealership,
 * distribution or authorisation relationship is on record - that is a fact
 * about the business rather than a design choice, and the note above the grid
 * says so. Please leave it there until the business supplies logo files and
 * says what relationship, if any, it has with each brand.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Brand directory widget.
 */
class Star_Electric_Widget_Brand_Directory extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-brand-directory';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Brand Directory', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-nerd';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_note_section',
			array( 'label' => __( 'Standing note', 'star-electric' ) )
		);

		$this->note(
			__( 'This note is why the tiles are monograms. No dealership or distribution relationship is on record, so none may be implied. Please leave it in place.', 'star-electric' )
		);

		$this->area( 'note_strong', __( 'First sentence (bold)', 'star-electric' ), __( 'Brand names come from the approved product sources; the logo tiles are placeholders.', 'star-electric' ), 3 );
		$this->area( 'note_text', __( 'Rest of the note', 'star-electric' ), __( 'Star Electric Enterprises has not stated any dealership, distribution or authorisation relationship, so none is claimed here. Supplied logo files will replace these tiles.', 'star-electric' ), 4 );

		$this->end_controls_section();

		$this->start_controls_section(
			'star_search_section',
			array( 'label' => __( 'Search and A-Z', 'star-electric' ) )
		);

		$this->note(
			__( 'The whole alphabet is always shown, with the letters no brand starts with switched off, so the bar keeps its shape.', 'star-electric' )
		);

		$this->text( 'search_label', __( 'Search label', 'star-electric' ), __( 'Search brands', 'star-electric' ) );
		$this->text( 'search_hint', __( 'Search placeholder', 'star-electric' ), __( 'Start typing a brand name…', 'star-electric' ) );
		$this->text( 'all_label', __( '"All" button', 'star-electric' ), __( 'All', 'star-electric' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'star_empty_section',
			array( 'label' => __( 'When nothing matches', 'star-electric' ) )
		);

		$this->text( 'empty_title', __( 'Heading', 'star-electric' ), __( 'No brands match that search', 'star-electric' ) );
		$this->area( 'empty_text', __( 'Text', 'star-electric' ), __( 'Try a different letter or clear the search box to see the whole directory.', 'star-electric' ), 3 );

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		Star_Electric_Sections::brand_directory(
			array(
				'note_strong'  => (string) ( $s['note_strong'] ?? '' ),
				'note'         => (string) ( $s['note_text'] ?? '' ),
				'search_label' => (string) ( $s['search_label'] ?? '' ),
				'search_hint'  => (string) ( $s['search_hint'] ?? '' ),
				'all_label'    => (string) ( $s['all_label'] ?? '' ),
				'empty_title'  => (string) ( $s['empty_title'] ?? '' ),
				'empty_text'   => (string) ( $s['empty_text'] ?? '' ),
			)
		);
	}
}
