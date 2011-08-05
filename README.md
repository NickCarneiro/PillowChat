# PillowTalk

A simple chat room built in an effort to learn CouchDB and PHPillow.

## Architecture Overview

The front end is a typical chat window with a view of recent messages, an input box, and a list of users present. The front end relies heavily upon jQuery which communicates with PHP on the server using JSON POST requests. All chat messages are stored as documents.

## Special features
PillowTalk supports [tripcodes](http://en.wikipedia.org/wiki/Tripcode) for simple authentication, but these would not likely stand up to an attack by a determined adversary.

## Install
* Install CouchDB
* Install PHPillow using PEAR
* Create a database in CouchDB's Futon interface
* Copy PillowTalk files to your web server
* Set your database details in settings.php
