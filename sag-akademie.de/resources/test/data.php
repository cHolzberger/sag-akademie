<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title>Untitled Document</title>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.js">
        </script>
        <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.js">
        </script>
        <script src="../../resources/scripts/jquery/jquery.ribbon.js">
        </script>
        <script src="../../resources/scripts/jquery/jquery.layout.js">
        </script>
        <style type="text/css">
            .ui-layout-pane-west {
            	/* OVERRIDE 'default styles' */
            	padding: 0 !important;
            	overflow: hidden !important;
            }
            
			tr, td {
				height: 20px;
				overflow: hidden;
				white-space: nowrap;
				width: 100px;
			}
						
			tr:hover {
				color: red;
				overflow: hidden;
			}
        </style>
		
        <link rel="stylesheet" type="text/css" href="../../css/theme/ui.all.css" media="screen"/>
        <link rel="stylesheet" type="text/css" href="../../css/theme/ui.theme.css" media="screen"/>
        <link rel="stylesheet" type="text/css" href="../../css/theme/ui.accordion.css" media="screen"/>
        <link rel="stylesheet" type="text/css" href="../../css/ui-ribbon.css" media="screen"/>
    </head>
    <body>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
            <title>Untitled Document</title>
        </head>
        <body>
        	<div style="position: relative; height: <?=500*20?>px;"> 
					
				<table  cellspacing=0 cellpadding=0 style="table-layout: fixed; position: fixed; height: auto;">		
				<colgroup>
						<col width="100" height="20"/>
						<col width="100" height="20"/>
						<col width="100" height="20"/>
						<col width="100" height="20"/>
						<col width="100" height="20"/>
						<col width="100" height="20"/>
						<col width="100" height="20"/>
						<col width="100" height="20"/>
						<col width="100" height="20"/>
						<col width="100" height="20"/>
				</colgroup>
				<tbody>
					<tr> <td>empty</td></tr>

			            </tbody>

			</table>
			</div>
			<script type="text/javascript">
			window.store = [
			<? for ($j=0; $j<500; $j++) { ?>
				{
				 <?for ( $i=0; $i<25; $i++) {?>
				 <?=$i?>: "Content <?=$j?> <?=$i?>",
			<? }?>},  
			<? }?>
			];
			
			$().ready(function() {
				
				$().scroll(function() {
					//var show = parseInt(window.scrollY / width);
					//$("table").css("top",window.scrollY +"px");
					//$("table").css("left",0);
					var offs = window.scrollY / 20.0;
					var start = parseInt (offs);
					var p = offs - start;
					
					var ht="";
					var max = start + 20;
					if ( start+20 > window.store.length ) max = window.store.length ; 
					 
					for ( var i=start; i < max; i++) {
					    ht = ht + "<tr>";
						ht = ht + "<td>" + window.store[i][0] + "</td>";
						ht = ht + "</tr>";
					}
					
					$("tbody").html(ht);
				});
			});
		</script>
        </body>
        </html>
