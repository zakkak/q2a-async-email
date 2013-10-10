q2a-async-email
===============

Asynchronous Mail plugin for question2answer

1. Summary
----------
This package is a plugin for question2answer.

question2answer: http://www.question2answer.org/


2. Feature of this plugin
-------------------------
1. Asynchronous Mailing function can be added to Q2A.
2. This program overrides qa_send_notification() and qa_send_email() of qa-app-emails.php.
3. Nothing happens with installing this plugin only.
4. Mails from core program is immediately sent as before.
5. This program is used other plugin that send a lot of emails. (ex: notification to users).


3. Correspondence Version
-------------------------
question2answer V1.5 later

4. Installation/Settings
------------------------
1. Unzip archive any local folder.
2. Upload async-email folder under qa-plugin folder.
3. Log in administrator account.
4. Select admin -> plugins menu.
5. Click option of "async-email".
6. Enable async email, and save.
7. Call qa-async-email-send.php from CRON  

5. Uninstallation
-----------------
1. Log in administrator account.
2. Select admin -> plugins menu.
3. Click option of "async-email".
4. Click "Restore default" button.
5. Delete async-email folder under qa-plugin folder.
6. Drop table(qa_mailqueue) in DB.  

6. Options
----------
[Enable Async Email]
OFF: Mails from Q2A are immediately sent.
ON : The mail sent from the function by which the override was carried out
 is once accumulated in database. Then, it is transmitted little by little from
 another programs, such as CRON.

[Emails packed into one insert query]
E-mail data is compressed and inserted to one query.
When 100 is specified, as for 10000 mails, 100 times of insert-queries are sent.
If a value is enlarged, insertion performance will improve. However, it may become
an error if the maximum of "max_allowed_packet" of MySQL is exceeded.

[Emails sent at once]
The number of emails which qa-async-email-send.php sends by one call.
If a value is enlarged, the efficiency of transmission will become good, 
but server's load increases and it may be marked as SPAM from server's admin.

[If error, resent]
The number of times of resending when transmission of email becomes error.
When maximum is reached, email-data remains in table of DB.

7. License / Disclaimer
-----------------------
1. This software obeys license of Question2Answer.
2. The author does not always promise to support.
3. The author does not compensate you for what kind of damage that you used question2answer or this file.  

8. Author/Creator
-----------------
handle: sama55  
site: http://cmsbox.jp/

9. Version history
------------------
¡[2013/03/28] V1.0		First Release  
¡[2013/03/30] V1.0.1	fix read error of qa-async-email-flush-buffer.php  
¡[2013/05/23] V1.0.2	fix small bug (treatment of boolean value)  
¡[2013/09/23] V1.0.4	Add html field to qa_mailqueue table  
¡[2013/09/24] V1.0.5	Bug fix (Fixed 'html' = false in qa-async-email-send.php)  

Have fun !!
