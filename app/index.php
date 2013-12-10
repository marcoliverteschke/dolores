<?php

	require('lib/flight/Flight.php');
	require('config.php');
	require('lib/curl/curl.php');
	require('lib/CouchConnect.php');
	require('couch_views.php');
	require('lib/lessc.inc.php');


	Flight::before('start', function(){
		global $dolores_config;
		$request = Flight::request();

		lessc::ccompile('css/2013.dolores.less', 'css/2013.dolores.css');

		Flight::view()->set('version', '0.1.2');

		if(!Flight::has('couchdb_connect')) {
			$couchdb_connect = new CouchConnect;
			$couchdb_connect->couchdb_server_url = $dolores_config['couch']['host'];
			$couchdb_connect->couchdb_database = $dolores_config['couch']['db'];
			$couchdb_connect->debug = TRUE;

			Flight::set('couchdb_connect', $couchdb_connect);
		}

		$couchdb_connect = Flight::get('couchdb_connect');
		
		if($request->url !== '/setup') {
			if(!$couchdb_connect->database_exists()) {
				Flight::redirect('/setup');
			}
		}
	});


	Flight::route('/setup', function(){
		$couchdb_connect = Flight::get('couchdb_connect');
		global $couch_views;

		print 'Dolores Setup';

		print '<p>Do we have a database? ' . ($couchdb_connect->database_exists() ? 'YES' : 'NO') . '</p>';
		if(!$couchdb_connect->database_exists()) {
			print '<p>Can I create a database? ' . ($couchdb_connect->create_database() ? 'YES' : 'NO') . '</p>';
			print '<p>Do we have a database now? ' . ($couchdb_connect->database_exists() ? 'YES' : 'NO') . '</p>';
		}
		print '<p>Can we write? ' . ($couchdb_connect->can_write() ? 'YES' : 'NO') . '</p>';
		print '<p>Can we read? ' . ($couchdb_connect->can_read() ? 'YES' : 'NO') . '</p>';
		print '<p>Can we update? ' . ($couchdb_connect->can_update() ? 'YES' : 'NO') . '</p>';
		print '<p>Can we delete? ' . ($couchdb_connect->can_delete() ? 'YES' : 'NO') . '</p>';
		print '<p>Can we create our views? ' . ($couchdb_connect->create_views($couch_views) ? 'YES' : 'NO') . '</p>';
		print '<p><a href="/">Return to app</a></p>';
	});


	Flight::route('/contact/delete/@id/@rev', function($id, $rev){
		$request = Flight::request();

		$couchdb_connect = Flight::get('couchdb_connect');
		$couchdb_connect->delete($id, $rev);

		Flight::redirect('/');
	});


	Flight::route('/contact/update', function(){
		$request = Flight::request();

		$couchdb_connect = Flight::get('couchdb_connect');
		$couchdb_connect->save($request->data['contact']);

		Flight::redirect('/');
	});


	Flight::route('/contact/create', function(){
		$request = Flight::request();

		$couchdb_connect = Flight::get('couchdb_connect');
		$couchdb_connect->save($request->data['contact']);

		Flight::redirect('/');
	});


	Flight::route('/', function(){
		$couchdb_connect = Flight::get('couchdb_connect');

		$request = Flight::request();

		$conditions = array();
		if(isset($request->query['filter'])) {
			$conditions['any'] = $request->query['filter'];
			Flight::view()->set('filter', $request->query['filter']);
		}

		$results = $couchdb_connect->find_all(array('conditions' => $conditions, 'order' => 'last_name_asc'));

		if(isset($results->rows) && is_array($results->rows)) {
			Flight::view()->set('contacts', $results->rows);
		}

		Flight::render('layout');
	});

	Flight::start();