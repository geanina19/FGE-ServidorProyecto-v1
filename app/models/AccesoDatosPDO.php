<?php

/*
 * Acceso a datos con BD Usuarios : 
 * Usando la librería PDO *******************
 * Uso el Patrón Singleton :Un único objeto para la clase
 * Constructor privado, y métodos estáticos 
 */
class AccesoDatosPDO
{

    private static $modelo = null;
    private $dbh = null;

    public static function getModelo()
    {
        if (self::$modelo == null) {
            self::$modelo = new AccesoDatosPDO();
        }
        return self::$modelo;
    }



    // Constructor privado  Patron singleton

    private function __construct()
    {
        try {
            $dsn = "mysql:host=" . DB_SERVER . ";dbname=" . DATABASE . ";charset=utf8";
            $this->dbh = new PDO($dsn, DB_USER, DB_PASSWD);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Error de conexión " . $e->getMessage();
            exit();
        }
    }

    // Cierro la conexión anulando todos los objectos relacioanado con la conexión PDO (stmt)
    public static function closeModelo()
    {
        if (self::$modelo != null) {
            $obj = self::$modelo;
            // Cierro la base de datos
            $obj->dbh = null;
            self::$modelo = null; // Borro el objeto.
        }
    }


    // Devuelvo cuantos filas tiene la tabla

    public function numClientes(): int
    {
        $result = $this->dbh->query("SELECT id FROM clientes");
        $num = $result->rowCount();
        return $num;
    }

    public function nombresClientes(): int
    {
        $result = $this->dbh->query("SELECT count(*) FROM clientes");
        $num = $result->rowCount();
        return $num;
    }


    // SELECT Devuelvo la lista de Usuarios
    public function getClientes($primero, $cuantos, $filtro): array
    {
        $tuser = [];
        // Crea la sentencia preparada
        // echo "<h1> $primero : $cuantos  </h1>";
        $stmt_usuarios  = $this->dbh->prepare("select * from clientes " . $filtro . " limit $primero,$cuantos");
        // Si falla termina el programa
        $stmt_usuarios->setFetchMode(PDO::FETCH_CLASS, 'Cliente');

        if ($stmt_usuarios->execute()) {
            while ($user = $stmt_usuarios->fetch()) {
                $tuser[] = $user;
            }
        }
        // Devuelvo el array de objetos
        return $tuser;
    }


    // SELECT Devuelvo un usuario o false
    public function getCliente(int $id)
    {
        $cli = false;
        $stmt_cli   = $this->dbh->prepare("select * from clientes where id=:id");
        $stmt_cli->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
        $stmt_cli->bindParam(':id', $id);
        if ($stmt_cli->execute()) {
            if ($obj = $stmt_cli->fetch()) {
                $cli = $obj;
            }
        }
        return $cli;
    }

    // SELECT Devuelvo un usuario o false
    public function getClienteSiguiente(int $id)
    {
        // Si el id es el mayor disponible, devolvemos el cliente actual
        $stmt_max_id = $this->dbh->prepare("SELECT MAX(id) FROM clientes");
        $stmt_max_id->execute();
        $max_id = $stmt_max_id->fetchColumn();

        // Si el id actual es el máximo (no hay cliente siguiente), devolvemos el cliente actual
        if ($id >= $max_id) {
            return $this->getCliente($id);
        }

        // Si hay un cliente siguiente, lo buscamos
        $cli = false;
        $stmt_cli = $this->dbh->prepare("SELECT * FROM clientes WHERE id > :id ORDER BY id ASC LIMIT 1");
        $stmt_cli->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
        $stmt_cli->bindParam(':id', $id);
        if ($stmt_cli->execute()) {
            if ($obj = $stmt_cli->fetch()) {
                $cli = $obj;
            }
        }
        return $cli;
    }

    public function getClienteAnterior(int $id)
    {
        // Si el id es menor o igual a 1, devolvemos el cliente actual para que no salga un error
        if ($id <= 1) {
            return $this->getCliente(1);
        }

        $cli = false;
        $stmt_cli   = $this->dbh->prepare("select * from clientes where id < :id order by id desc limit 1");
        $stmt_cli->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
        $stmt_cli->bindParam(':id', $id);
        if ($stmt_cli->execute()) {
            if ($obj = $stmt_cli->fetch()) {
                $cli = $obj;
            }
        }
        return $cli;
    }



    // UPDATE TODO
    public function modCliente($cli): bool
    {

        $stmt_moduser   = $this->dbh->prepare("update clientes set first_name=:first_name,last_name=:last_name" .
            ",email=:email,gender=:gender, ip_address=:ip_address,telefono=:telefono WHERE id=:id");
        $stmt_moduser->bindValue(':first_name', $cli->first_name);
        $stmt_moduser->bindValue(':last_name', $cli->last_name);
        $stmt_moduser->bindValue(':email', $cli->email);
        $stmt_moduser->bindValue(':gender', $cli->gender);
        $stmt_moduser->bindValue(':ip_address', $cli->ip_address);
        $stmt_moduser->bindValue(':telefono', $cli->telefono);
        $stmt_moduser->bindValue(':id', $cli->id);

        $stmt_moduser->execute();
        $resu = ($stmt_moduser->rowCount() == 1);
        return $resu;
    }


    //INSERT 
    public function addCliente($cli): bool
    {

        // El id se define automáticamente por autoincremento.
        $stmt_crearcli  = $this->dbh->prepare(
            "INSERT INTO `clientes`( `first_name`, `last_name`, `email`, `gender`, `ip_address`, `telefono`)" .
                "Values(?,?,?,?,?,?)"
        );
        $stmt_crearcli->bindValue(1, $cli->first_name);
        $stmt_crearcli->bindValue(2, $cli->last_name);
        $stmt_crearcli->bindValue(3, $cli->email);
        $stmt_crearcli->bindValue(4, $cli->gender);
        $stmt_crearcli->bindValue(5, $cli->ip_address);
        $stmt_crearcli->bindValue(6, $cli->telefono);
        $stmt_crearcli->execute();
        $resu = ($stmt_crearcli->rowCount() == 1);
        return $resu;
    }

    // public function addUser() {
    //     $login = "luis";
    //     $password = "luis123";
    //     $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    //     $rol = 0;

    //     $stmt_crearcli  = $this->dbh->prepare("INSERT INTO Usuarios (login, password, rol) VALUES (:login, :password, :rol)");
    //     $stmt_crearcli->bindParam(':login', $login);
    //     $stmt_crearcli->bindParam(':password', $hashed_password);
    //     $stmt_crearcli->bindParam(':rol', $rol);
    //     $stmt_crearcli->execute();
    //     $resu = ($stmt_crearcli->rowCount() == 1);
    //     return $resu;
    // }


    //DELETE 
    public function borrarCliente(int $id): bool
    {


        $stmt_borcli   = $this->dbh->prepare("delete from clientes where id =:id");

        $stmt_borcli->bindValue(':id', $id);
        $stmt_borcli->execute();
        $resu = ($stmt_borcli->rowCount() == 1);
        return $resu;
    }

    public function getCorreo($correo)
    {
        $stmt_verifcorreocli   = $this->dbh->prepare("select * from clientes where email = :email");

        $stmt_verifcorreocli->bindValue(':email', $correo);
        $stmt_verifcorreocli->execute();
        $resu = ($stmt_verifcorreocli->rowCount() == 1);
        return $resu;
    }

    public function getIP($ip)
    {
        $stmt_verifipcli   = $this->dbh->prepare("select * from clientes where ip_address = :ip");

        $stmt_verifipcli->bindValue(':ip', $ip);
        $stmt_verifipcli->execute();
        $resu = ($stmt_verifipcli->rowCount() == 1);
        return $resu;
    }

    public function getTelefono($telefono)
    {
        $stmt_veriftelefonocli   = $this->dbh->prepare("select * from clientes where telefono = :telefono");
        $stmt_veriftelefonocli->bindValue(':telefono', $telefono);
        $stmt_veriftelefonocli->execute();
        $resu = ($stmt_veriftelefonocli->rowCount() == 1);
        return $resu;
    }

    public function getUltimoId()
    {
        $cli = false;
        $stmt_idultimocli   = $this->dbh->prepare("select id from clientes order by id desc limit 1");
        if ($stmt_idultimocli->execute()) {
            $result = $stmt_idultimocli->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return (int)$result['id'];
            }
        }
        return null;
    }

    // public function getClientesPorFiltrado($primero, $cuantos, $filtro)
    // {
    //     $tuser = [];

    //     // Si no hay ordenación establecida, por defecto ordenar por ID
    //     if (!$filtro) {
    //         $filtro = 'ORDER BY id ASC';
    //     }

    //     $stmt_clientes = $this->dbh->prepare("select * from Clientes " . $filtro . " limit $primero, $cuantos");

    //     // Si falla, termina el programa
    //     $stmt_clientes->setFetchMode(PDO::FETCH_CLASS, 'Cliente');

    //     if ($stmt_clientes->execute()) {
    //         while ($user = $stmt_clientes->fetch()) {
    //             $tuser[] = $user;
    //         }
    //     }
    //     // Devuelvo el array de objetos
    //     return $tuser;
    // }


    public function getUsuarioPorLogin(String $login)
    {
        $stmt_veriflogin = $this->dbh->prepare("select * from usuarios where login = :login");
        $stmt_veriflogin->bindParam(':login', $login);
        $stmt_veriflogin->execute();
        return $stmt_veriflogin->fetchObject('Usuario');
    }


    // Evito que se pueda clonar el objeto. (SINGLETON)
    public function __clone()
    {
        trigger_error('La clonación no permitida', E_USER_ERROR);
    }
}
