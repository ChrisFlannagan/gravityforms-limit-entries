<?php

class GFLE_Settings {

	const SETTING_ID = 'gfle_limit_entries_amount';
	const SETTING_MAX_ENTRIES_REACHED = 'gfle_max_entries_reached';
	const REACHED_CTA = 'gfle_reached_cta';
	const REACHED_CAT_LINK = 'gfle_reached_cta_link';

	public function hook() {
		add_filter( 'gform_form_settings', [ $this, 'add_settings' ], 10, 2 );
		add_filter( 'gform_pre_form_settings_save', [ $this, 'save_settings' ] );
	}

	/**
	 * @param $settings
	 * @param $form
	 *
	 * @filter gform_form_settings
	 *
	 * @return mixed
	 */
	public function add_settings( $settings, $form ) {
		$settings[ $this->get_settings_key_label() ][ self::SETTING_ID ] = '
        <tr>
            <th><label for="' . self::SETTING_ID . '">' . __( 'Global Maximum Entries', 'gflimitentries' ) . '</label></th>
            <td><input type="number" min="0" value="' . rgar( $form, self::SETTING_ID ) . '" name="' . self::SETTING_ID . '"></td>
        </tr>';

		$settings[ $this->get_settings_key_label() ][ self::SETTING_MAX_ENTRIES_REACHED ] = '
        <tr>
            <th><label for="' . self::SETTING_MAX_ENTRIES_REACHED . '">' . __( 'Max Reached Message', 'gflimitentries' ) . '</label></th>
            <td><textarea name="' . self::SETTING_MAX_ENTRIES_REACHED . '">' . htmlentities2( rgar( $form, self::SETTING_MAX_ENTRIES_REACHED ) ) . '</textarea></td>
        </tr>';

		$settings[ $this->get_settings_key_label() ][ self::REACHED_CTA ] = '
        <tr>
            <th><label for="' . self::REACHED_CTA . '">' . __( 'Call To Action Text', 'gflimitentries' ) . '</label></th>
            <td><input value="' . esc_attr( rgar( $form, self::REACHED_CTA ) ) . '" name="' . self::REACHED_CTA . '"></td>
        </tr>';

		$settings[ $this->get_settings_key_label() ][ self::REACHED_CAT_LINK ] = '
        <tr>
            <th><label for="' . self::REACHED_CAT_LINK . '">' . __( 'Call To Action Link', 'gflimitentries' ) . '</label></th>
            <td><input value="' . esc_attr( rgar( $form, self::REACHED_CAT_LINK ) ) . '" name="' . self::REACHED_CAT_LINK . '"></td>
        </tr>';

		return $settings;
	}

	/**
	 * @param $form
	 *
	 * @filter gform_pre_form_settings_save
	 *
	 * @return mixed
	 */
	public function save_settings( $form ) {
		$form[ self::SETTING_ID ] = rgpost(  self::SETTING_ID  );
		$form[ self::SETTING_MAX_ENTRIES_REACHED ] = rgpost(  self::SETTING_MAX_ENTRIES_REACHED  );
		$form[ self::REACHED_CTA ] = rgpost(  self::REACHED_CTA  );
		$form[ self::REACHED_CAT_LINK ] = rgpost(  self::REACHED_CAT_LINK  );

		return $form;
	}

	public function get_settings_key_label() {
		return __( 'Limit Form Entries', 'gflimitentries' );
	}

}