<?php


namespace ParseThisNews\Storage;


use ParseThisNews\Util\Settings;

class PSQLStorage implements iStorage
{
    private const SERVICE_NAME = 'PSQL';

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
                'pgsql:host=%s;port=%s;dbname=%s',
                $this->settings['db_host'],
                $this->settings['db_port'],
                $this->settings['db_name']
            ),
            $this->settings['db_user'],
            $this->settings['db_pass']
        );
        $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        //$connection->exec('SET NAMES utf8');
        //$connection->exec('SET CHARACTER SET utf8_general_ci');

        return $connection;
    }

    public function get(string $entityName, ?array $filter = []): ?array
    {
        $query = sprintf('SELECT * FROM %s', $entityName);
        $filterValues = null;

        if (!empty($filter)) {
            $markers = implode(
                ' AND ',
                array_map(
                    static function ($name) {
                        return $name . ' = ?';
                    },
                    array_keys($filter)
                )
            );

            $query .= ' WHERE ' . $markers;
            $filterValues = array_values($filter);
        }

        $statement = $this->connection->prepare($query);
        $result = $statement->execute($filterValues);
        if (!$result) {
            return null;
        }

        return $statement->fetchAll();
    }

    public function create(string $entityName, array $data): bool
    {
        if (empty($data)) {
            throw new \InvalidArgumentException('Empty data');
        }
        $filteredData = array_filter($data);

        $query = sprintf('INSERT INTO %s', $entityName);
        $query .= ' (' . implode(', ', array_keys($filteredData)) . ')';

        $markers = implode(
            ', ',
            array_map(
                static function () {
                    return '?';
                },
                array_keys($filteredData)
            )
        );

        $query .= sprintf(' VALUES (%s)', $markers);

        $statement = $this->connection->prepare($query);

        return $statement->execute(array_values($filteredData));
    }

    public function update(string $entityName, $id, array $data)
    {
        // TODO: Implement update() method.
    }

    public function delete(string $entityName, ?array $condition = []): bool
    {
        if (empty($condition)) {
            $query = 'TRUNCATE TABLE %s;';
            $query = sprintf($query, $entityName);

            $statement = $this->connection->query($query);
            return !empty($statement);
        }

        $query = sprintf('DELETE FROM %s', $entityName);

        $markers = implode(
            ' AND ',
            array_map(
                static function ($name) {
                    return $name . ' = ?';
                },
                array_keys($condition)
            )
        );

        $query .= ' WHERE ' . $markers;
        $conditionValues = array_values($condition);

        $statement = $this->connection->prepare($query);
        $result = $statement->execute($conditionValues);

        return !empty($result);
    }
}
