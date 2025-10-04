<?php

/**
 * Class BloodBank
 * Represents a blood bank in the Blood Bank Management System.
 */
class BloodBank
{
    // Attributes based on the class diagram and table details
    private ?int $bank_id;
    private string $bank_name;
    private int $pincode;
    private int $state_id; // State ID
    private string $bank_owner;
    private string $address;
    private int $phone_number;
    private string $bank_email;
    private string $password_hash;
    // Blood stock quantities (assuming these are quantities of each blood type)
    private int $a_stock;
    private int $ap_stock;
    private int $b_stock;
    private int $bp_stock;
    private int $ab_stock;
    private int $abp_stock;
    private int $o_stock;
    private int $op_stock;

    /**
     * Constructor for the BloodBank class.
     *
     * @param int $bank_id Unique identifier for the blood bank.
     * @param string $bank_name Name of the blood bank.
     * @param int $pincode Pincode of the blood bank's location.
     * @param int $state_id State ID of the blood bank's location.
     * @param string $bank_owner Name of the blood bank owner/director.
     * @param string $address Address of the blood bank.
     * @param int $phone_number Phone number of the blood bank.
     * @param string $bank_email Email address of the blood bank.
     * @param string $password_hash Hash of Password.
     * @param int $a_stock Quantity of A blood type.
     * @param int $ap_stock Quantity of A+ blood type.
     * @param int $b_stock Quantity of B blood type.
     * @param int $bp_stock Quantity of B+ blood type.
     * @param int $ab_stock Quantity of AB blood type.
     * @param int $abp_stock Quantity of AB+ blood type.
     * @param int $o_stock Quantity of O blood type.
     * @param int $op_stock Quantity of O+ blood type.
     */
    public function __construct(
        ?int $bank_id,
        string $bank_name,
        int $pincode,
        int $state_id,
        string $bank_owner,
        string $address,
        int $phone_number,
        string $bank_email,
        string $password_hash,
        int $a_stock,
        int $ap_stock,
        int $b_stock,
        int $bp_stock,
        int $ab_stock,
        int $abp_stock,
        int $o_stock,
        int $op_stock
    ) {
        $this->bank_id = $bank_id;
        $this->bank_name = $bank_name;
        $this->pincode = $pincode;
        $this->state_id = $state_id;
        $this->bank_owner = $bank_owner;
        $this->address = $address;
        $this->phone_number = $phone_number;
        $this->bank_email = $bank_email;
        $this->password_hash = $password_hash;
        $this->a_stock = $a_stock;
        $this->ap_stock = $ap_stock;
        $this->b_stock = $b_stock;
        $this->bp_stock = $bp_stock;
        $this->ab_stock = $ab_stock;
        $this->abp_stock = $abp_stock;
        $this->o_stock = $o_stock;
        $this->op_stock = $op_stock;
    }

    // --- General Getters (non-stock related) ---
    public function getBankId(): int
    {
        return $this->bank_id;
    }

    public function getBankName(): string
    {
        return $this->bank_name;
    }

    public function getPincode(): int
    {
        return $this->pincode;
    }

    public function getStateId(): int
    {
        return $this->state_id;
    }

    public function getBankOwner(): string
    {
        return $this->bank_owner;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getPhoneNumber(): int
    {
        return $this->phone_number;
    }

    public function getBankEmail(): string
    {
        return $this->bank_email;
    }

    public function getPasswordHash(): string
    {
        return $this->password_hash;
    }

    // --- Individual Blood Stock Getters ---
    public function getAStock(): int
    {
        return $this->a_stock;
    }

    public function getApStock(): int
    {
        return $this->ap_stock;
    }

    public function getBStock(): int
    {
        return $this->b_stock;
    }

    public function getBpStock(): int
    {
        return $this->bp_stock;
    }

    public function getAbStock(): int
    {
        return $this->ab_stock;
    }

    public function getAbpStock(): int
    {
        return $this->abp_stock;
    }

    public function getOStock(): int
    {
        return $this->o_stock;
    }

    public function getOpStock(): int
    {
        return $this->op_stock;
    }

    // --- General Setters (non-stock related) ---
    public function setBankName(string $bank_name): void
    {
        $this->bank_name = $bank_name;
    }

    public function setPincode(int $pincode): void
    {
        $this->pincode = $pincode;
    }

    public function setStateId(int $state_id): void
    {
        $this->state_id = $state_id;
    }

    public function setBankOwner(string $bank_owner): void
    {
        $this->bank_owner = $bank_owner;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function setPhoneNumber(int $phone_number): void
    {
        $this->phone_number = $phone_number;
    }

    public function setBankEmail(string $bank_email): void
    {
        $this->bank_email = $bank_email;
    }

    public function setPasswordHash(string $password_hash): void
    {
        $this->password_hash = $password_hash;
    }

    // --- Individual Blood Stock Setters ---
    public function setAStock(int $quantity): void
    {
        $this->a_stock = $quantity;
    }

    public function setApStock(int $quantity): void
    {
        $this->ap_stock = $quantity;
    }

    public function setBStock(int $quantity): void
    {
        $this->b_stock = $quantity;
    }

    public function setBpStock(int $quantity): void
    {
        $this->bp_stock = $quantity;
    }

    public function setAbStock(int $quantity): void
    {
        $this->ab_stock = $quantity;
    }

    public function setAbpStock(int $quantity): void
    {
        $this->abp_stock = $quantity;
    }

    public function setOStock(int $quantity): void
    {
        $this->o_stock = $quantity;
    }

    public function setOpStock(int $quantity): void
    {
        $this->op_stock = $quantity;
    }
}
