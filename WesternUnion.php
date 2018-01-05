<?php
class WesternUnion {
	public $settings = array(
		'description' => 'Provide payment information for Western Union money transfers.',
	);
	function payment_features() {
		return '';
	}
	function payment_button($params) {
		global $billic, $db;
		if (get_config('westernunion_instructions') == '') {
			return false;
		}
		return 'Pay by Western Union';
	}
	function payment_page($params) {
		global $billic, $db;
		if (get_config('westernunion_instructions') == '') {
			return 'Western Union is not configured';
		}
		$html = '';
		if ($billic->user['verified'] == 0 && get_config('westernunion_require_verification') == 1) {
			return 'verify';
		} else if (get_config('westernunion_min_payment') != '' && $params['invoice']['total'] < get_config('westernunion_min_payment')) {
			return 'The minimum payment to pay by Western Union is ' . get_config('billic_currency_prefix') . get_config('westernunion_min_payment') . get_config('billic_currency_suffix') . '. Please <a href="/User/MyAccount/Action/AddFunds">click here</a> to generate a new invoice to add credit to your account of at least ' . get_config('billic_currency_prefix') . get_config('westernunion_min_payment') . get_config('billic_currency_suffix') . '.';
		} else {
			$html.= nl2br(str_replace('{$invoiceid}', $params['invoice']['id'], get_config('westernunion_instructions')));
		}
		return $html;
	}
	function settings($array) {
		global $billic, $db;
		if (empty($_POST['update'])) {
			echo '<form method="POST"><input type="hidden" name="billic_ajax_module" value="WesternUnion"><table class="table table-striped">';
			echo '<tr><th>Setting</th><th>Value</th></tr>';
			echo '<tr><td>Require Verification</td><td><input type="checkbox" name="westernunion_require_verification" value="1"' . (get_config('westernunion_require_verification') == 1 ? ' checked' : '') . '></td></tr>';
			echo '<tr><td>Minimum Payment</td><td><input class="form-control" type="text" name="westernunion_min_payment" value="' . safe(get_config('westernunion_min_payment')) . '"></td></tr>';
			echo '<tr><td>Instructions</td><td><textarea class="form-control" name="westernunion_instructions">' . safe(get_config('westernunion_instructions')) . '</textarea></td></tr>';
			echo '<tr><td colspan="2" align="center"><input type="submit" class="btn btn-default" name="update" value="Update &raquo;"></td></tr>';
			echo '</table></form>';
		} else {
			if (empty($billic->errors)) {
				set_config('westernunion_require_verification', $_POST['westernunion_require_verification']);
				set_config('westernunion_min_payment', $_POST['westernunion_min_payment']);
				set_config('westernunion_instructions', $_POST['westernunion_instructions']);
				$billic->status = 'updated';
			}
		}
	}
}
