<?php

class blog{

    private $czyZalogowany=0;
    
    private $polaczenie;
    
    private $komunikat='';
    
    
    
    
    
    
    
    
    
    function logowanie(){
        
        
        
        
        
        
        
        
        session_start();
        if (isset($_SESSION['user']))
        $this->czyZalogowany=1;
        if (isset($_POST['user']) && isset($_POST['pass']))
        
        if ($_POST['user']=='admin' && $_POST['pass']=='123') {
        $_SESSION['user']=$_POST['user'];
        $this->czyZalogowany=1;
        }
        if (isset($_POST['wyloguj'])) {
        session_unset();
        session_destroy();
        $this->czyZalogowany=0;
            }
            }
    
    
    
    
    
    function nawiazPolaczenie(){
        $this->polaczenie = new mysqli('localhost','root','','m');
        mysqli_set_charset($this->polaczenie,'utf8');
    }
        

    
    
    
    
        
    
    
    function zapiszDodawanie($tytul, $post){
        
        
        
        
        
        
        
        
        $zapytanie = "SELECT * FROM blog WHERE tytul='$tytul' AND post='$post'";
        $wynik_zapytania=$this->polaczenie->query($zapytanie);
        if (mysqli_fetch_object($wynik_zapytania)==null){
        $zapytanie = "INSERT INTO blog (tytul, post) VALUES ('$tytul', '$post')";
        $wynik_zapytania=$this->polaczenie->query($zapytanie);
$this->komunikat = '<h3 class="OKi">Rekord dodany</h3>';
            }
            else
            
            $this->komunikat = '<h3 class="nieOKi">Taki rekord już istnieje</h3>';
}

    
    
    
    
    function zapiszPoprawe($id, $tytul, $post){
        
        
        
        
        
        
        
        
        $zapytanie = "UPDATE blog SET tytul='$tytul', post='$post' WHERE id=$id";       
$wynik_zapytania=$this->polaczenie->query($zapytanie);         
$this->komunikat = '<h3 class="OKi">Rekord poprawiony</h3>';

        
    }
    
    
    
    
    
    
    
    
    function usunRekord($id){
        
        
        
        
        
        
        
        $zapytanie = "DELETE FROM blog WHERE id=$id";       
$wynik_zapytania=$this->polaczenie->query($zapytanie);         
$this->komunikat = '<h3 class="OKi">Rekord usunięty</h3>';

    }
    
    
    
    
    
    
    
    
    
    
    
    
    function wyswietlTabele(){
        
        $zapytanie='SELECT * FROM blog ORDER BY tytul, post';      
$wynik_zapytania=$this->polaczenie->query($zapytanie);        
$tmp = '<h2> Strona Rammstein </h2><table>';
if ($this->czyZalogowany==1)      
         $tmp .= "<tr><th>Nr</th><th>tytul</th><th>post</th><th></th><th>!</th></tr>";
    else
        $tmp .= "<tr><th>Nr</th><th>tytul</th><th>post</th></tr>";
     $i=1;                      
     while($obiekt=mysqli_fetch_object($wynik_zapytania)){
         $tmp .= "<tr><td>$i</td><td>$obiekt->tytul</td><td>$obiekt->post</td>";
        if ($this->czyZalogowany==1)    
$tmp .= "<td><a href='blog.php?edytuj=$obiekt->id'>Edytuj</a></td><td>
<a href='blog.php?usun=$obiekt->id'>Usuń</a></td></tr>";
          $i++;
     }
     return $tmp .'</table>';

   
        
    }
    
    
    
    
    
    
    
    
    function wyswietlFormularzDodaj(){
        
        
        
        
    
        
        
        
        
        $tmp = "<h2>Dodawanie rekordów</h2>";
        $tmp .= "<form action='blog.php' method='post'>
							<input name='tytul' placeholder='tytuł'>
							<p>
							<textarea name='post' placeholder='treść artykułu'></textarea></p>
							<input type='submit' value='dodaj'>
						</form>";
        return $tmp;
        
    }
    
    
    
    
        
        private function wyswietlFormularzPopraw($id) {
					$r = $this->polaczenie->query("SELECT * FROM blog WHERE id=".$id);
            
					if($obj = mysqli_fetch_object($r))
					   echo "
					
						<form action='blog.php' method='post'>
							tytuł: <input name='id' value='".$id."' type='hidden'>
							<input name='tytul' value='".$obj->tytul."'>
							<p><textarea name='post'>$obj->post</textarea></p>
							<input type='submit' value='Edytuj'>
						</form>
					   ";
				}
        
        
        
        
        
        
        public
    function wyswietlLogowanie(){
        
        
        
        
        
        
        
        
        
        $tmp = "<form action='blog.php' method='POST' class='login'>";
                                
                                if (!$this->czyZalogowany) {             
        $tmp .= "Użytkownik: <input name='user'>";
     $tmp .= "Hasło: <input type='password' name='pass'>";
     $tmp .= "<input type='submit' value='Zaloguj'>";
} 
        
        else { 
            
    $tmp .= 'Zalogowany: '.$_SESSION['user'];
        $tmp .= "<input type='hidden' name='wyloguj' value='1'>";
$tmp .= "<input type='submit' value='Wyloguj'>";

        }

                                
                        $tmp .= "</form>";                        
    return $tmp;
        
                                
                                
                                
                                
                                
                                
                                }
    
    
    
    
    
    
    
    
    
    function wyswietlStrone(){
        
        
        
        
        
        
        
        
        
        
        $tmp = "";
                if($this->czyZalogowany==1) {
                        if(isset($_GET['edytuj']))        
                                $tmp .= $this->wyswietlFormularzPopraw($_GET['edytuj']);
                        else
                            $tmp .= $this->wyswietlFormularzDodaj();
                }
    
        $tmp .= $this->wyswietlTabele();
        $tmp .= $this->komunikat;
        return $tmp;
        


 
        
    }
    
    
    
    
    
    
    
    
    
    
    
    
    function __construct(){
        
        
        
        
        
        
        
        
        
        
        
        
        
        $this->nawiazPolaczenie();    
        $this->logowanie();           
if ($this->czyZalogowany==1) {

if(isset($_GET['usun']))      
    $this->usunRekord($_GET['usun']);
       

    if(isset($_POST['tytul']) && isset($_POST['post'])) 
if ($_POST['tytul']=='' && $_POST['post']=='')    
   $this->komunikat = '<h3 class="nieOKi">Przesłano pusty formularz</h3>';
else {
    if(!isset($_POST['id'])) 
$this->zapiszDodawanie($_POST['tytul'], $_POST['post']);
   else     
$this->zapiszPoprawe($_POST['id'], $_POST['tytul'], $_POST['post']);
    
}

}

        
        
        
        
    }
    
    
    



}
$blog = new blog("m");
?>
<html>
<head>
<meta charset='utf-8'>
<style>

    
    
    
     * {font-size:15px;}
        h1 a {font-size:20px; text-decoration: none; color:navy;}
        h2 {font-size: 17px;}
        .OKi {color:green;}
        .nieOKi {color:red;}
        th, td, input {border:0; padding:0 10px;
border-left:1px solid grey;
border-bottom:1px solid black;  
        } 
        .login {position: absolute; top:10px; right:10px;font-size:80%;}
        .login input {width:80px;font-size:90%;}

    
    

</style>
</head>
<body>
<?php echo $blog->wyswietlLogowanie(); ?>
    <h1><a href=blog.php></a></h1>
<?php echo $blog->wyswietlStrone(); ?>

</body>
</html>
