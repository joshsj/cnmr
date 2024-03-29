# CNMR

University project: an events ticketing site using PHP and MySQL.

At the time of writing, Joker has just been released and it's great, so I'm developing a cinema ticketing site.

## Requirements

### Front-end

- Use of modern HTML5 and CSS3
- Suitably attractive site with a consistent 'look and feel'
- Responsive design for mobile friendliness
- Basic accessibility features
- Extra design features, optionally using Javascript

### Back-end

- Implemented with PHP
- Support event searches, filtering, and sorting
  provide users with event

### RDBMS

- Implemented using MySQL
- Store event/venue information
- Secure database techniques

### Content Management System (CMS)

- UI for admins to add, edit, and delete event information
- Authenticated access

## Install

Follow the [Slim](http://www.slimframework.com/docs/v4/start/web-servers) for PHP install guide

## Getting Data

Using `film-data.py`

1. Get an API key for [OMDb](http://www.omdbapi.com/apikey.aspx)

2. Visit the IMDB page for a film, and copy the ID from the URL. It will begin with 'tt'

3. Call the script: `python film-data.py api-key id id id...`

4. Import the data in phpMyAdmin. When importing genres, check "Do not abort on INSERT error" to prevent errors on duplicate genres
