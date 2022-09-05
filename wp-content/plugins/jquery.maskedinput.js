// more readable sample of jquery.maskedinput.js plugin
// http://digitalbush.com/projects/masked-input-plugin/

<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<title>untitled</title>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript"></script>
	<script src="jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
	
	<script type="text/javascript" charset="utf-8">
	jQuery(function($) {
		$.mask.definitions['~']='[+-]';
		$('#date').mask('99/99/9999');
		$('#phone').mask('(999) 999-9999');
		$('#phoneext').mask("(999) 999-9999? x99999");
		$("#tin").mask("99-9999999");
		$("#ssn").mask("999-99-9999");
		$("#product").mask("a*-999-a999",{placeholder:" ",completed:function(){alert("You typed the following: "+this.val());}});
		$("#eyescript").mask("~9.99 ~9.99 999");
	});
	</script>
</head>

<body>

	<p>
		The following example is a demonstration
	</p>
	<p>
		from
	</p>
	<p>
		the usage tab.
	</p>
	<table border="0">
		<tbody>
			<tr>
				<td>
					Date
				</td>
				<td>
					<input id="date" tabindex="1" type="text" />
				</td>
				<td>
					99/99/9999
				</td>
			</tr>
			<tr>
				<td>
					Phone
				</td>
				<td>
					<input id="phone" tabindex="3" type="text" />
				</td>
				<td>
					(999) 999-9999
				</td>
			</tr>
			<tr>
				<td>
					Phone + Ext
				</td>
				<td>
					<input id="phoneext" tabindex="4" type="text" />
				</td>
				<td>
					(999) 999-9999? x99999
				</td>
			</tr>
			<tr>
				<td>
					Tax ID
				</td>
				<td>
					<input id="tin" tabindex="5" type="text" />
				</td>
				<td>
					99-9999999
				</td>
			</tr>
			<tr>
				<td>
					SSN
				</td>
				<td>
					<input id="ssn" tabindex="6" type="text" />
				</td>
				<td>
					999-99-9999
				</td>
			</tr>
			<tr>
				<td>
					Product Key
				</td>
				<td>
					<input id="product" tabindex="7" type="text" />
				</td>
				<td>
					a*-999-a999
				</td>
			</tr>
			<tr>
				<td>
					Eye Script
				</td>
				<td>
					<input id="eyescript" tabindex="8" type="text" />
				</td>
				<td>
					~9.99 ~9.99 999
				</td>
			</tr>
		</tbody>
	</table>

</body>
</html>
