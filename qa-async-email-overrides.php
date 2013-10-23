<?php

/*
	Question2Answer (c) Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-plugin/async-email/qa-async-email-overrides.php
	Version: See define()s at top of qa-include/qa-base.php
	Description: Override function of qa-app-emails.php (qa_send_email)


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

require_once QA_INCLUDE_DIR.'qa-app-options.php';

if(!defined('QA_ASYNCEMAIL_NAME')) define('QA_ASYNCEMAIL_NAME', 'asyncemail');
if(!defined('QA_ASYNCEMAIL_ENABLE')) define('QA_ASYNCEMAIL_ENABLE', 'asyncemail_enable');
if(!defined('QA_ASYNCEMAIL_INSERTCOUNT')) define('QA_ASYNCEMAIL_INSERTCOUNT', 'asyncemail_insertcount');

$qa_asyncemail_buffers = array();

function qa_send_notification($userid, $email, $handle, $subject, $body, $subs, $async=false, $buffering=false)
/*
Send email to person with $userid and/or $email and/or $handle (null/invalid values are ignored or retrieved from
user database as appropriate). Email uses $subject and $body, after substituting each key in $subs with its
corresponding value, plus applying some standard substitutions such as ^site_title, ^handle and ^email.
*/
{
	global $qa_notifications_suspended;

	if(qa_opt(QA_ASYNCEMAIL_ENABLE)) {
		if($async) {
			if ($qa_notifications_suspended>0)
				return false;
			
			require_once QA_INCLUDE_DIR.'qa-db-selects.php';
			require_once QA_INCLUDE_DIR.'qa-util-string.php';
			
			if (isset($userid)) {
				$needemail=!qa_email_validate(@$email); // take from user if invalid, e.g. @ used in practice
				$needhandle=empty($handle);
				
				if ($needemail || $needhandle) {
					if (QA_FINAL_EXTERNAL_USERS) {
						if ($needhandle) {
							$handles=qa_get_public_from_userids(array($userid));
							$handle=@$handles[$userid];
						}
						
						if ($needemail)
							$email=qa_get_user_email($userid);
					
					} else {
						$useraccount=qa_db_select_with_pending(
							qa_db_user_account_selectspec($userid, true)
						);
						
						if ($needhandle)
							$handle=@$useraccount['handle'];

						if ($needemail)
							$email=@$useraccount['email'];
					}
				}
			}
				
			if (isset($email) && qa_email_validate($email)) {
				$subs['^site_title']=qa_opt('site_title');
				$subs['^handle']=$handle;
				$subs['^email']=$email;
				$subs['^open']="\n";
				$subs['^close']="\n";
			
				return qa_send_email(array(
					'fromemail' => qa_opt('from_email'),
					'fromname' => qa_opt('site_title'),
					'toemail' => $email,
					'toname' => $handle,
					'subject' => strtr($subject, $subs),
					'body' => (empty($handle) ? '' : qa_lang_sub('emails/to_handle_prefix', $handle)).strtr($body, $subs),
					'html' => false,
					),
					$async,		// Async mode
					$buffering	// Buffering mode
				);
			
			} else
				return false;
		} else {
			return qa_send_notification_base($userid, $email, $handle, $subject, $body, $subs);
		}
	} else {
		return qa_send_notification_base($userid, $email, $handle, $subject, $body, $subs);
	}
}

function qa_send_email($params, $async=false, $buffering=false)
/*
Send the email based on the $params array - the following keys are required (some can be empty): fromemail,
fromname, toemail, toname, subject, body, html
*/
{
	global $qa_asyncemail_buffers;
	
	if(qa_opt(QA_ASYNCEMAIL_ENABLE)) {
		if($async) {
			if($buffering) {
				$qa_asyncemail_buffers[] = array(
					'fromemail' => $params['fromemail'],
					'fromname' => $params['fromname'],
					'toemail' => $params['toemail'],
					'toname' => $params['toname'],
					'subject' => $params['subject'],
					'body' => $params['body'],
					'html' => $params['html']
					);
				if(count($qa_asyncemail_buffers) >= qa_opt(QA_ASYNCEMAIL_INSERTCOUNT)) {
					require_once QA_PLUGIN_DIR.'async-email/qa-async-email-flush-buffer.php';
					qa_asyncemail_flush_buffer();
				}
			} else {
				$sql  = "INSERT IGNORE INTO ^mailqueue (`fromemail`,`fromname`,`toemail`,`toname`,`subject`,`body`,`html`,`create`,`retrycount`,`errorinfo`) VALUES ($,$,$,$,$,$,$,NOW(),'0','')";
				return qa_db_query_sub($sql, $params['fromemail'], $params['fromname'], $params['toemail'], $params['toname'], $params['subject'], $params['body'], $params['html']);
			}
		} else {
			return qa_send_email_base($params);
		}
	} else {
		return qa_send_email_base($params);
	}
}

/*
	Omit PHP closing tag to help avoid accidental output
*/