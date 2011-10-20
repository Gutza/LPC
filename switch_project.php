<?php

LPC_Project::setCurrent();
header("Location: ".$_GET['return_to']);
exit;
