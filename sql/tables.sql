-- ################################################################
-- File: tables.sql
-- Date: 2018-05-10
-- Desc: SQL to create schema and tables for our site database.
-- Please follow the instructions to recreate the same tables locally!
-- It is important that you connect to the database that is created!
-- ################################################################

-- Adds user to postgreSQL

-- Username: group4lab
-- Password: see Trello board

-- INSTRUCTIONS: use through "Execute arbitrary SQL queries..."
CREATE ROLE group4lab LOGIN ENCRYPTED PASSWORD 'md5c57ba1c96506b1b6d77bbc7aa8f528ae'
SUPERUSER CREATEDB CREATEROLE REPLICATION
VALID UNTIL 'infinity';

-- Creates Database
-- INSTRUCTIONS: use through "Execute arbitrary SQL queries..."
CREATE DATABASE securitylab
WITH OWNER = group4lab
ENCODING = 'UTF8'
TABLESPACE = pg_default;

-- Creates securitylab schema
-- ============================================================
-- NOTE! NOTE! NOTE! NOTE! NOTE! NOTE! NOTE! NOTE! NOTE! NOTE!
-- =============================================================
-- First you need to connect to the new database that was made earlier!
-- In pgAdmin 3 you can go to "Execute arbitrary SQL queries..."
-- Look at the top bar with the icons. At the very end is a drop-down-list with
-- the current database you are connected to. Click on it and select <New connection...>.
-- Select the new user, the new database and localhost:5432.
-- It will ask you to sign in with the new user. Use the password from the Trello board!
-- After that you can execute the queries below.

-- To drop schema and all data in them, use:
-- DROP SCHEMA securitylab CASCADE;

CREATE SCHEMA securitylab
AUTHORIZATION group4lab;

-- Creates user-table
-- "CHECK (column <> ''::text)" checks that string is not empty (NOT NULL only checks for NULL)

CREATE TABLE securitylab.users(
  id SERIAL NOT NULL PRIMARY KEY,
  username VARCHAR(256) NOT NULL UNIQUE CHECK (username <> ''::text),
  password VARCHAR(256) NOT NULL CHECK (password <> ''::text),
  email VARCHAR(256) NOT NULL UNIQUE CHECK (email <> ''::text),
  verified BOOLEAN DEFAULT FALSE
);

-- Creates verify-table
-- Used when verifying a newly registered user.
-- Default for verifyTokenExpire is one hour from current_timestamp (if not entered manually).
CREATE TABLE securitylab.verify(
  id SERIAL NOT NULL PRIMARY KEY,
  user_id INTEGER UNIQUE NOT NULL REFERENCES securitylab.users(id),
  verify_token VARCHAR(256) NOT NULL CHECK (verify_token <> ''::text),
  verify_token_inserted_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Creates reset-table
-- Used when resetting a user's password.
-- Default for resetTokenExpire is one hour from current_timestamp (if not entered manually).
CREATE TABLE securitylab.reset(
  id SERIAL NOT NULL PRIMARY KEY,
  user_id INTEGER UNIQUE NOT NULL REFERENCES securitylab.users(id),
  reset_token VARCHAR(256) NOT NULL CHECK (reset_token <> ''::text),
  reset_token_inserted_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- Creates message-table
-- If a timestamp is not supplied for date, it picks the current time.

CREATE TABLE securitylab.message(
  id SERIAL NOT NULL PRIMARY KEY,
  user_id INTEGER NOT NULL REFERENCES securitylab.users(id) ON DELETE CASCADE,
  message TEXT NOT NULL CHECK (message <> ''::text),
  date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Creates vote-table
-- Message id and username id must be unique for each vote.

CREATE TABLE securitylab.vote(
  id SERIAL NOT NULL PRIMARY KEY,
  message_id INTEGER NOT NULL REFERENCES securitylab.message(id) ON DELETE CASCADE,
  user_id INTEGER NOT NULL REFERENCES securitylab.users(id) ON DELETE CASCADE,
  vote SMALLINT NOT NULL CHECK (vote = 1 OR vote = -1),
  UNIQUE(message_id, user_id)
);

-- Creates keyword-table
-- Maximum length of keyword is 24 characters (can be debated)
-- Each message must have unique keywords.

CREATE TABLE securitylab.keyword(
  id SERIAL NOT NULL PRIMARY KEY,
  message_id INTEGER NOT NULL REFERENCES securitylab.message(id) ON DELETE CASCADE,
  keyword VARCHAR(24) NOT NULL CHECK (keyword <> ''::text),
  UNIQUE(message_id, keyword)
);