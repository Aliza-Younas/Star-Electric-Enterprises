<?php
/**
 * Complaint Form.
 *
 * The complaint form and the sidebar beside it, which share one grid and so are
 * one widget.
 *
 * The form is a real one: its nonce, its honeypot, what it accepts, how it is
 * validated, where the complaint is stored and who it is emailed to all belong
 * to Star_Electric_Forms and are not reachable from this panel.
 *
 * The page promises no resolution time. The store has supplied no complaints
 * procedure or turnaround, and stating one would be inventing a commitment -
 * please do not type one into the sidebar.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Complaint form widget.
 */
class Star_Electric_Widget_Complaint_Form extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-complaint-form';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Complaint Form', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-form-horizontal';
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
			__( 'Wording only, and no promised resolution time: the store has not agreed one. Which fields the form has and where a complaint goes are set by the site, not here.', 'star-electric' )
		);

		$this->text( 'heading', __( 'Heading', 'star-electric' ), __( 'Complaint Details', 'star-electric' ) );
		$this->text( 'note_text', __( 'Line under the heading', 'star-electric' ), __( 'The more detail you give, the faster we can look into it.', 'star-electric' ) );
		$this->text( 'required_label', __( '"required" label', 'star-electric' ), __( 'required', 'star-electric' ) );
		$this->text( 'submit', __( 'Button label', 'star-electric' ), __( 'Submit Complaint', 'star-electric' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'star_your_details',
			array( 'label' => __( 'Your details', 'star-electric' ) )
		);

		$this->text( 'legend_you', __( 'Group heading', 'star-electric' ), __( 'Your details', 'star-electric' ) );
		$this->text( 'label_name', __( 'Name label', 'star-electric' ), __( 'Full name', 'star-electric' ) );
		$this->text( 'label_order', __( 'Order number label', 'star-electric' ), __( 'Order number', 'star-electric' ) );
		$this->text( 'placeholder_order', __( 'Order number placeholder', 'star-electric' ), __( 'The reference on your order confirmation', 'star-electric' ) );
		$this->text( 'label_phone', __( 'Phone label', 'star-electric' ), __( 'Phone', 'star-electric' ) );
		$this->text( 'label_email', __( 'Email label', 'star-electric' ), __( 'Email', 'star-electric' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'star_problem',
			array( 'label' => __( 'What went wrong', 'star-electric' ) )
		);

		$this->text( 'legend_problem', __( 'Group heading', 'star-electric' ), __( 'What went wrong', 'star-electric' ) );
		$this->text( 'label_type', __( 'Complaint type label', 'star-electric' ), __( 'Complaint type', 'star-electric' ) );
		$this->text( 'type_prompt', __( 'First, empty option', 'star-electric' ), __( 'Select the type of problem…', 'star-electric' ) );

		$type = new \Elementor\Repeater();
		$type->add_control(
			'title',
			array( 'label' => __( 'Complaint type', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);
		$defaults = array();
		foreach ( Star_Electric_Sections::default_complaint_types() as $text ) {
			$defaults[] = array( 'title' => $text );
		}
		$this->add_control(
			'types',
			array(
				'label'       => __( 'Complaint types offered', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $type->get_controls(),
				'default'     => $defaults,
				'title_field' => '{{{ title }}}',
			)
		);

		$this->text( 'label_subject', __( 'Subject label', 'star-electric' ), __( 'Subject', 'star-electric' ) );
		$this->text( 'placeholder_subject', __( 'Subject placeholder', 'star-electric' ), __( 'A short summary of the problem', 'star-electric' ) );
		$this->text( 'label_description', __( 'Description label', 'star-electric' ), __( 'Description', 'star-electric' ) );
		$this->area( 'placeholder_description', __( 'Description placeholder', 'star-electric' ), __( 'What was ordered, what arrived, when it happened, and what you would like us to do.', 'star-electric' ), 3 );

		$this->add_control(
			'consent',
			array(
				'label'       => __( 'Consent line', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'default'     => __( 'I agree that my details may be used to investigate and respond to this complaint, as described in the [Privacy Policy]({privacy}).', 'star-electric' ),
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
			'submit',
			'legend_you',
			'legend_problem',
			'label_name',
			'label_order',
			'placeholder_order',
			'label_phone',
			'label_email',
			'label_type',
			'type_prompt',
			'label_subject',
			'placeholder_subject',
			'label_description',
			'placeholder_description',
			'consent',
		) as $key ) {
			$args[ $key ] = (string) ( $s[ $key ] ?? '' );
		}
		$args['note']  = (string) ( $s['note_text'] ?? '' );
		$args['types'] = $types ? $types : Star_Electric_Sections::default_complaint_types();

		Star_Electric_Sections::complaint_form( $args );
	}
}
