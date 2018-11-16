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
		<script>			
			$().ready(function() {
				$("tr:odd").css("background-color","gray");
								var tables= $("table");
				
				var width = 100*20;
				var lastShow = -1;
				var show = 0;
				$().scroll(function() {	
					setTimeout (function () {
					show = parseInt(window.scrollY / width);
					if ( show == lastShow ) return;
					tables.slice(0,show).hide();
					tables.slice(show-1, show+2).show();
					tables.slice(show+2).hide();
					/*for ( var i=0; i < tables.length; i++) {
						if ( i >= show-1 && i <= show+1) {
							tables.eq(i).not(":visible").show();
						} else {
							tables.eq(i).filter(":visible").hide();
						}
					}*/
					lastShow = show;
					},1);
				});
			});
		</script>
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
        	<div style="position: relative;"> 
			<? for ($k=0; $k<500; $k++){ ?> 
				<? if ( $k > 2): ?>
					<div style="position: relative; height: <?=50*20?>px;">
					<table  cellspacing=0 cellpadding=0 style="table-layout: fixed; display: none; height: <?50*20?>px;">
				<? else: ?>
					<div style="position: relative; height: <?=50*20?>px;">
					<table style="table-layout: fixed; height: <?50*20?>px; margin:0px; padding:0px;" cellspacing=0 cellpadding=0 height="2000">
				<? endif; ?>
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
				<tbody style="height: auto;">

        	<? for ($j=0; $j<50; $j++) { ?>
            	<tr>
                <?
                for ($i = 0; $i < 20; $i++) {
                ?>
                <td>
                    Column <?=$k?>:<?=$j?>:<?=$i?>
                </td>
                <? } ?>
				</tr>
			<? }?>
			            </tbody>

			</table>
			</div>
			<?}?>
			</div>
        </body>
        </html>
