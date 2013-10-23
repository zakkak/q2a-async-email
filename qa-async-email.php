<?php

/*
	Question2Answer (c) Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-plugin/async-email/qa-async-email.php
	Version: See define()s at top of qa-include/qa-base.php
	Description: Initiates async email plugin


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

class qa_async_email {

	const PLUGIN			= 'asyncemail';
	const ENABLE			= 'asyncemail_enable';
	const ENABLE_DFL		= false;
	const INSERTCOUNT		= 'asyncemail_insertcount';
	const INSERTCOUNT_DFL	= 100;
	const SENDCOUNT			= 'asyncemail_sendcount';
	const SENDCOUNT_DFL		= 50;
	const RETRYCOUNT		= 'asyncemail_retrycount';
	const RETRYCOUNT_DFL	= 3;
	const SAVE_BUTTON		= 'asyncemail_save_button';
	const DFL_BUTTON		= 'asyncemail_dfl_button';
	const SAVED_MESSAGE		= 'asyncemail_saved_message';
	const CREATE_ERROR		= 'asyncemail_create_error';

	var $directory;
	var $urltoroot;

	function load_module($directory, $urltoroot) {
		$this->directory=$directory;
		$this->urltoroot=$urltoroot;
	}

	function option_default($option) {
		if ($option==self::ENABLE) return self::ENABLE_DFL;
		if ($option==self::INSERTCOUNT) return self::INSERTCOUNT_DFL;
		if ($option==self::SENDCOUNT) return self::SENDCOUNT_DFL;
		if ($option==self::RETRYCOUNT) return self::RETRYCOUNT_DFL;
	}

	function admin_form(&$qa_content) {
		$saved=false;
		$error='';
		if (qa_clicked(self::SAVE_BUTTON)) {
			if(qa_post_text(self::ENABLE.'_field')) {
				if (trim(qa_post_text(self::INSERTCOUNT.'_field')) == '')
					$error = qa_lang_html(self::PLUGIN.'/'.self::INSERTCOUNT.'_error');
				if (!is_numeric(trim(qa_post_text(self::INSERTCOUNT.'_field'))))
					$error = qa_lang_html(self::PLUGIN.'/'.self::INSERTCOUNT.'_error');
				if (trim(qa_post_text(self::SENDCOUNT.'_field')) == '')
					$error = qa_lang_html(self::PLUGIN.'/'.self::SENDCOUNT.'_error');
				if (!is_numeric(trim(qa_post_text(self::SENDCOUNT.'_field'))))
					$error = qa_lang_html(self::PLUGIN.'/'.self::SENDCOUNT.'_error');
				if (trim(qa_post_text(self::RETRYCOUNT.'_field')) == '')
					$error = qa_lang_html(self::PLUGIN.'/'.self::RETRYCOUNT.'_error');
				if (!is_numeric(trim(qa_post_text(self::RETRYCOUNT.'_field'))))
					$error = qa_lang_html(self::PLUGIN.'/'.self::RETRYCOUNT.'_error');

				if(!qa_opt(self::ENABLE)) {
					$tblcreatesql = 'CREATE TABLE IF NOT EXISTS `^mailqueue` (';
					$tblcreatesql .= '`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,';
					$tblcreatesql .= '`fromemail` varchar(256) CHARACTER SET ascii DEFAULT NULL,';
					$tblcreatesql .= '`fromname` varchar(256) DEFAULT NULL,';
					$tblcreatesql .= '`toemail` varchar(256) CHARACTER SET ascii DEFAULT NULL,';
					$tblcreatesql .= '`toname` varchar(256) DEFAULT NULL,';
					$tblcreatesql .= '`subject` varchar(800) DEFAULT NULL,';
					$tblcreatesql .= '`body` varchar(8000) DEFAULT NULL,';
					$tblcreatesql .= '`html` tinyint(3) DEFAULT \'0\',';
					$tblcreatesql .= '`create` datetime NOT NULL,';
					$tblcreatesql .= '`retrycount` tinyint(3) unsigned DEFAULT \'0\',';
					$tblcreatesql .= '`errorinfo` varchar(512) DEFAULT NULL,';
					$tblcreatesql .= 'PRIMARY KEY (`id`)';
					$tblcreatesql .= ') ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';
					if(!qa_db_query_sub($tblcreatesql))
						$error = qa_lang_html(self::PLUGIN.'/'.self::CREATE_ERROR);
				}
			}
			if ($error == '') {
				qa_opt(self::ENABLE,(int)qa_post_text(self::ENABLE.'_field'));
				qa_opt(self::INSERTCOUNT,(int)qa_post_text(self::INSERTCOUNT.'_field'));
				qa_opt(self::SENDCOUNT,(int)qa_post_text(self::SENDCOUNT.'_field'));
				qa_opt(self::RETRYCOUNT,(int)qa_post_text(self::RETRYCOUNT.'_field'));
				$saved=true;
			}
		}
		if (qa_clicked(self::DFL_BUTTON)) {
			qa_opt(self::ENABLE,self::ENABLE_DFL);
			qa_opt(self::INSERTCOUNT,self::INSERTCOUNT_DFL);
			qa_opt(self::SENDCOUNT,self::SENDCOUNT_DFL);
			qa_opt(self::RETRYCOUNT,self::RETRYCOUNT_DFL);
			$saved=true;
		}
		
		$rules = array();
		$rules[self::INSERTCOUNT] = self::ENABLE.'_field';
		$rules[self::SENDCOUNT] = self::ENABLE.'_field';
		$rules[self::RETRYCOUNT] = self::ENABLE.'_field';
		qa_set_display_rules($qa_content, $rules);

		$ret = array();
		if($saved)
			$ret['ok'] = qa_lang_html(self::PLUGIN.'/'.self::SAVED_MESSAGE);
		else {
			if($error != '')
				$ret['ok'] = '<SPAN STYLE="color:#F00;">'.$error.'</SPAN>';
		}

		$fields = array();
		$fields[] = array(
			'id' => self::ENABLE,
			'label' => qa_lang_html(self::PLUGIN.'/'.self::ENABLE.'_label'),
			'type' => 'checkbox',
			'value' => (int)qa_opt(self::ENABLE),
			'tags' => 'NAME="'.self::ENABLE.'_field" ID="'.self::ENABLE.'_field"',
		);
		$fields[] = array(
			'id' => self::INSERTCOUNT,
			'label' => qa_lang_html(self::PLUGIN.'/'.self::INSERTCOUNT.'_label'),
			'type' => 'number',
			'value' => (int)qa_opt(self::INSERTCOUNT),
			'tags' => 'NAME="'.self::INSERTCOUNT.'_field" ID="'.self::INSERTCOUNT.'_field"',
			'suffix' => qa_lang_html(self::PLUGIN.'/'.self::INSERTCOUNT.'_suffix'),
		);
		$fields[] = array(
			'id' => self::SENDCOUNT,
			'label' => qa_lang_html(self::PLUGIN.'/'.self::SENDCOUNT.'_label'),
			'type' => 'number',
			'value' => (int)qa_opt(self::SENDCOUNT),
			'tags' => 'NAME="'.self::SENDCOUNT.'_field" ID="'.self::SENDCOUNT.'_field"',
			'suffix' => qa_lang_html(self::PLUGIN.'/'.self::SENDCOUNT.'_suffix'),
		);
		$fields[] = array(
			'id' => self::RETRYCOUNT,
			'label' => qa_lang_html(self::PLUGIN.'/'.self::RETRYCOUNT.'_label'),
			'type' => 'number',
			'value' => (int)qa_opt(self::RETRYCOUNT),
			'tags' => 'NAME="'.self::RETRYCOUNT.'_field" ID="'.self::RETRYCOUNT.'_field"',
			'suffix' => qa_lang_html(self::PLUGIN.'/'.self::RETRYCOUNT.'_suffix'),
		);
		$ret['fields'] = $fields;

		$buttons = array();
		$buttons[] = array(
			'label' => qa_lang_html(self::PLUGIN.'/'.self::SAVE_BUTTON),
			'tags' => 'NAME="'.self::SAVE_BUTTON.'" ID="'.self::SAVE_BUTTON.'"',
		);
		$buttons[] = array(
			'label' => qa_lang_html(self::PLUGIN.'/'.self::DFL_BUTTON),
			'tags' => 'NAME="'.self::DFL_BUTTON.'" ID="'.self::DFL_BUTTON.'"',
		);
		$ret['buttons'] = $buttons;

		return $ret;
	}
	
	function suggest_requests() {
		return array();
	}
	function match_request($request) {
		return false;
	}
	function process_request($request) {
		return qa_content_prepare();
	}
}

/*
	Omit PHP closing tag to help avoid accidental output
*/