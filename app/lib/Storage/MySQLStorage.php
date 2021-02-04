<?php


namespace ParseThisNews\Storage;


use ParseThisNews\Util\Settings;

class MySQLStorage implements iStorage
{
    private const SERVICE_NAME = 'MySQL';

    private \PDO $connection;
    private array $settings;

    public function __construct()
    {
        $this->settings = Settings::getSettings(self::SERVICE_NAME);
        $this->connection = $this->getPreparedConnection();
    }

    private function getPreparedConnection(): \PDO
    {
        $connection = new \PDO(
            sprintf(
                'mysql:host=%s;port=%s;dbname=%s',
                $this->settings['db_host'],
                $this->settings['db_port'],
                $this->settings['db_name']
            ),
            $this->settings['db_user'],
            $this->settings['db_pass']
        );
        $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
        $connection->exec('SET NAMES utf8');
        $connection->exec('SET CHARACTER SET utf8_general_ci');

        return $connection;
    }

    public function findAll(string $entityName): array
    {
        $statement = $this->connection->query(sprintf('SELECT * FROM %s', $entityName));
        return $statement->fetchAll();
    }

    public function get(string $entityName, array $filer)
    {
        if (empty($filer)) {
            throw new \InvalidArgumentException('Empty filter');
        }

        $query = sprintf('SELECT * FROM %s', $entityName);
        $markers = implode(
            ' AND ',
            array_map(
                static function ($name) {
                    return $name . ' = ?';
                },
                array_keys($filer)
            )
        );

        $query .= ' WHERE ' . $markers;

        $statement = $this->connection->prepare($query);
        $result = $statement->execute(array_values($filer));
        if (!$result) {
            return null;
        }

        return $statement->fetch();
    }

    public function create(string $entityName, array $data): bool
    {
        if (empty($data)) {
            throw new \InvalidArgumentException('Empty data');
        }

        $query = sprintf('INSERT INTO %s', $entityName);
        $query .= ' (' . implode(', ', array_keys($data)) . ')';

        $markers = implode(
            ', ',
            array_map(
                static function () {
                    return '?';
                },
                array_keys($data)
            )
        );

        $query .= sprintf(' VALUES (%s)', $markers);

        $statement = $this->connection->prepare($query);

        return $statement->execute(array_values($data));
    }

    public function update(string $entityName, $id, array $data)
    {
        // TODO: Implement update() method.
    }

    public function delete(string $entityName, $id)
    {
        // TODO: Implement delete() method.
    }

    public function deleteAll(string $entityName): bool
    {
        $query = 'SET FOREIGN_KEY_CHECKS = 0;  TRUNCATE TABLE %s; SET FOREIGN_KEY_CHECKS = 1;';
        $statement = $this->connection->query(sprintf($query, $entityName));
        return !empty($statement);
    }
}