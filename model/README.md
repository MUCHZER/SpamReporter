### Connect DB

## Structure DB

 - report
 	- id (int) {PK}
	- country (varchar(10))
	- number (varchar(45))
	- type (int)
	- date (timestamp)
	- resume (longtext)
	- author_id (int) {PK -> author}

 - author
 	- id (int) {PK}
	- first (varchar(45))
	- last (varchar(45))
	- pseudo (varchar(45))
	- mail (varchar(255))
	- password (varchar(255))
	- date (timestamp)
	- ipadress (varchar(255))
	- useragent (mediumtext)
	- registered (int)

 - comment
 	- id (int) {PK}
	- comment (varchar(255))
	- author_id (int) {PK -> author}
	- date (timestamp)
	- modified (timestamp)
  - report_id (int) {PK -> report}

 - vote
 	- id (int) {PK}
	- report_id (int) {PK -> report}
	- author_id (int) {PK -> author}
	- vote (tinyint)
	- date (timestamp)

## Prototype function

# Report


/**
* Function addReport($report)
*
* Add new phone number in report DB
*
* @param (array) $report = from form
* @return (bool) true if it works
*/

/**
* Function deleteReport
*
* !PROTOTYPE! for a panel admin
*
* @param
* @return
*/

/**
* Function updateReport
*
* !PROTOTYPE! author may edit his report
*
* @param
* @return
*/


# Comment

/**
* Function addComment($comment)
*
* Add new comment on a report
*
* @param (array)
* @return (bool) true if it works
*/

/**
* Function editComment($comment)
*
* Edit a comment
*
* @param (array) "report_id" => $report_id (int),
*		 "comment" => $comment (char),
*		 "id" => $id (int)
*		 "modified" => time() (timestamp)
* @return (bool) if no error = true
*/

/**
* Function deleteComment($id)
*
*
*
*
*
*/


## API Rest

## JSON view
