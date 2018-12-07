<?php

function is_secure_mdp($mdp)
{
	if (strlen($mdp) < 8)
		return FALSE;
	$digit = 0;
	$i = 0;
	while ($i < strlen($mdp))
	{
		if (is_numeric($mdp[$i]))
			$digit += 1;
		$i += 1;
	}
	$i = 0;
	$alpha = 0;
	while ($i < strlen($mdp))
	{
		if (ctype_alpha($mdp[$i]))
			$alpha += 1;
		$i += 1;
	}
	if (!$digit || !$alpha)
		return FALSE;
	return TRUE;
}
?>
