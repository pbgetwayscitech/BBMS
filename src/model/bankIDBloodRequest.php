<?php

/**
 * Class BankIdRequest
 * Represents a blood request made to a blood bank.
 */
class BankIdBloodRequest
{
    // Attributes based on the class diagram
    private ?int $request_id;
    private string $requested_by;        // Name of entity requesting (e.g., 'donor')
    private int $requested_by_id;        // ID of entity requesting (e.g., donor ID)
    private string $requested_blood_group; // ENUM: 'a', 'ap', 'b', 'bp', 'ab', 'abp', 'o', 'op'
    private int $requested_for;         // Patient ID or similar identifier
    private int $bank_id;               // Foreign key referencing the blood bank
    private string $requested_on;       // Date of request (e.g., YYYY-MM-DD)
    private string $status;              // ENUM: 'requested', 'fulfilled', 'discarded'
    private string $note;

    /**
     * Constructor for the BankIdRequest class.
     *
     * @param int $request_id Unique identifier for the request.
     * @param string $requested_by Name of the entity making the request.
     * @param int $requested_by_id ID of the entity making the request.
     * @param string $requested_blood_group Blood group requested.
     * @param int $requested_for ID of the patient for whom blood is requested.
     * @param int $bank_id Foreign key referencing the blood bank.
     * @param string $requested_on Date when the request was made.
     * @param string $status Current status of the request ('requested', 'fulfilled', 'discarded').
     * @param string $note Additional notes about the request.
     */
    public function __construct(
        ?int $request_id,
        string $requested_by,
        int $requested_by_id,
        string $requested_blood_group,
        int $requested_for,
        int $bank_id,
        string $requested_on,
        string $status,
        string $note
    ) {
        $this->request_id = $request_id;
        $this->requested_by = $requested_by;
        $this->requested_by_id = $requested_by_id;
        $this->requested_blood_group = $requested_blood_group;
        $this->requested_for = $requested_for;
        $this->bank_id = $bank_id;
        $this->requested_on = $requested_on;
        $this->status = $status;
        $this->note = $note;
    }

    // --- Getters ---
    public function getRequestId(): int
    {
        return $this->request_id;
    }

    public function getRequestedBy(): string
    {
        return $this->requested_by;
    }

    public function getRequestedById(): int
    {
        return $this->requested_by_id;
    }

    public function getRequestedBloodGroup(): string
    {
        return $this->requested_blood_group;
    }

    public function getRequestedFor(): int
    {
        return $this->requested_for;
    }

    public function getBankId(): int
    {
        return $this->bank_id;
    }

    public function getRequestedOn(): string
    {
        return $this->requested_on;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    // --- Setters ---
    public function setRequestedBy(string $requested_by): void
    {
        $this->requested_by = $requested_by;
    }

    public function setRequestedById(int $requested_by_id): void
    {
        $this->requested_by_id = $requested_by_id;
    }

    public function setRequestedBloodGroup(string $requested_blood_group): void
    {
        $this->requested_blood_group = $requested_blood_group;
    }

    public function setRequestedFor(int $requested_for): void
    {
        $this->requested_for = $requested_for;
    }

    public function setBankId(int $bank_id): void
    {
        $this->bank_id = $bank_id;
    }

    public function setRequestedOn(string $requested_on): void
    {
        $this->requested_on = $requested_on;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setNote(string $note): void
    {
        $this->note = $note;
    }
}
