<?php

	class CouchConnect {

		// CouchDB server URL
		var $couchdb_server_url = '';

		// CouchDB database name
		var $couchdb_database = '';

		// cURL library location
		var $curl_lib = 'curl/curl.php';

		// debug to error log
		var $debug = FALSE;


		public function save($in_object) {
			require_once($this->curl_lib);
			if(is_array($in_object)) {
				$in_object = json_decode(json_encode($in_object));
			}

			if(isset($in_object->{'_id'}) && !empty($in_object->{'_id'})) {
				$uuid = $in_object->{'_id'};
			} else {
				$uuid = md5('dolores-' . time());
			}

			$curl = new Curl;
			$response_json = $curl->put($this->couchdb_server_url . '/' . $this->couchdb_database . '/' . $uuid, json_encode($in_object));
			$response_object = json_decode($response_json);
//			$this->write_error_log(print_r($response_object, 1));			

			return $uuid;
		}


		public function find_all($params) {
			require_once($this->curl_lib);
			$curl = new Curl;
			$response_json = $curl->get($this->couchdb_server_url . '/' . $this->couchdb_database . '/_design/contacts/_view/find_all');
			$response_object = json_decode($response_json);
//			$this->write_error_log(print_r($response_object, 1));

			if(isset($response_object->rows) && is_array($response_object->rows)) {
				if(isset($params['conditions'])) {
					if(isset($params['conditions']['any'])) {
						$new_rows = array();
						foreach($response_object->rows as $key => $row) {
							if(preg_match("/^.*" . (string)$params['conditions']['any'] . ".*$/i", json_encode($row->value->fields))) {
								$new_rows[] = $row;
							}
						}
						$response_object->rows = $new_rows;
					}
				}

				if(isset($params['order'])) {
					usort($response_object->rows, array($this, 'sort_by_' . $params['order']));
				}
			}

			return $response_object;
		}


		public function delete($id, $rev) {
			require_once($this->curl_lib);
			$curl = new Curl;
			$delete_response_json = $curl->delete($this->couchdb_server_url . '/' . $this->couchdb_database . '/' . $id . '?rev=' . $rev);
			$delete_response_object = json_decode($delete_response_json);
		}


		private function sort_by_last_name_asc($a, $b) {
			$a_last_name = '';
			$b_last_name = '';
			foreach($a->value->fields as $field) {
				if($field->type === 'last_name') {
					$a_last_name = $field->value;
				}
			}
			foreach($b->value->fields as $field) {
				if($field->type === 'last_name') {
					$b_last_name = $field->value;
				}
			}
			return strnatcasecmp($a_last_name, $b_last_name);
		}


		public function database_exists() {
			require($this->curl_lib);
			$curl = new Curl;
			$response_json = $curl->get($this->couchdb_server_url . '/' . $this->couchdb_database);
			$response_object = json_decode($response_json);
			//			$this->write_error_log(print_r($response_object, 1));
			$database_exists = false;

			if(isset($response_object->db_name) && $response_object->db_name === $this->couchdb_database) {
				$database_exists = true;
			}

			return $database_exists;
		}


		private function write_error_log($message) {
			if($this->debug === TRUE) {
				error_log($message);
			}
		}


		public function create_database() {
			$curl = new Curl;
			$response_json = $curl->put($this->couchdb_server_url . '/' . $this->couchdb_database);
			$response_object = json_decode($response_json);
		//			$this->write_error_log(print_r($response_object, 1));
			return (isset($response_object->ok) && $response_object->ok);
		}


		public function create_views($designdocuments_data) {
			$okay = false;
			foreach($designdocuments_data as $designdocument_name => $designdocument_views) {
				
//				$this->write_error_log(json_encode($designdocument_views));

				$doc['views'] = $designdocument_views;

				$uuid = 'contacts';

				$curl = new Curl;
				$response_json = $curl->put($this->couchdb_server_url . '/' . $this->couchdb_database . '/_design/' . $uuid, json_encode($doc));
				$response_object = json_decode($response_json);
//				$this->write_error_log(print_r($response_object, 1));

				$okay = (isset($response_object->ok) && $response_object->ok);
			}
			return $okay;
		}


		public function can_write() {
			$test_data = new stdClass();
			$test_data->title = 'This is my rifle. There are many like it, but this one is mine.';

			$uuid = md5('dolores');

			$curl = new Curl;
			$response_json = $curl->put($this->couchdb_server_url . '/' . $this->couchdb_database . '/' . $uuid, json_encode($test_data));
			$response_object = json_decode($response_json);
//			$this->write_error_log(print_r($response_object, 1));

			return (isset($response_object->ok) && $response_object->ok);
		}


		public function can_read() {
			$uuid = md5('dolores');

			$curl = new Curl;
			$response_json = $curl->get($this->couchdb_server_url . '/' . $this->couchdb_database . '/' . $uuid);
			$response_object = json_decode($response_json);
//			$this->write_error_log(print_r($response_object, 1));

			return (isset($response_object->_id) && !isset($response_object->error));
		}


		public function can_update() {
			$uuid = md5('dolores');

			$curl = new Curl;
			$response_json = $curl->get($this->couchdb_server_url . '/' . $this->couchdb_database . '/' . $uuid);
			$response_object = json_decode($response_json);
//			$this->write_error_log(print_r($response_object, 1));

			$response_object->body = 'My rifle is my best friend. It is my life. I must master it as I must master my life. My rifle, without me, is useless. Without my rifle, I am useless. I must fire my rifle true. I must shoot straighter than my enemy who is trying to kill me. I must shoot him before he shoots me.';
			$response_json = $curl->put($this->couchdb_server_url . '/' . $this->couchdb_database . '/' . $uuid, json_encode($response_object));
			$response_object = json_decode($response_json);
//			$this->write_error_log(print_r($response_object, 1));

			return (isset($response_object->ok) && $response_object->ok);
		}


		public function can_delete() {
			$uuid = md5('dolores');

			$curl = new Curl;
			$response_json = $curl->get($this->couchdb_server_url . '/' . $this->couchdb_database . '/' . $uuid, array('revs_info' => 'true'));
			$response_object = json_decode($response_json);
			// $this->write_error_log(print_r($response_object, 1));

			if(isset($response_object->_revs_info) && is_array($response_object->_revs_info)) {
				$delete_response_json = $curl->delete($this->couchdb_server_url . '/' . $this->couchdb_database . '/' . $uuid . '?rev=' . $response_object->_revs_info[0]->rev);
				$delete_response_object = json_decode($delete_response_json);
				// $this->write_error_log(print_r($delete_response_object, 1));
			}

			return (isset($delete_response_object) && isset($delete_response_object->ok) && $delete_response_object->ok);
		}


	}