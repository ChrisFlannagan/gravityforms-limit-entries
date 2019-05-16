<?php

/**
 * Class GFLE_Display
 *
 * @property string $form_message;
 * @property string $form_cta;
 * @property string $form_cta_link;
 */
class GFLE_Display {

	const FORM_REACHED_MAX_OPTION = 'form_reached_max_option_';
	const FORM_REACHED_MAX_PRODUCT_OPTION = 'form_reached_max_product_option_';
	const LIMIT_AMT_CSS = 'limit-amt-';

	private $form_message;
	private $form_cta;
	private $form_cta_link;

	public function hook() {
		add_filter( 'gform_field_validation', [ $this, 'validate_amount' ], 10, 4 );
		add_filter( 'gform_pre_render', [ $this, 'display_or_nullify_form' ], 10, 1 );
		add_filter( 'gform_form_not_found_message', [ $this, 'display_message' ] );
	}

	public function validate_amount( $result, $value, $form, $field ) {
		if ( ! $this->is_limited_form( $form ) ) {
			return $result;
		}

		$field_id = isset( $form[ GFLE_Settings::SETTING_FIELD_ID_TO_COUNT ] ) ? (float) $form[ GFLE_Settings::SETTING_FIELD_ID_TO_COUNT ] : 0;
		if ( empty( $field_id ) ) {
			return $result;
		}

		if ( ! is_array( $value ) || ! isset( $value[ (string) $field_id ] ) || ! is_numeric( $value[ (string) $field_id ] ) ) {
			return $result;
		}

		$total = $this->get_total( $form );
		$total_with_submission = (int) $value[ (string) $field_id ] + (int) $total;
		$limit = (int) $this->get_limit( $form );

		if ( $result['is_valid'] && $total_with_submission > $limit ) {
			$result['is_valid'] = false;
			$result['message'] = 'Limit is nearly reached and you can only set to ' . ( $limit - $total ) . ' or less';
		}

		return $result;
	}

	/**
	 * @param array $form
	 *
	 * @return array|null;
	 */
	public function display_or_nullify_form( $form ) {
		if ( ! $this->is_limited_form( $form ) ) {
			return $form;
		}

		$total = $this->get_total( $form );
		$limit = $this->get_limit( $form );
		if ( $total < $limit ) {
			return $form;
		}

		$option_key = self::FORM_REACHED_MAX_OPTION . '_' . $form['id'];
		$max_reached = get_option( $option_key, 0 );
		if ( ! empty( $max_reached ) && (int) $max_reached < $limit ) {
			$max_reached = 0;
			delete_option( $option_key );
		}

		if ( $total === $limit && empty( $max_reached ) ) {
			update_option( $option_key, $total );
			return $form;
		}

		$this->form_message = $form[ GFLE_Settings::SETTING_MAX_ENTRIES_REACHED ];
		$this->form_cta = $form[ GFLE_Settings::REACHED_CTA ];
		$this->form_cta_link = $form[ GFLE_Settings::REACHED_CAT_LINK ];

		return null;
	}

	/**
	 * @return string
	 */
	public function display_message() {
		$message = '<p class="gform_not_found gfle_max_message">' . esc_html( $this->form_message ) . '</p>';
		if ( ! empty( $this->form_cta ) && ! empty( $this->form_cta_link ) ) {
			$message .= '<p class="gform_not_found gfle_max_message_cta"><a href="' . esc_html( $this->form_cta_link ) . '">' . esc_html( $this->form_cta ) . '</a>';
		}

		return $message;
	}

	private function is_limited_form( $form ) {
		$limit = isset( $form[ GFLE_Settings::SETTING_ID ] ) ? (int) $form[ GFLE_Settings::SETTING_ID ] : 0;
		if ( empty( $limit ) ) {
			return false;
		}

		$field_id = isset( $form[ GFLE_Settings::SETTING_FIELD_ID_TO_COUNT ] ) ? (float) $form[ GFLE_Settings::SETTING_FIELD_ID_TO_COUNT ] : 0;
		if ( empty( $field_id ) ) {
			return false;
		}

		return true;
	}

	private function get_limit( $form ) {
		return isset( $form[ GFLE_Settings::SETTING_ID ] ) ? (int) $form[ GFLE_Settings::SETTING_ID ] : 0;
	}

	private function get_total( $form ) {
		$field_id = isset( $form[ GFLE_Settings::SETTING_FIELD_ID_TO_COUNT ] ) ? (float) $form[ GFLE_Settings::SETTING_FIELD_ID_TO_COUNT ] : 0;
		if ( empty( $field_id ) ) {
			return 0;
		}

		$total = 0;
		$entries = GFAPI::get_entries( $form['id'] );
		foreach ( $entries as $entry ) {
			if ( isset( $entry[ (string) $field_id ] ) && is_numeric( $entry[ (string) $field_id ] ) ) {
				$total += (int) $entry[ (string) $field_id ];
			}
		}

		return $total;
	}
}