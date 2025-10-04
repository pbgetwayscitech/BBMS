<?php

/**
 * Class BankIdBloodStock
 * Represents a specific unit of blood stock held by a blood bank.
 */
class BankIdBloodStock
{
    // Attributes based on the class diagram and table details
    private ?int $stock_id;
    private int $donor_id;
    private int $bank_id;
    private string $donation_date;  // Date of donation (YYYY-MM-DD)
    private string $blood_group;    // ENUM: 'a', 'ap', 'b', 'bp', 'ab', 'abp', 'o', 'op'
    private string $expiary_date;   // Expiry date of the blood unit (YYYY-MM-DD)
    private string $note;
    private string $stock_status;   // ENUM: 'preserved', 'utilised', 'discarded'
    private string $stock_status_date; // Date when stock status was last updated (YYYY-MM-DD)

    /**
     * Constructor for the BankIdBloodStock class.
     *
     * @param int $stock_id Unique identifier for the blood stock unit.
     * @param int $donor_id Foreign key to the GenDonor.
     * @param int $bank_id Foreign key to the BloodBank.
     * @param string $donation_date Date of blood donation.
     * @param string $blood_group Blood group of the unit.
     * @param string $expiary_date Expiration date of the blood unit.
     * @param string $note Additional notes about the blood unit.
     * @param string $stock_status Current status ('preserved', 'utilised', 'discarded').
     * @param string $stock_status_date Date when the stock status was last updated.
     */
    public function __construct(
        ?int $stock_id,
        int $donor_id,
        int $bank_id,
        string $donation_date,
        string $blood_group,
        string $expiary_date,
        string $note,
        string $stock_status,
        string $stock_status_date
    ) {
        $this->stock_id = $stock_id;
        $this->donor_id = $donor_id;
        $this->bank_id = $bank_id;
        $this->donation_date = $donation_date;
        $this->blood_group = $blood_group;
        $this->expiary_date = $expiary_date;
        $this->note = $note;
        $this->stock_status = $stock_status;
        $this->stock_status_date = $stock_status_date;
    }

    // --- Getters ---
    public function getStockId(): int
    {
        return $this->stock_id;
    }

    public function getDonorId(): int
    {
        return $this->donor_id;
    }

    public function getBankId(): int
    {
        return $this->bank_id;
    }

    public function getDonationDate(): string
    {
        return $this->donation_date;
    }

    public function getBloodGroup(): string
    {
        return $this->blood_group;
    }

    public function getExpiaryDate(): string
    {
        return $this->expiary_date;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function getStockStatus(): string
    {
        return $this->stock_status;
    }

    public function getStockStatusDate(): string
    {
        return $this->stock_status_date;
    }

    // --- Setters ---
    public function setDonationDate(string $donation_date): void
    {
        $this->donation_date = $donation_date;
    }

    public function setBloodGroup(string $blood_group): void
    {
        $this->blood_group = $blood_group;
    }

    public function setExpiaryDate(string $expiary_date): void
    {
        $this->expiary_date = $expiary_date;
    }

    public function setNote(string $note): void
    {
        $this->note = $note;
    }

    public function setStockStatus(string $stock_status): void
    {
        $this->stock_status = $stock_status;
    }

    public function setStockStatusDate(string $stock_status_date): void
    {
        $this->stock_status_date = $stock_status_date;
    }
}
