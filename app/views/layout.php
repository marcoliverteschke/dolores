<!doctype html>
<html class="no-js" lang="de-DE">
	<head>
		<meta charset="utf-8">
		<title>Dolores</title>

		<link rel="stylesheet" href="/css/normalize.css?v=1.0">
		<link rel="stylesheet" href="/css/font-awesome.min.css?v=1.0">
		<link rel="stylesheet" href="/css/2013.dolores.css?v=1.0">

		<!--[if lt IE 9]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<script src="/javascript/modernizr-2.7.1.js"></script>
		<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
		<script src="/javascript/dolores.js?v=1.0"></script>
	</head>
	<body>
		<!--<h1>Dolores</h1>-->

		<main>
			<form action="/#contacts" id="filter-form" method="get">
				<input name="filter" placeholder="Kontakte durchsuchen" type="search" value="<?php print isset($filter) ? $filter : '' ?>" /><!--
				--><input type="submit" value="Suche starten" /><!--
			--></form>

			<form action="/contact/create" id="create-form" method="post">
				<!-- Vorname -->
				<input name="contact[fields][0][type]" type="hidden" value="first_name" />
				<input name="contact[fields][0][label]" type="hidden" value="Vorname" />
				<input name="contact[fields][0][value]" placeholder="Vorname" type="text" />
				<!-- Nachname -->
				<input name="contact[fields][1][type]" type="hidden" value="last_name" />
				<input name="contact[fields][1][label]" type="hidden" value="Nachname" />
				<input name="contact[fields][1][value]" placeholder="Nachname" type="text" />
				<!-- E-Mail -->
				<input name="contact[fields][2][type]" type="hidden" value="email" />
				<input name="contact[fields][2][label]" type="hidden" value="E-Mail" />
				<input name="contact[fields][2][value]" placeholder="E-Mail" type="text" />
				<!-- Telefon -->
				<input name="contact[fields][3][type]" type="hidden" value="phone_landline_business" />
				<input name="contact[fields][3][label]" type="hidden" value="Telefon" />
				<input name="contact[fields][3][value]" placeholder="Telefon" type="text" />
				<!-- Mobil -->
				<input name="contact[fields][4][type]" type="hidden" value="phone_cell_business" />
				<input name="contact[fields][4][label]" type="hidden" value="Mobil" />
				<input name="contact[fields][4][value]" placeholder="Mobil" type="text" />

				<input type="submit" value="Kontakt erstellen" />
			</form>

			<?php
				if(isset($contacts)) {
					print '<section id="contacts">';
						foreach($contacts as $contact) {
							print '<div class="contact front-facing">';

								print '<div class="contact-view">';
									print sprintf('<h1>%s %s</h1>', render_field($contact->value->fields, 'first_name', false), render_field($contact->value->fields, 'last_name', false));
									$hide_fields = array('first_name', 'last_name');
									print '<ul class="contact-fields">';
									foreach($contact->value->fields as $key => $field) {
										if(!in_array($field->type, $hide_fields) && !empty($field->value)) {
											print sprintf('<li><label>%s</label><span>%s</span></li>', $field->label, $field->value);
										}
									}
									print '</ul>';
									print '<a class="contact-action contact-action-edit" href="javascript:void(0);" title="Kontakt bearbeiten"><i class="fa fa-pencil"></i></a>';
								print '</div>';

								print '<div class="contact-edit">';
									print '<form action="/contact/update" method="post">';
										print sprintf('<input name="contact[_id]" type="hidden" value="%s" /> ', $contact->value->{'_id'});
										print sprintf('<input name="contact[_rev]" type="hidden" value="%s" /> ', $contact->value->{'_rev'});

										print '<ul>';
										foreach($contact->value->fields as $key => $field) {
											print '<li>';
											print sprintf('<input name="contact[fields][%s][type]" type="hidden" value="%s" /> ', $key, $field->type);
											print sprintf('<input name="contact[fields][%s][label]" type="hidden" value="%s" />', $key, $field->label);
											print sprintf('<input name="contact[fields][%s][value]" placeholder="%s" type="text" value="%s" /> ', $key, $field->label, $field->value);
											print '</li>';
										}
										print '<li><input type="submit" value="Kontakt speichern" /></li>';
										print sprintf('<li class="d-d-d-danger"><a class="contact-action-delete" href="/contact/delete/%s/%s">Kontakt löschen</a></li>', $contact->value->{'_id'}, $contact->value->{'_rev'});
										print '</ul>';
									print '</form>';
									print '<a class="contact-action contact-action-close" href="javascript:void(0);" title="Formular schließen"><i class="fa fa-reply"></i></a>';
								print '</div>';

							print '</div>';					
						}
					print '</section>';
				}

				function render_field($fields, $field_name, $show_label = true) {
					if(is_array($fields)) {
						foreach($fields as $key => $field) {
							error_log(print_r($field, 1));
							if(isset($field->type) && $field->type === $field_name) {
								return ($show_label ? sprintf('<label>%s</label>', $field->label) : '') . sprintf('<span>%s</span>', $field->value);
							}
						}
					}
				}
			?>
		</main>

		<div id="version"><?php print $version; ?></div>

<!--		<script src="js/scripts.js"></script>-->
	</body>
</html>