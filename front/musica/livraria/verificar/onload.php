<?php

Core::includeBack('Musicalivraria');

$livraria = [];
Musicalivraria::verificar($livraria);
$data['msg'] = $livraria['msg'];