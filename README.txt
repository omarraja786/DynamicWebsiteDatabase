The task has been completed using HTML/CSS/PHP/JQuery and AJAX but there is an issue with showing and hiding the username and password fields.
When initially opening up the update form, the username and password fields are being displayed regardless of the selected row's "is_user" status. Once the value of "is_user" is changed using the drop down list the function
which hides and displays the form fields updated accordingly. 


----------------------------------------------------------------------------
Login Screen works and is password encrypted; to login use the details
Username: admin
Password: test
----------------------------------------------------------------------------

Searching works dynamically by first name and last name so it supports full names too.

Sorting works for all columns and can be used by clicking on the table headers.

Adding users works fully and disables the entry for "Created" and "Last Updated" as these are set automatically.

User accounts can be created for stuff members when updating the row and the staff and user tables are linked via a foreign key.

Fields are shown and hidden depending on the is_user field excluding the fact of the small bug mentioned above.

Updated Staff Members functions correctly.



------------------------------------------------------------------------------------
Database has been exported as "phplogin.sql"