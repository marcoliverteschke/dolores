<?php

	$couch_views['contacts']['find_all']['map'] = '
		function(doc) {
			emit(doc._id, doc);
		}';