<?php

/*
	Question2Answer (c) Gideon Greenspan

	http://www.question2answer.org/

	File: qa-plugin/async-email/qa-async-email-send.php
	Version: See define()s at top of qa-include/qa-base.php
	Description: Send emails queued in DB.


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
if (!defined('QA_VERSION')) { 
	require_once dirname(empty($_SERVER['SCRIPT_FILENAME']) ? __FILE__ : $_SERVER['SCRIPT_FILENAME']).'/../../qa-include/qa-base.php';
}
require_once QA_INCLUDE_DIR.'qa-app-options.php';
require_once QA_INCLUDE_DIR.'qa-app-emails.php';

if(!defined('QA_ASYNCEMAIL_NAME')) define('QA_ASYNCEMAIL_NAME', 'asyncemail');
if(!defined('QA_ASYNCEMAIL_ENABLE')) define('QA_ASYNCEMAIL_ENABLE', 'asyncemail_enable');
if(!defined('QA_ASYNCEMAIL_SENDCOUNT')) define('QA_ASYNCEMAIL_SENDCOUNT', 'asyncemail_sendcount');
if(!defined('QA_ASYNCEMAIL_RETRYCOUNT')) define('QA_ASYNCEMAIL_RETRYCOUNT', 'asyncemail_retrycount');
if(!defined('QA_ASYNCEMAIL_DEBUG')) define('QA_ASYNCEMAIL_DEBUG', false);

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../');
	exit;
}

/************************************
 Get settings
 ************************************/
$enable = (bool)qa_opt(QA_ASYNCEMAIL_ENABLE);
$sendcount = (int)qa_opt(QA_ASYNCEMAIL_SENDCOUNT);
$retrycount = (int)qa_opt(QA_ASYNCEMAIL_RETRYCOUNT);

if(QA_ASYNCEMAIL_DEBUG) {
	echo '<H2>Settings</H2>';
	echo 'enable = ' . $enable . '<BR>';
	echo 'sendcount = ' . $sendcount . '<BR>';
	echo 'retrycount = ' . $retrycount . '<BR>';
}

if ($enable) {
	/************************************
	 Get email data from DB
	 ************************************/
	$query = 'SELECT * FROM ^mailqueue WHERE retrycount < '.$retrycount.' ORDER BY id ASC LIMIT 0, '.$sendcount;
	$mails = qa_db_read_all_assoc(qa_db_query_sub($query));

	if(QA_ASYNCEMAIL_DEBUG) {
		echo '<H2>Mail Data</H2>';
		echo '<PRE>';
		var_dump($mails);
		echo '</PRE>';
	}

	/************************************
	 Send emails and Delete/Update records
	 ************************************/
	$params = array();
	if(QA_ASYNCEMAIL_DEBUG) echo '<p>--- Send Start ---</p>';
	foreach ($mails as $mail) {
		if(QA_ASYNCEMAIL_DEBUG) echo '<p>id = '.$mail['id'];
		//$mail['html'] = false;
		if(qa_send_email($mail)) {
			if(QA_ASYNCEMAIL_DEBUG) echo '<br>send complete !!';
			qa_async_email_delrec($mail['id']);
		} else {
			if(QA_ASYNCEMAIL_DEBUG) echo '<br>send error !!';
			if(($mail['retrycount']+1) < $retrycount) {
				if(QA_ASYNCEMAIL_DEBUG) echo '<br>lower of retry count';
				qa_async_email_updrec($mail['id'], $mail['retrycount']+1, 'Waiting for resending.');
			} else {
				if(QA_ASYNCEMAIL_DEBUG) echo '<br>higher of retry count';
				qa_async_email_updrec($mail['id'], $mail['retrycount']+1, 'Retry out.');
			}
		}
		if(QA_ASYNCEMAIL_DEBUG) echo '</p>';
	}
	if(QA_ASYNCEMAIL_DEBUG) echo '<p>--- Send End ---</p>';
}

function qa_async_email_delrec($id) {
	$sql = "DELETE IGNORE FROM ^mailqueue WHERE id = #";
	qa_db_query_sub($sql, $id);
}

function qa_async_email_updrec($id, $retrycount, $errorinfo) {
	$sql = "UPDATE IGNORE ^mailqueue SET retrycount=#, errorinfo=$ WHERE id = $";
	qa_db_query_sub($sql, $retrycount, $errorinfo, $id);
}

/*
	Omit PHP closing tag to help avoid accidental output
*/