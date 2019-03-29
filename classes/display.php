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

		$class_amts = [];
		foreach ( $form['fields'] as $field ) {
			/** @var \GF_Field $field */
			if ( isset( $field->cssClass ) && strpos( $field->cssClass, self::LIMIT_AMT_CSS ) !== false ) {
				$input_id = '';
				foreach ( $field->inputs as $input ) {
					if ( $input['label'] === 'Quantity' ) {
						$input_id = $input['id'];
					}
				}

				$value = $field->get_value_submission( [] )[ $input_id ];
				$amount = explode( self::LIMIT_AMT_CSS, $field->cssClass )[1];
				if ( is_numeric( $amount ) && is_numeric( $value ) ) {
					$class_amts[] = (int) $amount * (int) $value;
				}
			}
		}

		if ( empty( $class_amts ) ) {
			$count = RGFormsModel::get_form_counts( $form[ 'id' ] );
			$option_key = self::FORM_REACHED_MAX_OPTION;
			if ( (int) $count[ 'total' ] < $limit ) {
				return $form;
			}
		} else {
			$option_key = self::FORM_REACHED_MAX_PRODUCT_OPTION;
			$count = [ 'total' => array_sum( $class_amts ) ];
			if ( (int) $count[ 'total' ] < $limit ) {
				return $form;
			}
		}

		$option_key = $option_key . '_' . $form['id'];
		$max_reached = get_option( $option_key, 0 );
		if ( ! empty( $max_reached ) && (int) $max_reached < $limit ) {
			$max_reached = 0;
			delete_option( $option_key );
		}

		if ( (int) $count['total'] === $limit && empty( $max_reached ) ) {
			update_option( $option_key, $count['total'] );
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
		$message = '<p class="gform_not_found gfle_max_message">' . esc_html_e( $this->form_message ) . '</p>';
		if ( ! empty( $this->form_cta ) && ! empty( $this->form_cta_link ) ) {
			$message .= '<p class="gform_not_found gfle_max_message_cta"><a href="' . esc_html( $this->form_cta_link ) . '">' . esc_html( $this->form_cta ) . '</a>';
		}

		return $message;
	}

}