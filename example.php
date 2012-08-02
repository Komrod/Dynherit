<?php

/*************************
  Dynherit test
  ************************
  Copyright (c) 2003-2012 Komrod Dev Team

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License version 3
  as published by the Free Software Foundation.

**********************************************/


// include the Dynherit class
include('dynherit.php');


// Classes from parent to last child, with file and class name
$classes = array(
	array('file'=>'class_base.php', 'name'=>'class_base'),
	array('file'=>'class_a.php', 'name'=>'class_a'),
	array('file'=>'class_b.php', 'name'=>'class_b'),
	);


///////////////////////////////////// Testing Dynherit ///////////////////////////////////////

echo("<h1>Testing Dynherit</h1>");


// get the instance from Dynherit
$instance = dynherit::exec($classes);

echo("Function call_all: <br/>\n");
$instance->call_all('cache');
echo("<br/>\n<br/>\n<br/>\n");
	
echo("Function echo_all: <br/>\n");
$instance->echo_all();
echo("<br/>\n<br/>\n<br/>\n");
	


die();


