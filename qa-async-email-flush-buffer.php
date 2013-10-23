<?php

/*
	Question2Answer (c) Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-plugin/async-email/qa-async-email-flush-buffer.php
	Version: See define()s at top of qa-include/qa-base.php
	Description: Insert email data on global buffer


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

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../');
	exit;
}

function qa_asyncemail_flush_buffer()
{
	global $qa_asyncemail_buffers;
	$sql  = "INSERT IGNORE INTO ^mailqueue (`fromemail`,`fromname`,`toemail`,`toname`,`subject`,`body`,`html`,`create`,`retrycount`,`errorinfo`) VALUES ";
	$next = false;
	if(is_array($qa_asyncemail_buffers)) {
		foreach($qa_asyncemail_buffers as $i => $params) {
			if($next) $sql .= ',';
			$sql .= qa_db_apply_sub("($,$,$,$,$,$,$,NOW(),'0','')", array($params['fromemail'], $params['fromname'], $params['toemail'], $params['toname'], $params['subject'], $params['body'], $params['html']));
			unset($qa_asyncemail_buffers[$i]);
			$next = true;
		}
	}
	if($next)
		return qa_db_query_sub($sql);
	else
		return true;
}

/*
	Omit PHP closing tag to help avoid accidental output
*/