<html>

<head>
    <meta charset='utf-8'>
    <link href='style.css' rel='stylesheet'>
</head>

<body>
    <h1><a href='CMS.php' class="header">CMS</a></h1>
    <?php
    class Cms
    {
        public function __construct()
        {
            $this->Connection = new mysqli("localhost", "root", "", "mine");
            $this->recordsCount = 0;

            if ($this->MustAddRecord()) {
                $this->TryAddRecord();
            }

            if (isset($_GET['edit'])) {
                $this->EditRecord();
            }

            if (isset($_GET['delete'])) {
                $this->DeleteRecord();
            }
        }

        public function ShowTable()
        {
            echo "<table>";
            $this->ShowHeaders();

            $r = $this->Connection->query("SELECT * FROM osoby");
            $this->recordsCount = 0;

            while ($obj = mysqli_fetch_object($r)) {
                $this->AddNewPersonField($obj);
            }

            echo "</table>";

            // przekieruj sterowanie w zaleznosci od tego
            // czy ma usunac czy edytowac czy dodac
            if (isset($_GET['edit'])) {
                $this->ShowEditForm();
            } else {
                echo "<form action='cms.php' method='get'>
                                <input name='name' placeholder='imię'>
                                <input name='surname' placeholder='nazwisko'>
                                <input type='submit' value='Dodaj !'>
                            </form>";
            }
        }

        private function ShowHeaders()
        {
            echo "
                            <tr>
                                <td> nr </td>
                                <td> imie </td>
                                <td> nazwisko </td>
                            </tr>
                        ";
        }

        private function AddNewPersonField($obj)
        {
            echo "
                    <tr>
                        <td>$this->recordsCount</td>
                        <td>$obj->Name</td>
                        <td>$obj->Surname</td>
                        <td><a href='cms.php?edit=" . $obj->Id . "'>Edytuj</a></td>
                        <td><a href='cms.php?delete=" . $obj->Id . "'>Usuń</a></td>
                    </tr>
                    ";
            $this->recordsCount++;
        }

        private function ShowEditForm()
        {
            $q = "SELECT * FROM osoby WHERE Id=" . $_GET['edit'];
            $r = $this->Connection->query($q);
            $obj = mysqli_fetch_object($r);
            echo "<form action='cms.php' method='get'>
                            <input name='name' value='" . $obj->Name . "'>
                            <input name='surname' value='" . $obj->Surname . "'>
                            <input name='edit' type='hidden' value=" . $_GET['edit'] . ">
                            <input type='submit' value='Edytuj'>
                        </form>
                    ";
        }

        private function MustAddRecord()
        {
            // sprawdza, czy powstaly trzy zmienne
            // za pomoca metody get
            return isset($_GET['name']) && isset($_GET['surname']) && !(isset($_GET['edit']));
        }

        private function TryAddRecord()
        {
            if ($this->WasEnteredEmptyRecord()) {
                $this->AddRecord();
            } else {
                echo "<span class='error'>Wykryto pusty rekord !</span>";
            }
        }

        private function AddRecord()
        {
            // wybranie w celu nie wpisania tego
            // samego rekordu
            $q = "SELECT * FROM osoby WHERE Name='" . $_GET['name'] . "' AND Surname='" . $_GET['surname'] . "'";
            $r = $this->Connection->query($q);

            $isSameRecordAvailableInDatabase = mysqli_fetch_object($r) != null;

            if (!$isSameRecordAvailableInDatabase) {
                $q = "INSERT INTO osoby(Name, Surname) VALUES('" . $_GET['name'] . "', '" . $_GET['surname'] . "')";
                $r = $this->Connection->query($q);

                // wyswietlanie komunikatow
                echo "<span class='ok'>Dodano rekord !</span>";
            } else {
                echo "<span class='error'>taki rekord już istnieje !</span>";
            }
        }

        private function WasEnteredEmptyRecord()
        {
            return $_GET['name'] != '' && $_GET['surname'] != '';
        }

        private function EditRecord()
        {
            if (isset($_GET['name']) && isset($_GET['surname'])) {
                $query = "UPDATE osoby SET Name='" . $_GET['name'] . "', Surname='" . $_GET['surname'] . "' WHERE Id=" . $_GET['edit'];
                $this->Connection->query($query);
                echo "<span class='ok'>Zmieniono rekord !</span>";
            }
        }

        private function DeleteRecord()
        {
            $q = "DELETE FROM osoby WHERE Id=" . $_GET['delete'];
            $this->Connection->query($q);
            echo "<span class='ok'>Usunięto rekord!</span>";
        }

        private $Connection;
    }
    ?>
    <?php
    $cms = new Cms();
    $cms->ShowTable();
    ?>
</body>

</html>