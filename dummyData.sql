-- ################################################################
-- File: dummyData.sql
-- Date: 2018-05-15
-- Desc: SQL to create data to fill the tables as in tables.sql

-- NB: Before running these sql codes, please make sure that you have created
--     a database with all the specifications as defined in tables.sql.
-- ################################################################

			-- =======================================
			-- Filling data(dummy) for the users table
			-- =======================================
/*
  get passwords in php using

  function getHashedPassword($password) : void{
    echo password_hash($password, PASSWORD_DEFAULT);
}
*/
-- blongho, m1dj:Angowins@, blongho02@gmail.com, unverified
INSERT INTO securitylab.users (username, password, email, verified)
	VALUES ('blongho', '$2y$10$RWpL7ALUckG0f6xC1pApEOqRm5UmUt4XLReXidx0s9yk1GpDjjmzq',
	'blongho02@gmail.com', false);

-- xavijeff, w06!mvR0_, xavilo500@gmail.com, unverified
INSERT INTO securitylab.users (username, password, email, verified)
	VALUES ('xavijeff', '$2y$10$tZuiYSbt9yqRP3wK53cuu.v3OuuXtRk0qSMmS6R43EGYySPO/VnVS',
	'xavilo300@gmail.com', false);

-- marviso, yke_!@gekl2, marvis2000@yahoo.com
INSERT INTO securitylab.users (username, password, email, verified)
	VALUES ('marviso', '$2y$10$PyPZzBdz1/oyC7DtMmt7MetJD4eDf1xrd5v/lblexvrDJgsN7zfGq',
	'marvis2000@yahoo.com', false);



			-- =========================================
			-- Data(dummy) for the messages table table
			-- =========================================

-- blongho writes the first post. He is just learned hacking and brags about it but he soon
-- realizes it is not as easy as in the books
INSERT INTO securitylab.message (user_id, message)
	VALUES (
	(SELECT id FROM securitylab.users u WHERE u.username = 'blongho'),
	'When i saw this site, i thought that i could hack within 30 minutes. It has ' ||
	 'proven to be very hard so far. Who are those behind this site?!'
	);

-- marviso writes the second statement. She is things the shape of the world changes with time
INSERT INTO securitylab.message (user_id, message)
	VALUES (
	(SELECT id FROM securitylab.users u WHERE u.username = 'marviso'),
	'When i was born, i thought that the world was flat. When i went to school, ' ||
	 'i was told that the world is round. Maybe by the time i get to the age' ||
	  'of 65, the world will be square. Who knows?'
	);

-- xavijeff writes the third message. He is confused about some general statements
INSERT INTO securitylab.message (user_id, message)
	VALUES (
	(SELECT id FROM securitylab.users u WHERE u.username = 'xavijeff'),
	'Some people say religion is the opium of the masses. That only the masses' ||
	 'console themselves with such thing as religion. Some say politics is ' ||
	  'a game, a very dirty game. What do you say these are? Still think...'
	);


			-- =======================================
			-- Filling data(dummy) for the votes table
			-- =======================================
/*
	After successful execution of the codes below, if you are using phppgadmin
	i.e working with http://127.0.0.1/phppgadmin/ for the database management,
	use

	SELECT * from securitylab.vote

	Otherwise, the results you will see will be table for the command

	SELECT "vote", count(*) AS "count" FROM "securitylab"."vote" GROUP BY "vote" ORDER BY "vote"
 */

 -- blongho down-votes for the second message
INSERT INTO securitylab.vote (message_id, user_id, vote)
	VALUES(
 		(SELECT id FROM securitylab.message m WHERE m.id = 2),
 		(SELECT id FROM securitylab.users u WHERE u.username = 'blongho'), -1
 	);

-- marviso up-votes the first message
INSERT INTO securitylab.vote (message_id, user_id, vote)
	VALUES(
 		(SELECT id FROM securitylab.message m WHERE m.id = 1),
 		(SELECT id FROM securitylab.users u WHERE u.username = 'marviso'), 1
 	);

-- xavijess up-votes the third message
INSERT INTO securitylab.vote (message_id, user_id, vote)
 	VALUES(
 		(SELECT id FROM securitylab.message m WHERE m.id = 3),
 		(SELECT id FROM securitylab.users u WHERE u.username = 'xavijeff'),	1
 	);


			-- =======================================
			-- Filling data(dummy) for the votes table
			-- =======================================

-- keywords for the first message
INSERT INTO securitylab.keyword(message_id, keyword)
	VALUES(
		(SELECT id FROM securitylab.message m WHERE m.id = 1), 'hacking'
	);


-- keywords for the third message
INSERT INTO securitylab.keyword(message_id, keyword)
	VALUES(
		(SELECT id FROM securitylab.message m WHERE m.id = 3), 'politics'
	);

	-- keywords for the second message
INSERT INTO securitylab.keyword(message_id, keyword)
	VALUES(
		(SELECT id FROM securitylab.message m WHERE m.id = 2), 'earth'
	);