<?php
ini_set("log_errors", 1);
ini_set("error_log", "php-error.log");
putenv('GDFONTPATH=' . realpath('.'));

function MakeCart()
{
	$rows = GeneraCartella(); //Genero le righe della cartella
	$im     = imagecreate ( 490 , 190 );	//Inizializzo l'oggetto immagine (larghezza, altezza)
	$white = imagecolorallocate($im, 255, 255, 255);	//Inizializzo il colore bianco (per lo sfondo)
	$black = imagecolorallocate ( $im , 0 , 0 , 0 );	//Inizializzo il colore nero (per le linee e i numeri)

	imagefill($im, 0, 0, $white);	//Riempio l'immagine con il colore di sfondo

	imagesetthickness ($im, 2 );	//Imposto lo spessore delle linee
	//Disegno le linee orizzontali
	for($i=1; $i<=2; $i++)
	{
		imageline($im, 20, (50*$i)+20, 470, (50*$i)+20, $black);
	}
	//Disegno le linee verticali
	for($i=1; $i<=8; $i++)
	{
		imageline($im, (50*$i)+20, 20, (50*$i)+20, 170, $black);
	}
	for($i=0;$i<count($rows);$i++)	//Ciclo su ogni riga della tabella
	{
		for($y=0;$y<count($rows[$i]);$y++)	//Ciclo su ogni numero della riga
		{
			$g = GroupCalc($rows[$i][$y]);	//Calcolo in gruppo del numero (che mi dirà in quale colonna andrà messo)
			$xs = ($g * 50) + 20 + ($rows[$i][$y] >= 10 ? 10 : 15);	//Calcolo la posizione orizzontale del numero all'interno dell'immagine (centrandolo nella casella a seconda se è composta da una cifra o due cifre)
			$ys = ($i * 50) + 20 + 33 + $i; //Calcolo la posizione verticale del numero all'interno dell'immagine
			imagettftext( $im , 20 , 0, $xs , $ys , $black, "arialbd.ttf", $rows[$i][$y] );	//Scrivo il numero all'interno dell'immagine nella posizione calcolata prima
		}
	}

	//Mando in output l'immagine generata
	ob_start();
	imagepng($im);
	$imagePng = ob_get_contents();
	ob_end_clean();
	imagedestroy($im);	//distruggo l'oggetto liberando la memoria
	return base64_encode($imagePng);
}

function GeneraCartella()
{
	/*La generazione di una cartella della tombola deve rispettare questi requisiti:
		- 15 numeri per cartella
		- 5 numeri per riga
		- Massimo 3 numeri per colonna (le colonne sono definite come gruppo di numeri che hanno la stessa decina tranne per il 90 che va nella colonna della decina 8)
		- Le colonne devono essere ordinate verticalmente dall'alto verso il basso
	*/
	$cartella = [];			//Array che contiene tutti i numeri per la semplice esclusione di quelli già usciti e il conteggio totale
	$rows = [[],[],[]];		//Cartella suddivisa in 3 righe. Verrà restituito questa variabile dal metodo
	$groups = [];			//Array per la gestione delle colonne in modo da controllare che vengano estratti al massimo 3 numeri per colonna
	while(count($cartella) < 15)	//Ciclo finché non sono stati estratti tutti e 15 i numeri di una cartella
	{
		$n = random_int(1, 90);			//estraggo un numero casuale da 1 a 90
		if(!in_array($n, $cartella))	//Se non è già stato estratto allora posso prenderlo in considerazione
		{
			$group = GroupCalc($n);		//Ne calcolo il "gruppo" (cioè di che colonna della cartella fa parte)
			if(isset($groups[$group])) //Se esistono già numeri per quel gruppo
			{
				if($groups[$group] < 3)	//Se in quel gruppo non ci sono già tre numeri allora posso aggiungerlo
				{
					for($i=0; $i<3; $i++)	//Ciclo su tutte le righe della tabella
					{
						if(count($rows[$i]) < 5)	//Se la riga ha meno di 5 numeri (perché di più non ne può tenere)
						{
							$toadd = true;			//Setto una variabile di controllo a true
							for($y=0; $y<count($rows[$i]); $y++)	//Ciclo sui numeri già presenti nella riga corrente
							{
								$rgrp = GroupCalc($rows[$i][$y]);	//Calcolo il gruppo del numero
								if($rgrp == $group)					//Se il gruppo del numero estratto è uguale a quello di un numero già presente
								{
									$toadd = false;					//Il numero non può essere aggiunto perché per ogni riga ci può essere solo un numero per gruppo
									break;
								}
							}
							if($toadd)					//Se il gruppo del numero non era già presente su questas riga
							{
								$rows[$i][] = $n;		//Aggiungo il numero alla riga
								$cartella[] = $n;		//Aggiungo il numero tra quelli aggiunti alla cartella
								$groups[$group]++;		//Incremento di uno i numeri inseriti per quel gruppo (sempre per il concetto che massimo 3 per gruppo)
								break;					//Concludo il ciclo sulle righe perché il numero è stato inserito
							}
						}
					}					
				}				
			}
			else	//Se numeri di quel gruppo non ne sono mai stati inseriti
			{
				for($i=0; $i<3; $i++)	//Ciclo tutte le righe
				{
					if(count($rows[$i]) < 5)	//La prima con meno di 5 numeri inserisco
					{
						$rows[$i][] = $n;		//Aggiungo il numero alla riga
						$cartella[] = $n;		//Aggiungo il numero tra quelli aggiunti alla cartella
						$groups[$group] = 1;	//Inserisco il gruppo nell'array dei gruppi con lavore 1 (perché il numero è di un gruppo mai inserito prima)
						break;					//Concludo il ciclo sulle righe perché il numero è stato inserito
					}
				}
			}
		}
	}
	sort($rows[0]);	//Metto in ordine le righe, sistemando l'ordinamento orizzontale
	sort($rows[1]);
	sort($rows[2]);
	//Ora sistemo l'oridnamento verticale
	$done = false;	//Variabile di controllo. Devo fermarmi solo quando non ci sono stati scambi da fare
	while(!$done) //Finché ci sono stati scambi da fare
	{
		$done = true;	//Setto a true la variabile. Se non ci saranno scambi rimarrà a true e il ciclo terminerà (stile bubble sort)
		for($r=0;$r<2;$r++) //Ciclo per la prima e la seconda riga
		{
			for($i=0;$i<5;$i++)	//ciclo ogni numero della riga
			{
				$r1 = $rows[$r][$i];	//Metto il numero in una variabile
				$g1 = GroupCalc($r1);	//Ne calcolo il gruppo
				for($y=0;$y<5;$y++)		//Ciclo per ogni numero della riga successiva (quindi, alla fine del ciclo superiore avrò confrontato la prima riga con la seconda e la seconda con la terza)
				{
					$r2 = $rows[$r+1][$y];	//Metto in una variabile il numero
					$g2 = GroupCalc($r2);	//Ne calcolo il gruppo
					if($g1 == $g2)			//Se i due numeri hanno il gruppo uguale devo controllare
					{
						if($r2 < $r1)		//Se il numero della riga successiva è minore della precedente sono da scambiare (per l'ordinamento verticale)
						{
							$rows[$r][$i] = $r2;	//Metto il numero della riga successiva al posto di quello nella riga precedente
							$rows[$r+1][$y] = $r1;	//Metto il numero della riga precedente al posto di quello nella riga successiva
							$done = false;			//Setto la variabile a false per continuare il ciclo while (almeno uno scambio è stato operato)							
						}
						break;					//Interrompo il ciclo sui numeri della riga successiva perché ho già trovato una corrispondenza di gruppo
					}
				}
			}
		}
		// Nel ciclo precedente ho confrontato la prima riga con la seconda e la seconda con la terza. Qui controllo la prima con la terza
		for($i=0;$i<5;$i++)	//Ciclo i numeri della prima riga
		{
			$r1 = $rows[0][$i];	//Metto il numero della prima riga in una variabile
			$g1 = GroupCalc($r1);	//Ne calcolo il gruppo
			for($y=0;$y<5;$y++)		//Ciclo i numeri della terza riga
			{
				$r2 = $rows[2][$y];	//Metto il numero della terza riga in una variabile
				$g2 = GroupCalc($r2);	//Ne calcolo il gruppo
				if($g1 == $g2)			//Se i due numeri hanno il gruppo uguale devo controllare
				{
					if($r2 < $r1)		//Se il numero della terza riga è minore del numero della prima riga sono da scambiare (per l'ordinamento verticale)
					{
						$rows[0][$i] = $r2;	//Metto il numero della terza riga al posto di quello della prima riga
						$rows[2][$y] = $r1;	//Metto il numero della prima riga al posto di quello della terza riga
						$done = false;	//Setto la variabile a false per continuare il ciclo while (almeno uno scambio è stato operato)
					}
					break;					//Interrompo il ciclo sui numeri della terza riga perché ho già trovato una corrispondenza di gruppo
				}
			}
		}
	}
	return $rows;	//Ritorno l'array di tre righe di numeri che rappresenta la cartella
}

function GroupCalc($n)
{
	return ($n == 90 ? 8 : floor($n / 10));
}
?>