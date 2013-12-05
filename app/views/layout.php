<!doctype html>
<html lang="de-DE">
	<head>
		<meta charset="utf-8">
		<title>Dolores</title>

		<link rel="stylesheet" href="/css/normalize.css?v=1.0">
		<link rel="stylesheet" href="/css/2013.dolores.css?v=1.0">

		<!--[if lt IE 9]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>
	<body>
		<!--<h1>Dolores</h1>-->

		<main>
			<form action="/" id="filter-form" method="get">
				<input name="filter" placeholder="Kontakte durchsuchen" type="search" value="<?php print isset($filter) ? $filter : '' ?>" /><!--
				--><input type="submit" value="Suche starten" /><!--
			--></form>

			<form action="/contact/create" method="post">
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
					foreach($contacts as $contact) {
						print '<div class="contact">';
						print '<form action="/contact/update" method="post">';
							print sprintf('<input name="contact[_id]" type="hidden" value="%s" /> ', $contact->value->{'_id'});
							print sprintf('<input name="contact[_rev]" type="hidden" value="%s" /> ', $contact->value->{'_rev'});

							foreach($contact->value->fields as $key => $field) {
								print sprintf('<input name="contact[fields][%s][type]" type="hidden" value="%s" /> ', $key, $field->type);
								print sprintf('<input name="contact[fields][%s][label]" type="hidden" value="%s" />', $key, $field->label);
								print sprintf('<input name="contact[fields][%s][value]" placeholder="%s" type="text" value="%s" /> ', $key, $field->label, $field->value);
							}
							print sprintf('<a href="/contact/delete/%s/%s">Kontakt löschen</a>', $contact->value->{'_id'}, $contact->value->{'_rev'});
							print '<input type="submit" value="Kontakt speichern" />';
						print '</form>';
						print '</div>';					
					}
				}
			?>
		</main>

		<div id="version"><?php print $version; ?></div>

<!--		<script src="js/scripts.js"></script>-->
	</body>
</html>