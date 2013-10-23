<?php
	
/*
	Question2Answer (c) Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-plugin/async-email/qa-async-email-lang-en-GB.php
	Version: See define()s at top of qa-include/qa-base.php
	Description: UK English language phrases for async email plugin


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
	'asyncemail_enable_label' => 'Enable Async Email',
	'asyncemail_insertcount_label' => 'Emails packed into one insert query',
	'asyncemail_insertcount_suffix' => 'Emails',
	'asyncemail_insertcount_error' => 'Please specify positive integer.',
	'asyncemail_sendcount_label' => 'Emails sent at once',
	'asyncemail_sendcount_suffix' => 'Emails',
	'asyncemail_sendcount_error' => 'Please specify positive integer.',
	'asyncemail_retrycount_label' => 'If error, resent',
	'asyncemail_retrycount_suffix' => 'Times',
	'asyncemail_retrycount_error' => 'Please specify positive integer.',
	'asyncemail_save_button' => 'Save Changes',
	'asyncemail_dfl_button' => 'Restore Default',
	'asyncemail_saved_message' => 'Plugin settings saved',
	'asyncemail_create_error' => 'Cueue table creation error. When there is no creation authority, import qa_mailqueue.sql into DB by manual.',
);

/*
	Omit PHP closing tag to help avoid accidental output
*/