<?php
/**
 * Contact Form.
 *
 * The enquiry form and the sidebar beside it, which share one grid and so are
 * one widget.
 *
 * The form is a real one. Its nonce, its honeypot, what it accepts, how it is
 * validated, where the message is stored and who it is emailed to are all
 * decided by Star_Electric_Forms and none of it is reachable from this panel -
 * an editor can change what the form *says*, never what it *does*.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contact form widget.
 */
class Star_Electric_Widget_Contact_Form extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-contact-form';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Contact Form', 'star-electric' );
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
			__( 'Wording only. Which fields the form has, how it is checked and where a message goes are set by the site, not here.', 'star-electric' )
		);

		$this->add_control(
			'heading',
			array(
				'label'   => __( 'Heading', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Send us a message', 'star-electric' ),
			)
		);
		$this->add_control(
			'note_text',
			array(
				'label'   => __( 'Line under the heading', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'We reply to the email address you give us.', 'star-electric' ),
			)
		);
		$this->add_control(
			'required_label',
			array(
				'label'   => __( '"required" label', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'required', 'star-electric' ),
			)
		);
		$this->add_control(
			'submit',
			array(
				'label'   => __( 'Button label', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Send Message', 'star-electric' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'star_fields',
			array( 'label' => __( 'Field labels', 'star-electric' ) )
		);

		foreach ( array(
			'label_name'    => __( 'Full name', 'star-electric' ),
			'label_phone'   => __( 'Phone', 'star-electric' ),
			'label_email'   => __( 'Email', 'star-electric' ),
			'label_subject' => __( 'Subject', 'star-electric' ),
			'label_message' => __( 'Message', 'star-electric' ),
		) as $key => $default ) {
			$this->add_control(
				$key,
				array(
					'label'   => $default,
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => $default,
				)
			);
		}

		$this->add_control(
			'placeholder',
			array(
				'label'   => __( 'Message placeholder', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => __( 'Tell us what you need — include ratings, sizes or model numbers where you know them.', 'star-electric' ),
			)
		);
		$this->add_control(
			'consent',
			array(
				'label'       => __( 'Consent line', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'default'     => __( 'I agree that my details may be used to respond to this message, as described in the [Privacy Policy]({privacy}).', 'star-electric' ),
				'description' => __( 'Write a link as [the words](https://the-url). {privacy} becomes the privacy policy page.', 'star-electric' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'star_subjects',
			array( 'label' => __( 'Subjects', 'star-electric' ) )
		);

		$this->add_control(
			'subject_prompt',
			array(
				'label'   => __( 'First, empty option', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Select a subject…', 'star-electric' ),
			)
		);

		$subject = new \Elementor\Repeater();
		$subject->add_control(
			'title',
			array( 'label' => __( 'Subject', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);

		$defaults = array();
		foreach ( Star_Electric_Sections::default_contact_subjects() as $text ) {
			$defaults[] = array( 'title' => $text );
		}

		$this->add_control(
			'subjects',
			array(
				'label'       => __( 'Subjects offered', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $subject->get_controls(),
				'default'     => $defaults,
				'title_field' => '{{{ title }}}',
			)
		);

		$this->end_controls_section();

		$this->sidebar_controls( 2 );
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		$subjects = array();
		foreach ( (array) ( $s['subjects'] ?? array() ) as $row ) {
			$text = trim( (string) ( $row['title'] ?? '' ) );
			if ( '' !== $text ) {
				$subjects[] = $text;
			}
		}

		Star_Electric_Sections::contact_form(
			array(
				'heading'        => (string) ( $s['heading'] ?? '' ),
				'note'           => (string) ( $s['note_text'] ?? '' ),
				'required_label' => (string) ( $s['required_label'] ?? '' ),
				'label_name'     => (string) ( $s['label_name'] ?? '' ),
				'label_phone'    => (string) ( $s['label_phone'] ?? '' ),
				'label_email'    => (string) ( $s['label_email'] ?? '' ),
				'label_subject'  => (string) ( $s['label_subject'] ?? '' ),
				'label_message'  => (string) ( $s['label_message'] ?? '' ),
				'subject_prompt' => (string) ( $s['subject_prompt'] ?? '' ),
				'subjects'       => $subjects ? $subjects : Star_Electric_Sections::default_contact_subjects(),
				'placeholder'    => (string) ( $s['placeholder'] ?? '' ),
				'consent'        => (string) ( $s['consent'] ?? '' ),
				'submit'         => (string) ( $s['submit'] ?? '' ),
				'cards'          => $this->sidebar_cards( $s, 2 ),
			)
		);
	}
}
