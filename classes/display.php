<?php

class GFLE_Display {

	private $form_message;

	public function hook() {
		add_filter( 'gform_pre_render', [ $this, 'display_or_nullify_form' ], 10, 1 );
		add_filter( 'gform_form_not_found_message', [ $this, 'display_message' ] );
	}

	/**
	 * @param array $form
	 *
	 * @return array|null;
	 */
	public function display_or_nullify_form( $form ) {
		$limit = isset( $form[ GFLE_Settings::SETTING_ID ] ) ? (int) $form[ GFLE_Settings::SETTING_ID ] : 0;
		if ( empty( $limit ) ) {
			return $form;
		}

		$count = RGFormsModel::get_form_counts( $form['id'] );
		if ( (int) $count['total'] < $limit ) {
			return $form;
		}

		$this->form_message = $form[ GFLE_Settings::SETTING_MAX_ENTRIES_REACHED ];

		return null;
	}

	public function display_message() {
		return '<p class="gform_not_found">' . esc_html_e( $this->form_message ) . '</p>';
	}

}