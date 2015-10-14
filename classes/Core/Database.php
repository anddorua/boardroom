<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 10.10.15
 * Time: 22:32
 */

namespace Core;

class Database
{
    private $dbh = null;
    private $dsn = null;
    private $user = null;
    private $password = null;
    private $transactionCounter = 0;
    private $table_name_to_test;
    private $database_creation_list;

    public function __construct($dbh, $user, $password, $table_name_to_test, array $database_creation_list)
    {
        $this->dsn = $dbh;
        $this->user = $user;
        $this->password = $password;
        $this->table_name_to_test = $table_name_to_test;
        $this->database_creation_list = $database_creation_list;
    }

    private function sureDbhSet()
    {
        if (!$this->dbh) {
            if ($this->dsn) {
                $this->dbh = new \PDO($this->dsn, $this->user, $this->password);
                if (!$this->tableExists($this->dbh, $this->table_name_to_test)) {
                    // create all tables
                    foreach($this->database_creation_list as $statement) {
                        $this->dbh->exec($statement);
                    }
                }
            } else {
                throw new ENoDatabaseSet("No PDO instance set.");
            }
        }
    }

    private function tableExists(\PDO $pdo, $table_name)
    {
        try {
            $result = $pdo->query("SELECT 1 FROM $table_name LIMIT 1");
        } catch (\Exception $e) {
            return FALSE;
        }
        return $result !== FALSE;
    }

    public function query($statement, $data)
    {
        $this->sureDbhSet();
        $this->dbh->prepare($statement)->execute($data);

    }
    public function fetchAllAssoc($statement, $data)
    {
        $this->sureDbhSet();
        $st = $this->dbh->prepare($statement);
        $st->execute($data);
        return $st->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function fetchFirstAssoc($statement, $data)
    {
        $this->sureDbhSet();
        $st = $this->dbh->prepare($statement);
        $st->execute($data);
        return $st->fetch(\PDO::FETCH_ASSOC);
    }
    public function exec($statement, $data)
    {
        //error_log("\nsql:" . print_r($statement, true), 3, "my_errors.txt");
        //error_log("\ndata:" . print_r($data, true), 3, "my_errors.txt");
        $this->sureDbhSet();
        $st = $this->dbh->prepare($statement);
        return $st->execute($data);
    }

    public function getLastError() {
        if ($this->dbh) {
            return $this->dbh->errorInfo();
        } else {
            return false;
        }
    }

    public function beginTransaction()
    {
        $this->sureDbhSet();
        if ($this->transactionCounter == 0) {
            $this->transactionCounter++;
            return $this->dbh->beginTransaction();
        } else {
            return ++$this->transactionCounter > 0;
        }
    }

    public function commit()
    {
        if ($this->transactionCounter > 0) {
            $this->transactionCounter--;
            if ($this->transactionCounter == 0) {
                return $this->dbh->commit();
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function rollback()
    {
        if ($this->transactionCounter > 0) {
            $this->transactionCounter = 0;
            return $this->dbh->rollBack();
        } else {
            return false;
        }
    }

}