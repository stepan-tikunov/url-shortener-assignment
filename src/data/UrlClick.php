<?php

namespace assignment\data;

use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Exception\GeoIp2Exception;
use MaxMind\Db\Reader\InvalidDatabaseException;

class UrlClick
{
    private int | null $id = null;
    private \DateTime $date;
    private string $ip;
    private Url $url;

    public function __construct(\DateTime $date, string $ip, Url $url) {
        $this->date = $date;
        $this->ip = $ip;
        $this->url = $url;
    }

    public function getId(): int | null {
        return $this->id;
    }

    public function getDate(): \DateTime {
        return $this->date;
    }

    public function getIp(): string {
        return $this->ip;
    }

    public function getUrl(): Url {
        return $this->url;
    }

    private function insert(): int {
        $db = UrlDatabase::getInstance();

        $statement = $db->prepare("INSERT INTO clicks (date, ip, url_id) VALUES (?, ?, ?);");

        $statement->bindValue(1, $this->getDate()->getTimestamp(), SQLITE3_INTEGER);
        $statement->bindValue(2, $this->getIp());
        $statement->bindValue(3, $this->getUrl()->getId(), SQLITE3_INTEGER);

        $statement->execute();

        return $db->lastInsertRowID();
    }

    private function update(): void {
        $db = UrlDatabase::getInstance();

        $statement = $db->prepare("UPDATE clicks SET date = ?, ip = ?, url_id = ? WHERE id = ?");

        $statement->bindValue(1, $this->getDate()->getTimestamp(), SQLITE3_INTEGER);
        $statement->bindValue(2, $this->getIp());
        $statement->bindValue(3, $this->getUrl()->getId(), SQLITE3_INTEGER);
        $statement->bindValue(4, $this->id, SQLITE3_INTEGER);

        $statement->execute();
    }

    public function save(): int {
        if ($this->id === null) {
            $this->id = $this->insert();
        } else {
            $this->update();
        }

        return $this->id;
    }

    public function toDto(): array {
        $db = GeoDatabase::getInstance();
        $geo = [];

        try {
            $entry = $db->city($this->ip);

            $geo["latitude"] = $entry->location->latitude;
            $geo["longitude"] = $entry->location->longitude;
            $geo["city"] = $entry->city->name;
            $geo["country"] = $entry->country->name;
        } catch (AddressNotFoundException | InvalidDatabaseException $exception) {}

        return [
            "date" => $this->date->getTimestamp(),
            "ip" => $this->ip,
            "geo" => $geo
        ];
    }

    /**
     * @return UrlClick[]
     */
    public static function findClicksFor(Url $url): array {
        $db = UrlDatabase::getInstance();

        $statement = $db->prepare("SELECT * FROM clicks WHERE url_id = ?;");
        $statement->bindValue(1, $url->getId(), SQLITE3_INTEGER);

        $result = $statement->execute();

        $clicks = [];

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $row["url"] = $url;
            $clicks[] = UrlClick::fromResultRow($row);
        }

        return $clicks;
    }

    public static function fromResultRow(array $row): UrlClick {
        $date = new \DateTime();
        $date->setTimestamp($row["date"]);

        $click = new UrlClick($date, $row["ip"], $row["url"]);
        $click->id = $row["id"];

        return $click;
    }
}