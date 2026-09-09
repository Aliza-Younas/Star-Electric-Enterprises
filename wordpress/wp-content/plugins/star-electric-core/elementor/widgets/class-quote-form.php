<?php
/**
 * Quote Form.
 *
 * The quote request form and the sidebar beside it, which share one grid and so
 * are one widget.
 *
 * The form is a real one. Its nonce, its honeypot, what it accepts, how it is
 * validated, where the request is stored and who it is emailed to are all
 * decided by Star_Electric_Forms. So is what happens when a product page sends
 * a visitor here with a product in hand. None of that is reachable from this
 * panel: an editor can change what the form says, never what it does.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Quote form widget.
 */
class Star_Electric_Widget_Quote_Form extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-quote-form';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Quote Form', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-form-vertical';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_panel',
			array( 'label' => __( 'Panel', 'star-electric' ) )
		);

		$this->note(
			__( 'Wording only. Which fields the form has, how it is checked and where a request goes are set by the site, not here.', 'star-electric' )
		);

		$this->text( 'heading', __( 'Heading', 'star-electric' ), __( 'Quote Request', 'star-electric' ) );
		$this->text( 'note_text', __( 'Line under the heading', 'star-electric' ), __( 'The more detail you provide, the more accurate the quotation.', 'star-electric' ) );
		$this->text( 'required_label', __( '"required" label', 'star-electric' ), __( 'required', 'star-electric' ) );
		$this->text( 'quoting_for', __( '"Quoting for" label', 'star-electric' ), __( 'Quoting for:', 'star-electric' ) );
		$this->text( 'submit', __( 'Button label', 'star-electric' ), __( 'Submit Quote Request', 'star-electric' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'star_your_details',
			array( 'label' => __( 'Your details', 'star-electric' ) )
		);

		$this->text( 'legend_you', __( 'Group heading', 'star-electric' ), __( 'Your details', 'star-electric' ) );
		$this->text( 'label_name', __( 'Name label', 'star-electric' ), __( 'Full name', 'star-electric' ) );
		$this->text( 'label_company', __( 'Company label', 'star-electric' ), __( 'Company / Contractor name', 'star-electric' ) );
		$this->text( 'label_phone', __( 'Phone label', 'star-electric' ), __( 'Phone', 'star-electric' ) );
		$this->text( 'label_email', __( 'Email label', 'star-electric' ), __( 'Email', 'star-electric' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'star_project_details',
			array( 'label' => __( 'Project details', 'star-electric' ) )
		);

		$this->text( 'legend_project', __( 'Group heading', 'star-electric' ), __( 'Project details', 'star-electric' ) );
		$this->text( 'label_type', __( 'Project type label', 'star-electric' ), __( 'Project type', 'star-electric' ) );
		$this->text( 'type_prompt', __( 'First, empty option', 'star-electric' ), __( 'Select a project type…', 'star-electric' ) );

		$type = new \Elementor\Repeater();
		$type->add_control(
			'title',
			array( 'label' => __( 'Project type', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);
		$defaults = array();
		foreach ( Star_Electric_Sections::default_project_types() as $text ) {
			$defaults[] = array( 'title' => $text );
		}
		$this->add_control(
			'types',
			array(
				'label'       => __( 'Project types offered', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $type->get_controls(),
				'default'     => $defaults,
				'title_field' => '{{{ title }}}',
			)
		);

		$this->text( 'label_date', __( 'Delivery date label', 'star-electric' ), __( 'Required delivery date', 'star-electric' ) );
		$this->text( 'hint_date', __( 'Delivery date hint', 'star-electric' ), __( 'Approximate is fine.', 'star-electric' ) );
		$this->text( 'label_items', __( 'Requirements label', 'star-electric' ), __( 'Product requirements', 'star-electric' ) );
		$this->area( 'placeholder_items', __( 'Requirements placeholder', 'star-electric' ), __( "List the products you need — for example:\n\n1.5mm single core copper wire — 20 coils\n32A MCB single pole — 40 units\n12W LED bulb B22 — 150 units", 'star-electric' ), 6 );
		$this->text( 'hint_items', __( 'Requirements hint', 'star-electric' ), __( 'Include ratings, sizes and any brand preference where it matters.', 'star-electric' ) );
		$this->text( 'label_qty', __( 'Quantities label', 'star-electric' ), __( 'Estimated total quantities / order value', 'star-electric' ) );
		$this->text( 'placeholder_qty', __( 'Quantities placeholder', 'star-electric' ), __( 'e.g. approx. 400 items, or an approximate budget range', 'star-electric' ) );
		$this->text( 'label_notes', __( 'Notes label', 'star-electric' ), __( 'Additional notes', 'star-electric' ) );
		$this->area( 'placeholder_notes', __( 'Notes placeholder', 'star-electric' ), __( 'Site location, phased delivery, access restrictions, anything else we should know…', 'star-electric' ), 3 );

		$this->add_control(
			'consent',
			array(
				'label'       => __( 'Consent line', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'default'     => __( 'I agree that my details may be used to respond to this quote request, as described in the [Privacy Policy]({privacy}).', 'star-electric' ),
				'description' => __( 'Write a link as [the words](https://the-url). {privacy} becomes the privacy policy page.', 'star-electric' ),
			)
		);

		$this->end_controls_section();

		$this->sidebar_controls();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		$types = array();
		foreach ( (array) ( $s['types'] ?? array() ) as $row ) {
			$text = trim( (string) ( $row['title'] ?? '' ) );
			if ( '' !== $text ) {
				$types[] = $text;
			}
		}

		$args = array( 'cards' => $this->sidebar_cards( $s ) );
		foreach ( array(
			'heading',
			'required_label',
			'quoting_for',
			'submit',
			'legend_you',
			'legend_project',
			'label_name',
			'label_company',
			'label_phone',
			'label_email',
			'label_type',
			'type_prompt',
			'label_date',
			'hint_date',
			'label_items',
			'placeholder_items',
			'hint_items',
			'label_qty',
			'placeholder_qty',
			'label_notes',
			'placeholder_notes',
			'consent',
		) as $key ) {
			$args[ $key ] = (string) ( $s[ $key ] ?? '' );
		}
		$args['note']  = (string) ( $s['note_text'] ?? '' );
		$args['types'] = $types ? $types : Star_Electric_Sections::default_project_types();

		Star_Electric_Sections::quote_form( $args );
	}
}
