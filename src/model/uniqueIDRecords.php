<?php

/**
 * Class UniqueIdRecord
 * Represents a record of activities (donation, request, fulfilled) for a donor.
 */
class UniqueIdRecord
{
    // Attributes based on the class diagram
    private ?int $record_number;
    private string $record_type; // ENUM: 'donation', 'request', 'fulfilled'
    private string $blood_group; // ENUM: 'a', 'ap', 'b', 'bp', 'ab', 'abp', 'o', 'op'
    private string $note;
    private int $bank_id;
    private string $date;        // Date of the record (e.g., YYYY-MM-DD)

    /**
     * Constructor for the UniqueIdRecord class.
     *
     * @param int $record_number Unique identifier for the record.
     * @param string $record_type Type of record ('donation', 'request', 'fulfilled').
     * @param string $blood_group Blood group associated with the record.
     * @param string $note Additional note for the record.
     * @param int $bank_id Foreign key referencing the blood bank.
     * @param string $date Date of the record.
     */
    public function __construct(
        ?int $record_number,
        string $record_type,
        string $blood_group,
        string $note,
        int $bank_id,
        string $date
    ) {
        $this->record_number = $record_number;
        $this->record_type = $record_type;
        $this->blood_group = $blood_group;
        $this->note = $note;
        $this->bank_id = $bank_id;
        $this->date = $date;
    }

    // --- Getters ---
    public function getRecordNumber(): int
    {
        return $this->record_number;
    }

    public function getRecordType(): string
    {
        return $this->record_type;
    }

    public function getBloodGroup(): string
    {
        return $this->blood_group;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function getBankId(): int
    {
        return $this->bank_id;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    // --- Setters ---
    public function setRecordType(string $record_type): void
    {
        $this->record_type = $record_type;
    }

    public function setBloodGroup(string $blood_group): void
    {
        $this->blood_group = $blood_group;
    }

    public function setNote(string $note): void
    {
        $this->note = $note;
    }

    public function setBankId(int $bank_id): void
    {
        $this->bank_id = $bank_id;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }
}
