<?php

getPage("/db/sql.php");

class User
{
    private $id;
    private $username;
    private $name;
    private $password;
    private $surname;
    private $email;
    private $vpnPass;
    private $region;
    private $gender = 'NULL';
    private $isAdmin = false ;

    public function __construct($args=[])
    {
        $this->id = $args['id'];
        $this->username = $args['username'];
        $this->password = $args['password'];
        $this->name = $args['name'];
        $this->surname = $args['surname'];
        $this->email = $args['email'];
        $this->email = $args['vpnPass'];
        $this->email = $args['region'];
        $this->isAdmin = $args['isAdmin'];
        //$this->gender = $args['gender'];
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }



    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param mixed $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender(string $gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return mixed
     */
    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * @param mixed $isAdmin
     */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }

    /**
     * @return mixed
     */
    public function getVpnPass()
    {
        return $this->vpnPass;
    }

    /**
     * @param mixed $vpnPass
     */
    public function setVpnPass($vpnPass): void
    {
        $this->vpnPass = $vpnPass;
    }

    /**
     * @return mixed
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param mixed $region
     */
    public function setRegion($region): void
    {
        $this->region = $region;
    }



    static protected $userColumns = ['id', 'name', 'surname', 'email', 'username', 'password', 'vpnPass', 'region', 'isAdmin'];

        // Properties which have database columns, excluding ID
        public function attributes() {
            $attributes = [];


            foreach(self::$userColumns as $column) {
                if($column == 'id') { continue; }
                $attributes[$column] = $this->$column;
            }
            return $attributes;
        }

//Set attributes as an array

    function is_post_request() {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    public function addToDb()
    {
        $db = new DataBase();
        $database = $db->dbConnection();
        $irDatabase = $db->irDbConnection();

        // setting the sql string using the attributes and inserting them as parameters to the sql string
        $attributes = $this->attributes();
        $sql = "INSERT INTO users (";
        $sql .= join(', ', array_keys($attributes));
        $sql .= ") VALUES ('";
        $sql .= join("', '", array_values($attributes));
        $sql .= "')";

        //applying the sql string to the database
        $database->query($sql);
        $irDatabase->query($sql);
        return true;
    } // END OF ADD TO DB



    // READ FROM DB
    public function getdBPassword(){

        $db = new DataBase();
        $database = $db->dbConnection();

        $sql = "SELECT * FROM users Where username=:username";

        // select a particular user by id
        $stmt = $database->prepare($sql);
        $stmt->execute(['username' => $this->username]);
        $user = $stmt->fetch();
        $dbPassword = $user['password'];

        $checkPassword = password_verify($this->password,$dbPassword);

        return $checkPassword;
    }

    public function getDbName(){
        $db = new DataBase();
        $database = $db->dbConnection();

        $sql = "SELECT * FROM users Where username=?";
        $query= $database->prepare($sql);
        $query->bindValue(1,$this->username);
        $result = $query->execute();

        $fetchedArray = $query->fetch(PDO::FETCH_ASSOC);


        $rowName = $fetchedArray['name'];
        $rowAdmin = $fetchedArray['isAdmin'];

        $this->setName($rowName);
        $this->setIsAdmin($rowAdmin);
    }


    public function getDbTable()
    {
        $db = new DataBase();
        $database = $db->dbConnection();

        $sql = "SELECT * FROM users";
        $query = $database->prepare($sql);
        $result = $query->execute();
        $row = $query->fetchAll(PDO::FETCH_ASSOC);
        $rowCount = $query->rowCount();



        foreach (self::$userColumns as $values) {
            if ($values == "password") continue;
            echo "<th>" . $values . "</th>";
        }

        $columns = self::$userColumns;

        for ($i = 0; $i < $rowCount; $i++) {
            echo '<tr>';
            foreach ($columns as $columnValue) {
                if ($columnValue == "password") continue;
                if ($columnValue == "username") {
                    $link = '<td><a href="';
                    $link .= '/pages/profile.php?id=' . $row[$i]['id'];
                    $link .= '">'. $row[$i][$columnValue] . '</a></td>';
                    echo $link;
                } // End of First iF
                else{
                        echo '<td>' . $row[$i][$columnValue] . '</td>';
                    }

            }
        echo '</tr>';
        } // END of FOR loop
        $query->closeCursor();
    } //End of function


    // Retrieve any data from DB table users
    public function getDbData($theId){
        $db = new DataBase();
        $database = $db->dbConnection();

        $sql = "SELECT * FROM users Where id=?";
        $query= $database->prepare($sql);
        $query->bindValue(1,$theId);
        $result= $query->execute();

        $fetchedArray = $query->fetch(PDO::FETCH_ASSOC);

        $this->setUsername($fetchedArray['username']);
        $this->setName($fetchedArray['name']);
        $this->setSurname($fetchedArray['surname']);
        $this->setEmail($fetchedArray['email']);
        $this->setRegion($fetchedArray['region']);
        $this->setIsAdmin($fetchedArray['isAdmin']);

    }

    public function updateDbData($id)
    {

        $db = new DataBase();
        $database = $db->dbConnection();
        $irDatabase = $db->dbConnection();



        $sql = "UPDATE users SET username = COALESCE(NULLIF(?, ''),username)";
        $sql .= ", name = COALESCE(NULLIF(?, ''),name)";
        $sql .= ", surname = COALESCE(NULLIF(?, ''),surname)";
        $sql .= ", email = COALESCE(NULLIF(?, ''),email)";
        $sql .= ", password = COALESCE(NULLIF(?, ''),password)";
        $sql .= ", vpnPass = COALESCE(NULLIF(?, ''),vpnPass)";
        $sql .= ", region = COALESCE(NULLIF(?, ''),region)";
        $sql .= ", isAdmin = ? WHERE id = ?";

        $query= $database->prepare($sql);
        $irQuery= $irDatabase->prepare($sql);

        $irQuery->bindParam(1,$this->username, PDO::PARAM_STR, 12);
        $irQuery->bindParam(2,$this->name, PDO::PARAM_STR, 12);
        $irQuery->bindParam(3,$this->surname, PDO::PARAM_STR, 12);
        $irQuery->bindParam(4,$this->email, PDO::PARAM_STR, 12);
        $irQuery->bindParam(5,$this->password, PDO::PARAM_STR, 12);
        $irQuery->bindParam(6,$this->vpnPass, PDO::PARAM_STR, 12);
        $irQuery->bindParam(7,$this->region, PDO::PARAM_STR, 12);
        $irQuery->bindParam(8,$this->isAdmin, PDO::PARAM_INT);
        $irQuery->bindParam(9,$id, PDO::PARAM_INT);

        $query->bindParam(1,$this->username, PDO::PARAM_STR, 12);
        $query->bindParam(2,$this->name, PDO::PARAM_STR, 12);
        $query->bindParam(3,$this->surname, PDO::PARAM_STR, 12);
        $query->bindParam(4,$this->email, PDO::PARAM_STR, 12);
        $query->bindParam(5,$this->password, PDO::PARAM_STR, 12);
        $query->bindParam(6,$this->vpnPass, PDO::PARAM_STR, 12);
        $query->bindParam(7,$this->region, PDO::PARAM_STR, 12);
        $query->bindParam(8,$this->isAdmin, PDO::PARAM_INT);
        $query->bindParam(9,$id, PDO::PARAM_INT);

        $query->execute();
        $query->closeCursor();

        $irQuery->execute();
        $irQuery->closeCursor();
        return TRUE;
    }


}
