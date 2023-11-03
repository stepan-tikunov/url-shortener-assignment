<?php

namespace assignment\data;

use assignment\encode\FormatPreservingEncoder;

class Url
{
    private int | null $id = null;
    private string $url;

    public function __construct(string $url) {
        $this->url = $url;
    }

    public function getId(): int | null {
        return $this->id;
    }

    public function getUrl(): string {
        return $this->url;
    }

    private function insert(): int {
        $db = UrlDatabase::getInstance();

        $statement = $db->prepare("INSERT INTO url (url) VALUES (?);");
        $statement->bindValue(1, $this->url);
        $statement->execute();

        return $db->lastInsertRowID();
    }

    private function update(): void {
        $db = UrlDatabase::getInstance();

        $statement = $db->prepare("UPDATE url SET url = ? WHERE id = ?");
        $statement->bindValue(1, $this->url);
        $statement->bindValue(2, $this->id, SQLITE3_INTEGER);

        $statement->execute();
    }

    public function save(): int {
        if ($this->id == null) {
            $this->id = $this->insert();
        } else {
            $this->update();
        }

        return $this->id;
    }

    public function toDto(): array {
        $encoder = new FormatPreservingEncoder();
        $short = $encoder->encode($this->id);

        return [
            "short" => $short,
            "long" => $this->url,
        ];
    }

    public static function fromResultRow(array $rs): Url {
        $url = new Url($rs["url"]);
        $url->id = $rs["id"];

        return $url;
    }

    public static function get(int $id): Url | null {
        $db = UrlDatabase::getInstance();

        $statement = $db->prepare("SELECT * FROM url WHERE id = ?;");
        $statement->bindValue(1, $id, SQLITE3_INTEGER);

        $result = $statement->execute()->fetchArray(SQLITE3_ASSOC);

        if ($result === false) {
            return null;
        }

        return self::fromResultRow($result);
    }

    /**
     * @return Url[]
     */
    public static function findAll(): array {
        $db = UrlDatabase::getInstance();

        $resultSet = $db->query("SELECT * FROM url;");

        $all = [];

        while ($row = $resultSet->fetchArray(SQLITE3_ASSOC)) {
            $all[] = self::fromResultRow($row);
        }

        return $all;
    }
}