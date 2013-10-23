<?php
	
/*
	Question2Answer (c) Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-plugin/async-email/qa-async-email-lang-ja.php
	Version: See define()s at top of qa-include/qa-base.php
	Description: Japanese language phrases for async email plugin


	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: http://www.question2answer.org/license.php
*/

return array(
	'asyncemail_enable_label' => 'Async Emailを有効にする',
	'asyncemail_insertcount_label' => '一つのインサートクエリーに束ねるメールの数',
	'asyncemail_insertcount_suffix' => '通',
	'asyncemail_insertcount_error' => '正の整数を指定してください',
	'asyncemail_sendcount_label' => '一度に送るメールの数',
	'asyncemail_sendcount_suffix' => '通',
	'asyncemail_sendcount_error' => '正の整数を指定してください',
	'asyncemail_retrycount_label' => '送信エラーの場合の再送回数',
	'asyncemail_retrycount_suffix' => '回',
	'asyncemail_retrycount_error' => '正の整数を指定してください',
	'asyncemail_save_button' => '変更を保存',
	'asyncemail_dfl_button' => '初期値に戻す',
	'asyncemail_saved_message' => 'プラグインの設定を保存しました。',
	'asyncemail_create_error' => 'キューテーブルの作成に失敗しました。テーブルの作成権限がない場合はマニュアルでqa_mailqueue.sqlをDBにインポートしてください。',
);

/*
	Omit PHP closing tag to help avoid accidental output
*/