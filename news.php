<html>
    <head>
        <meta charset='utf-8'>
		<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
		<link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-black.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		
		<style>
			/*--podswietla sie na jasno szary, gdy się nakieruje kursor na niego*/
			.link:hover {
				background-color: rgb(230,230,230);
			}
			
			/*--Podkreslenie tytulu--*/
			span.podkreslenie {
				margin-left: 25%;
				font-weight: 800;
			}
			
			form {
				margin-left: 6%;
				margin-top: 3%;
			}
			
			 textarea.poleEdycjiTekstu{
				width: 600px;
				height: 700px;
				z-index: 2;
			}
			
			textarea.poleDodawania{
				width: 700px;
				height: 400px;
			}
			
			.content {
				padding: 3% 40px 30px 40px;
				width: 1000px;
				margin: 0 0 0 25%;
				color: black;
				background-color: silver;
			}
			
			#background {
				background-size: 100% 300px;
				width: 100%;
				height: 450px;
			}
		</style>
    </head>
    <body id='myPage'> 
		
        <?php
            class Blog {
				/*
					Konstruktor, laczy z baza danych
					parametry:
						*nazwaBazy - nazwa bazy danych z ktora sie ma polaczyc np pawel
						*czyZalogowany - zawiera status, czy uzytkownik jest zalogowany
				*/
                public function __construct($nazwyBazy, $czyZalogowany) {
					// polacz sie z baza danych
                    $this->polacz($nazwyBazy);
					// ustaw nazwe tabeli, w ktorej sa informacje z bloga
                    $this->nazwaBazy_ = "artykuly";
					// ustaw status zalogowania na wartosc wpisana przy tworzeniu obiektu
                    $this->czyZalogowany_ = $czyZalogowany;
                }
                public function pokazArtykuly() {
					$informacjaoZalogowaniu = "";
					// jezeli jest zalogowany, to domyslnie jest to admin
					$login = "admin";
					if($this->czyZalogowany_ == 1)
							$informacjaoZalogowaniu = "Zalogowany jako $login";
					else 
						$informacjaoZalogowaniu = "Nie zalogowany użytkownik";
					
					// sprawdzenie, czy nie ma wyswietlic formularza do edycji artykulu
					if(!isset($_GET['idEdytowanego'])) {
						echo "<div class='w3-top' style='position: sticky'>
							<div class='w3-bar w3-theme-d2 w3-left-align' style='padding-left: 20px; opacity: 0.8'>
								<a href='news.php' class='w3-bar-item w3-button w3-hide-small w3-hover-white'>Wiadomości</a>
								<a href='news.php#tag' class='w3-bar-item w3-button w3-hide-small w3-hover-white'>Informacje</a>
								<span style='padding-left: 43%; font-weight: 700; margin-left: 25%;'>$informacjaoZalogowaniu</span>
							</div></div>
							<img src='background.jpg' id='background'>
							<div class='content'><h1>Witam na moim blogu !</h1>";
							// jezeli zalogowany i nie jest to formularz do edytowania to wyswietl dodawanie
						if($this->czyZalogowany_ == 1) {
							$this->pokazFormularzDoDodawania();
						}
						// wyciagnij wszystko z tabeli o nazwie nazwaTabeli
						$r = $this->polaczenie_->query("SELECT * FROM ". $this->nazwaBazy_);
						
						// dopoki sa jakies artykuly to wyswietlaj
						while($obj = mysqli_fetch_object($r)) {
							// tytul
							echo "
									<span class='podkreslenie'>$obj->tytul  </span>";
							// jezeli zalogowany to opcje edycji i usuwania
							if($this->czyZalogowany_ == 1) {
									echo "<a href='news.php?idEdytowanego=".$obj->id."' class='link'>Edytuj</a>
										<a href='news.php?idUsuwanego=".$obj->id."' class='link'>Usuń</a><br>";
							}
							// oraz tresc
							echo "<br>$obj->informacja<br>";
						}	
					}
					else {
						// jezeli edytujemy artykul, to wyswietl formularz do edytowania
						echo "<div class='w3-top'><div class='w3-bar w3-theme-d2 w3-left-align'><a href='news.php' class='w3-bar-item w3-button w3-hide-small w3-hover-white' >powrót do strony głównej</a></div>";
						$this->edytuj();
					}
					
					// wywolaj metody klasy
					$this->usun();
					$this->dodaj();
					
					// jezeli jest edytowany artykul to zastap jego tresc
					if(isset($_POST['idArtykulu'])) {
						$this->edytuj();
					}
					echo "</div>";
					
				}
				private function edytuj() {
					// jezeli jeszcze nie edytowales artykulu to wyswietl formularz
					if(!isset($_POST['idArtykulu'])) {
						$this->wyswietlFormularzDoEdycji();
					}
					// w przeciwnym wypadku zastap artykul edytowany artykulem edytowanym
					else {
						$r = $this->polaczenie_->query("UPDATE $this->nazwaBazy_ SET tytul='".$_POST['tytul']."', informacja='".$_POST['tresc']."' WHERE id=".$_POST['idArtykulu']);
					}
				}
				private function wyswietlFormularzDoEdycji() {
					// wyslij zapytanie o takich artykulach, w ktorym id jest rowne id artykulu edytowanego
					$r = $this->polaczenie_->query("SELECT * FROM ". $this->nazwaBazy_." WHERE id=".$_GET['idEdytowanego']);
					$obj = mysqli_fetch_object($r);
					// wyswietl formularz
					echo "
						
						<form action='news.php' method='post'>
							tytuł: <input name='idArtykulu' value=".$_GET['idEdytowanego']." type='hidden'>
							<input name='tytul' value='".$obj->tytul."'>
							<p><textarea name='tresc' class='poleEdycjiTekstu'>$obj->informacja</textarea></p>
							<input type='submit' value='Edytuj'>
						</form>
					";
				}
				private function usun() {
					// usun artykul o takim id
					if(isset($_GET['idUsuwanego'])) {
						$r = $this->polaczenie_->query("DELETE FROM ". $this->nazwaBazy_." WHERE id=".$_GET['idUsuwanego']);
					}
				}
                private function polacz($nazwyBazy) {
                    // nawiazuje polaczenie
                    $this->polaczenie_ = new mysqli("localhost", "root", "", $nazwyBazy);
                }
				private function dodaj() {
					// sprawdza, czy dane sa tresc i tytul i nie jest ten artykul edytowany
                    // za pomoca metody get
                    if(isset($_POST['tresc']) && isset($_POST['tytul']) && !isset($_POST['idArtykulu'])) {
                        if($_POST['tresc'] != '' && $_POST['tytul'] != '') {
                            // wybranie w celu nie wpisania tego
                            // samego rekordu
                            $q = "SELECT * FROM ".$this->nazwaBazy_." WHERE tytul='".$_POST['tytul']."'";
                            $r = $this->polaczenie_->query($q);
                            // nie ma zastrzezenia do przypisania
                            if(($obj = mysqli_fetch_object($r)) == null) {
                                $q = "INSERT INTO ".$this->nazwaBazy_."(tytul, informacja) VALUES('".$_POST['tytul']."', '".$_POST['tresc']."')";
                                $r = $this->polaczenie_->query($q);
                            }
                        }
                    } 
				}
				private function pokazFormularzDoDodawania() {
					echo "
						<form action='news.php' method='post'>
							<input name='tytul' placeholder='tytuł'>
							<p>
							<textarea name='tresc' placeholder='treść artykułu' class='poleDodawania'></textarea></p>
							<input type='submit' value='dodaj'>
						</form>
						<div class='margines'></div>";
				}
                private $polaczenie_;
                private $czyZalogowany_;
                private $nazwaBazy_;
            }
            ?>
        <?php
            $blog = new Blog("mine", 1);
            $blog->pokazArtykuly();
			$margin = "15px";
			// jezeli nie jestes na stronie do edytowania to wyswietl stopke
			if(!isset($_GET['idEdytowanego']))
				echo "<a id='tag'></a><footer class='w3-container w3-padding-32 w3-theme-d1 w3-center' style='margin-top: $margin; z-index: 8; position: relative; bottom: 0px'>
					<h4>Kontakt: </h4>
					email: blablabla@gmail.com
					<p>telefon: 2454456 </p>
					<p>Szablon powstał przy pomocy <a href='https://www.w3schools.com/w3css/default.asp'>w3.css</a></p>
					</footer>";
        ?>
		
		
    </body>
</html>