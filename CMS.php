<html>
    <head>
        <meta charset='utf-8'>
        <link href='style.css' rel='stylesheet'>
    </head>
    <body>
        <h1><a href='CMS.php' class="header">CMS</a></h1>
        <?php
            class Cms {
                public function __construct() {
                    $this->connect();
                }
                public function showTable() {          
                    echo "<table>";
                    echo "
                            <tr>
                                <td> nr </td>
                                <td> imie </td>
                                <td> nazwisko </td>
                            </tr>
                        ";
                    
                    $r = $this->connection_->query("SELECT * FROM osoby");
                    $number = 1;
                    while($obj = mysqli_fetch_object($r)) {
                        echo "
                            <tr>
                                <td>$number</td>
                                <td>$obj->imie</td>
                                <td>$obj->nazwisko</td>
                                <td><a href='cms.php?edit=".$obj->id."'>Edytuj</a></td>
                                <td><a href='cms.php?delete=".$obj->id."'>Usuń</a></td>
                            </tr>
                        ";
                        $number++;
                    }
                    echo "</table>";
                    // przekieruj sterowanie w zaleznosci od tego
                    // czy ma usunac czy edytowac czy dodac
                    $this->showForm();
                    $this->addRecord();
                    $this->editRecord();
                    $this->deleteRecord();
                }
                private function connect() {
                    // nawiazuje polaczenie
                    $this->connection_ = new mysqli("localhost", "root", "", "mine");
                }
                private function showForm() {
                    // pokazuje formularz
                    // jezeli wybrano edytuj to wyswietli
                    // formularz do edytowania
                    // w przeciwnym(domyslnym) wypadku napisze
                    // formularz do dodawania
                    if(isset($_GET['edit'])) {
                        $q = "SELECT * FROM osoby WHERE id=".$_GET['edit'];
                        $r = $this->connection_->query($q);
                        $obj = mysqli_fetch_object($r);
                        echo "<form action='cms.php' method='get'>
                                <input name='name' value='".$obj->imie."'>
                                <input name='surname' value='".$obj->nazwisko."'>
                                <input name='edit' type='hidden' value=".$_GET['edit'].">
                                <input type='submit' value='Edytuj'>
                            </form>
                        ";
                    }
                    else {
                         
                            echo "<form action='cms.php' method='get'>
                                <input name='name' placeholder='imię'>
                                <input name='surname' placeholder='nazwisko'>
                                <input type='submit' value='Dodaj !'>
                            </form>
                        ";
                    }
                }
                private function addRecord() {
                    // sprawdza, czy powstaly trzy zmienne
                    // za pomoca metody get
                    if(isset($_GET['name']) && isset($_GET['surname']) && !(isset($_GET['edit']))) {
                        if($_GET['name'] != '' && $_GET['surname'] != '') {
                            // wybranie w celu nie wpisania tego
                            // samego rekordu
                            $q = "SELECT * FROM osoby WHERE imie='".$_GET['name']."' AND nazwisko='".$_GET['surname']."'";
                            $r = $this->connection_->query($q);
                            // nie ma zastrzezenia do przypisania
                            if(($obj = mysqli_fetch_object($r)) == null) {
                                $q = "INSERT INTO osoby(imie, nazwisko) VALUES('".$_GET['name']."', '".$_GET['surname']."')";
                                $r = $this->connection_->query($q);
                                
                                // wyswietlanie komunikatow
                                echo "<span class='ok'>Dodano rekord !</span>";
                            }
                            else {
                                echo "<span class='error'>taki rekord już istnieje !</span>";
                            }
                        }
                        else {
                            echo "<span class='error'>nie dodam pustego rekordu !</span>";
                        }
                    } 
                }
                private function editRecord() {
                    if(isset($_GET['edit'])) {
                        if(isset($_GET['name']) && isset($_GET['surname'])) {
                            $q = "UPDATE osoby SET imie='".$_GET['name']."', nazwisko='".$_GET['surname']."' WHERE id=".$_GET['edit'];
                            $r = $this->connection_->query($q);
                            echo "<span class='ok'>Zmieniono rekord !</span>";
                        }
                    }
                }
                private function deleteRecord() {
                    if(isset($_GET['delete'])) {      
                            $q = "DELETE FROM osoby WHERE id=".$_GET['delete'];
                            $r = $this->connection_->query($q);
                            echo "<span class='ok'>Usunięto rekord!</span>";
                    }
                }
                private $connection_;
            }
            ?>
        <?php
            $cms = new Cms();
            $cms->showTable();
        ?>
    </body>
</html>