<?php

class GFLE_Settings {

	const SETTING_ID = 'gfle_limit_entries_amount';
	const SETTING_MAX_ENTRIES_REACHED = 'gfle_max_entries_reached';

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

		return $form;
	}

	public function get_settings_key_label() {
		return __( 'Limit Form Entries', 'gflimitentries' );
	}

}