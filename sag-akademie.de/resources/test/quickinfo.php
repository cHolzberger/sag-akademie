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
        <xscript type="text/javascript">
            
            $(document).ready(function() {
            	$('body').layout({
            		applyDefaultStyles: true
            	});
            	
            	$("#quickbar .accordion").accordion({
            		fillSpace: true
            	})
            });
        </xscript>
		<style type="text/css">
			.ui-layout-pane-west {
		/* OVERRIDE 'default styles' */
		padding: 0 !important;
		overflow: hidden !important;
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
            <DIV class="ui-layout-center">
                Center 
                pr0n  
                $(".searchme div.searchable").each ( function () { if ( $(this).text().search("Wohever") > -1 ) $(this).show(); else $(this).hide() });
				
				<br/><br/>
				
				
				
				
				<div style="width: 400px; height: 400px; overflow: scroll; position: absolute; ">
					<div class="col" style="position: absolute; width: 100px; height: 100%; float: left; border-right: 1px solid black;">
					<? for ( $i=0; $i<5000; $i++) { ?>
						<div class="cell" style="overflow: hidden;  width: 100px; height: 25px; border-bottom: 1px solid black"> 
							<?=md5(microtime());?>
						</div>
					<? } ?>
					</div>
					<div class="col" style="left: 100px; position: absolute; 100px; height: 100%; float: left;  border-right: 1px solid black;">
					<? for ( $i=0; $i<5000; $i++) { ?>
						<div class="cell" style="overflow: hidden;  width: 100px; height: 25px; border-bottom: 1px solid black"> 
							<?=md5(microtime());?>
						</div>
						<? } ?>
					</div>
					<div class="col" style="left: 200px; position: absolute; 200px; height: 100%; top: float: left;  border-right: 1px solid black;">
					<? for ( $i=0; $i<5000; $i++) { ?>
						<div class="cell" style="overflow: hidden; width: 200px; height: 25px; border-bottom: 1px solid black"> 
							<?=md5(microtime());?>
						</div>
						<? } ?>
					</div>
					<div class="col" style="left: 400px; position: absolute; 100px; height: 100%; float: left;  border-right: 1px solid black;">
					<? for ( $i=0; $i<5000; $i++) { ?>
						<div class="cell" style="overflow: hidden;  width: 100px; height: 25px; border-bottom: 1px solid black"> 
							<?=md5(microtime());?>
						</div>
						<? } ?>
					</div>

				</div> 
            </DIV>
            <div class="ui-layout-west quickbar" id="quickbar" style="display: none;">
                <div class="accordion basic">
                    <a href="#">Kunden</a>
                    <div>
                    	xxxxxx
                    <!--    <div style="width: autp; border-right: 1px solid gray; text-align: center; padding-top: 15px; position: absolute; border-bottom: 1px solid red; top: 0px; left: 0px; right: 0px; height: 70px; overflow: hidden; background-color: white;">
                            Suche<input style="width: 75%; margin: auto; display: block;"/>
                        </div>-->
                        <div class="searchme" style="position: absolute; left:0px; top:86px; bottom:30px; overflow: auto; width: 100%; height: auto; background-color: gray; border: 1px solid gray; ">
                            <div style="padding: 5px;background-color: blue; border-bottom: 1px solid red; margin-top: 1px; position: relative;">
                                Kanalbau Schneider
                            </div>
                            <div class="searchable" style="padding: 5px;background-color: yellow; border-bottom: 1px solid red; margin-top: 1px; position: relative;">
                                <img src="/css/theme/icons/person.png" style="position: absolute; margin: auto; top: 5px;"/>
                                <div style="margin-left: 32px;">
                                    <b>Wohever,</b>
                                    <div style="position: absolute; right: 0px; top: 5px; float: left; padding-right: 5px;">
                                        Kanalbau AG.
                                    </div>
                                </div>
                                <div style="margin-left: 32px;">
                                    John
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="#">Kunden2</a>
                    <div>
                        x
                    </div>
                </div>
                <!--<div style="position: absolute; width: 240px; padding: 5px; bottom: 0px; left: 0px; background-color: white; border-top: 1px solid red; margin-top: 1px;  height: 20px;">
                    END
                </div>-->
            </div>
        </body>
        </html>
